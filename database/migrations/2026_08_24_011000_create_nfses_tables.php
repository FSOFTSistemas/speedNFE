<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes')->nullOnDelete();
            $table->unsignedBigInteger('servico_id')->nullable();
            $table->foreign('servico_id')->references('id')->on('servicos')->nullOnDelete();

            $table->string('nro')->nullable();
            $table->string('serie')->nullable();
            $table->string('nDPS')->nullable();
            $table->string('serieDPS')->nullable();
            $table->string('chave', 60)->nullable();
            $table->string('codigo_verificacao')->nullable();
            $table->string('nDFSe')->nullable();
            $table->string('nProtocolo')->nullable();
            $table->string('situacao')->default('Rascunho');
            $table->string('cStat', 10)->nullable();
            $table->text('xMotivo')->nullable();

            $table->integer('tpAmb')->nullable();
            $table->integer('tpEmit')->nullable();
            $table->date('data_competencia')->nullable();
            $table->timestamp('data_emissao')->nullable();
            $table->timestamp('data_processamento')->nullable();

            $table->string('cLocEmi', 7)->nullable();
            $table->string('cLocPrestacao', 7)->nullable();
            $table->string('cLocIncid', 7)->nullable();
            $table->string('cTribNac', 6)->nullable();
            $table->string('cTribMun')->nullable();
            $table->string('cNBS')->nullable();
            $table->string('cIndOp', 6)->nullable();
            $table->string('cClassTrib', 6)->nullable();

            $table->decimal('vServ', 15, 2)->default(0);
            $table->decimal('vDescIncond', 15, 2)->default(0);
            $table->decimal('vDescCond', 15, 2)->default(0);
            $table->decimal('vDeducaoReducao', 15, 2)->default(0);
            $table->decimal('vBC', 15, 2)->default(0);
            $table->decimal('pAliq', 7, 4)->nullable();
            $table->decimal('vISSQN', 15, 2)->default(0);
            $table->decimal('vTotalRet', 15, 2)->default(0);
            $table->decimal('vLiq', 15, 2)->default(0);
            $table->decimal('vIBS', 15, 2)->default(0);
            $table->decimal('vCBS', 15, 2)->default(0);
            $table->decimal('vTotNF', 15, 2)->default(0);

            $table->text('discriminacao')->nullable();
            $table->text('informacoes_complementares')->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'situacao']);
            $table->index(['empresa_id', 'data_emissao']);
            $table->unique(['empresa_id', 'chave']);
        });

        Schema::create('nfse_xmls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nfse_id');
            $table->foreign('nfse_id')->references('id')->on('nfses')->onDelete('cascade');
            $table->string('tipo');
            $table->longText('xml');
            $table->timestamps();

            $table->unique(['nfse_id', 'tipo']);
        });

        Schema::create('nfse_eventos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nfse_id');
            $table->foreign('nfse_id')->references('id')->on('nfses')->onDelete('cascade');
            $table->string('tipo_evento');
            $table->string('codigo_evento', 10)->nullable();
            $table->integer('sequencia')->default(1);
            $table->string('situacao')->default('Pendente');
            $table->string('nProtocolo')->nullable();
            $table->string('cStat', 10)->nullable();
            $table->text('xMotivo')->nullable();
            $table->text('justificativa')->nullable();
            $table->timestamp('data_evento')->nullable();
            $table->longText('xml_pedido')->nullable();
            $table->longText('xml_retorno')->nullable();
            $table->timestamps();

            $table->unique(['nfse_id', 'tipo_evento', 'sequencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfse_eventos');
        Schema::dropIfExists('nfse_xmls');
        Schema::dropIfExists('nfses');
    }
};
