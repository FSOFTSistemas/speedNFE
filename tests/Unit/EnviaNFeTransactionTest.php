<?php

namespace Tests\Unit;

use App\Http\Controllers\Traits\EnviaNFe;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FluxoDeCaixaService;
use App\Services\NFeService;
use App\Services\PedidosService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EnviaNFeTransactionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function tearDown(): void
    {
        DB::clearResolvedInstance('db');
        parent::tearDown();
    }

    /** @dataProvider resultadosSefaz */
    public function test_emissao_confirma_a_transacao_antes_de_retornar(array $resposta, string $estado, int $status, string $tipo): void
    {
        $database = Mockery::mock();
        DB::swap($database);
        // Duas aberturas e apenas um commit deixam as alterações sem persistir.
        $database->shouldReceive('beginTransaction')->once()->ordered();
        $database->shouldReceive('commit')->once()->ordered();

        $pedido = Mockery::mock(Pedido::class)->makePartial();
        $pedido->forceFill([
            'id' => 10, 'empresa_id' => 1, 'estado' => 'Pendente',
            'tpNF' => 1, 'total' => 100, 'data' => '2026-09-23',
        ]);
        $pedido->setRelation('itens', collect([(object) ['produto_id' => 5, 'qtde' => 2]]));
        $pedido->setRelation('cliente', null);
        $pedido->shouldReceive('save')->once()->andReturnUsing(function () use ($pedido, $estado, $status) {
            self::assertSame($estado, $pedido->estado->value);
            self::assertSame($status, $pedido->status);

            return true;
        });

        $empresa = Mockery::mock(Empresa::class)->makePartial();
        $empresa->forceFill(['id' => 1, 'ultimaNFe' => 40]);
        $pedidos = Mockery::mock(PedidosService::class);
        $pedidos->shouldReceive('buscarPedido')->once()->with(10)->andReturn($pedido);
        $empresas = Mockery::mock(EmpresasService::class);
        $empresas->shouldReceive('buscarEmpresa')->once()->with(1)->andReturn($empresa);
        $estoque = Mockery::mock(EstoquesService::class);
        $caixa = Mockery::mock(FluxoDeCaixaService::class);

        if ($tipo === 'success') {
            $empresa->shouldReceive('update')->once()->with(['ultimaNFe' => 41])->andReturn(true);
            $estoque->shouldReceive('out')->once()->with(5, 2);
            $caixa->shouldReceive('registrarEntradaAutomatica')->once()
                ->with(1, 100, 'Venda NFe #41', '2026-09-23', 'NFe', 10);
        }

        $nfe = Mockery::mock(NFeService::class);
        $nfe->shouldReceive('gerarXml')->once()->with($pedido, $empresa)
            ->andReturn(['xml' => '<NFe/>', 'chave' => str_repeat('1', 44), 'nNf' => 41]);
        $nfe->shouldReceive('sign')->once()->with('<NFe/>')->andReturn('<assinada/>');
        $nfe->shouldReceive('transmitir')->once()->with('<assinada/>', str_repeat('1', 44), 10)
            ->andReturn($resposta);

        $emissor = new class($nfe)
        {
            use EnviaNFe { _enviarNFePeloId as public enviar; }

            private $service;

            public function __construct($service)
            {
                $this->service = $service;
            }

            private function makeNFeService($empresa)
            {
                return $this->service;
            }
        };

        $resultado = $emissor->enviar(10, $pedidos, $empresas, $estoque, $caixa);

        self::assertSame($tipo, $resultado->type);
        if ($tipo === 'success') {
            self::assertSame(str_repeat('1', 44), $pedido->chave);
            self::assertSame(41, $pedido->numero_nfe);
        }
    }

    public static function resultadosSefaz(): array
    {
        return [
            'autorizada' => [['sucesso' => '123456'], 'Autorizado', 1, 'success'],
            'rejeitada' => [['erro' => 'Rejeição de teste'], 'Rejeitado', 3, 'rejeitado_sefaz'],
        ];
    }
}
