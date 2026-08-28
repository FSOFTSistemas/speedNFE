<?php

namespace Tests\Feature;

use App\Enums\PreVendaStatusEnum;
use App\Models\User;
use App\Services\PreVendaService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PreVendaServiceTest extends TestCase
{
    private PreVendaService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        $this->criarEstrutura();
        $this->service = app(PreVendaService::class);
        $this->user = User::findOrFail(1);
    }

    public function test_cria_pre_venda_recalculando_totais_sem_movimentar_estoque(): void
    {
        $preVenda = $this->service->criarApi([
            'empresa_id' => 1,
            'cliente_id' => 1,
            'data' => '2026-08-28',
            'desconto' => 3,
            'acrescimo' => 2,
            'itens' => [[
                'produto_id' => 1,
                'quantidade' => 2,
                'valor_unitario' => 10,
                'desconto' => 1,
                'acrescimo' => 0,
            ]],
        ], $this->user);

        $this->assertSame('20.00', $preVenda->subtotal);
        $this->assertSame('18.00', $preVenda->total);
        $this->assertSame('Cliente teste', $preVenda->cliente_nome);
        $this->assertSame('Produto teste', $preVenda->itens->first()->descricao);
        $this->assertSame(5.0, (float) DB::table('estoques')->value('estoque_atual'));
    }

    public function test_rejeita_produto_de_outra_empresa(): void
    {
        DB::table('empresas')->insert(['id' => 2, 'fantasia' => 'Outra', 'sequenciaCupom' => 0, 'limNFes' => 100]);
        DB::table('produtos')->insert([
            'id' => 2,
            'empresa_id' => 2,
            'produto' => 'Produto externo',
            'codigo' => 'EXT',
            'precovenda' => 10,
            'un' => 'UN',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->criarApi([
            'empresa_id' => 1,
            'data' => '2026-08-28',
            'itens' => [['produto_id' => 2, 'quantidade' => 1]],
        ], $this->user);
    }

    public function test_converte_em_nfce_uma_unica_vez_e_movimenta_estoque(): void
    {
        $preVenda = $this->service->criarApi([
            'empresa_id' => 1,
            'cliente_id' => 1,
            'data' => '2026-08-28',
            'itens' => [['produto_id' => 1, 'quantidade' => 2]],
            'pagamentos' => [['forma_pag_id' => 1, 'valor' => 20]],
        ], $this->user);

        $convertida = $this->service->converterApi($preVenda->id, ['destino' => 'NFCE'], $this->user);

        $this->assertSame(PreVendaStatusEnum::CONVERTIDA, $convertida->status);
        $this->assertNotNull($convertida->cupom_id);
        $this->assertSame(3.0, (float) DB::table('estoques')->value('estoque_atual'));
        $this->assertSame('DINHEIRO', DB::table('cupom_formas')->value('forma'));

        try {
            $this->service->converterApi($preVenda->id, ['destino' => 'NFCE'], $this->user);
            $this->fail('A segunda conversão deveria ter sido rejeitada.');
        } catch (ValidationException) {
            $this->assertSame(3.0, (float) DB::table('estoques')->value('estoque_atual'));
            $this->assertSame(1, DB::table('cupoms')->count());
        }
    }

    public function test_converte_em_nfe_pendente_sem_movimentar_estoque(): void
    {
        $preVenda = $this->service->criarApi([
            'empresa_id' => 1,
            'cliente_id' => 1,
            'data' => '2026-08-28',
            'desconto' => 1,
            'acrescimo' => 2,
            'itens' => [['produto_id' => 1, 'quantidade' => 2, 'desconto' => 1]],
        ], $this->user);

        $convertida = $this->service->converterApi($preVenda->id, [
            'destino' => 'NFE',
            'cfop' => 1,
            'finalidade' => 1,
            'tipo' => 1,
        ], $this->user);

        $this->assertNotNull($convertida->pedido_id);
        $this->assertSame('Pendente', DB::table('pedidos')->value('estado'));
        $this->assertSame(3.0, (float) DB::table('pedidos')->value('desconto'));
        $this->assertSame(5.0, (float) DB::table('estoques')->value('estoque_atual'));
    }

    private function criarEstrutura(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('fantasia');
            $table->unsignedBigInteger('sequenciaCupom')->default(0);
            $table->integer('limNFes')->default(100);
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cargo');
            $table->unsignedBigInteger('empresa_id');
            $table->timestamps();
        });
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf_cnpj')->nullable();
            $table->unsignedBigInteger('empresa_id');
            $table->timestamps();
        });
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->string('produto');
            $table->string('codigo')->nullable();
            $table->decimal('precovenda', 15, 4);
            $table->string('un')->nullable();
            $table->timestamps();
        });
        Schema::create('forma_pags', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->unsignedBigInteger('empresa_id');
            $table->timestamps();
        });
        Schema::create('cfops', function (Blueprint $table) {
            $table->id();
            $table->string('cfop');
            $table->string('natureza');
            $table->timestamps();
        });
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cliente_id');
            $table->date('data');
            $table->integer('status');
            $table->unsignedBigInteger('empresa_id');
            $table->string('ref_nfe')->nullable();
            $table->integer('tpNF')->nullable();
            $table->integer('finNF')->nullable();
            $table->text('info_complementares')->nullable();
            $table->string('aut_xml')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('desconto', 15, 2);
            $table->decimal('total', 15, 2);
            $table->integer('numero_nfe');
            $table->integer('sequencia_evento');
            $table->string('chave');
            $table->string('estado');
            $table->unsignedBigInteger('cfop');
            $table->timestamps();
        });
        Schema::create('cupoms', function (Blueprint $table) {
            $table->id();
            $table->string('nroCupom');
            $table->timestamp('data');
            $table->string('situacao');
            $table->boolean('gerado_nfce');
            $table->boolean('contingencia');
            $table->decimal('total', 15, 2);
            $table->decimal('desconto', 15, 2);
            $table->decimal('acrescimo', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('troco', 15, 2);
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('empresa_id');
            $table->timestamps();
        });
        Schema::create('item_pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->unsignedBigInteger('produto_id');
            $table->decimal('qtde', 15, 4);
            $table->unsignedBigInteger('empresa_id');
            $table->decimal('desconto', 15, 2);
            $table->decimal('acrescimo', 15, 2);
            $table->decimal('unitario', 15, 4);
            $table->timestamps();
        });
        Schema::create('fatura_pedidos', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 15, 2);
            $table->date('vencimento');
            $table->unsignedBigInteger('venda_id');
            $table->unsignedBigInteger('forma_pag_id');
            $table->unsignedBigInteger('empresa_id');
            $table->timestamps();
        });
        Schema::create('item_cupoms', function (Blueprint $table) {
            $table->id();
            $table->decimal('qtde', 15, 4);
            $table->decimal('unitario', 15, 4);
            $table->decimal('desconto', 15, 2);
            $table->decimal('acrescimo', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->unsignedBigInteger('cupom_id');
            $table->unsignedBigInteger('produto_id');
            $table->timestamps();
        });
        Schema::create('cupom_formas', function (Blueprint $table) {
            $table->id();
            $table->string('forma');
            $table->decimal('valor', 15, 2);
            $table->unsignedBigInteger('cupom_id');
            $table->timestamps();
        });
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();
            $table->decimal('estoque_atual', 15, 4);
            $table->decimal('estoque_anterior', 15, 4);
            $table->decimal('entradas', 15, 4);
            $table->decimal('saidas', 15, 4);
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('produto_id');
            $table->timestamps();
        });

        $migration = require database_path('migrations/2026_08_28_000000_create_pre_vendas_tables.php');
        $migration->up();

        DB::table('empresas')->insert(['id' => 1, 'fantasia' => 'Empresa teste', 'sequenciaCupom' => 0, 'limNFes' => 100]);
        DB::table('users')->insert(['id' => 1, 'name' => 'Master', 'cargo' => 'master', 'empresa_id' => 1]);
        DB::table('clientes')->insert(['id' => 1, 'nome' => 'Cliente teste', 'cpf_cnpj' => '123', 'empresa_id' => 1]);
        DB::table('produtos')->insert(['id' => 1, 'empresa_id' => 1, 'produto' => 'Produto teste', 'codigo' => 'P1', 'precovenda' => 10, 'un' => 'UN']);
        DB::table('forma_pags')->insert(['id' => 1, 'descricao' => 'Dinheiro', 'empresa_id' => 1]);
        DB::table('cfops')->insert(['id' => 1, 'cfop' => '5102', 'natureza' => 'Venda']);
        DB::table('estoques')->insert(['estoque_atual' => 5, 'estoque_anterior' => 5, 'entradas' => 5, 'saidas' => 0, 'empresa_id' => 1, 'produto_id' => 1]);
    }
}
