<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfse_servicos_nacionais', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 6)->unique();
            $table->unsignedSmallInteger('item')->nullable();
            $table->unsignedSmallInteger('subitem')->nullable();
            $table->unsignedSmallInteger('desdobro')->nullable();
            $table->text('descricao');
            $table->string('fonte_versao')->nullable();
            $table->timestamps();
        });

        Schema::create('nfse_nbs', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->text('descricao');
            $table->string('fonte_versao')->nullable();
            $table->timestamps();
        });

        Schema::create('nfse_ind_ops', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 6)->unique();
            $table->string('artigo')->nullable();
            $table->text('tipo_operacao')->nullable();
            $table->text('local_operacao')->nullable();
            $table->text('caracteristica_fornecimento')->nullable();
            $table->string('grupo')->nullable();
            $table->string('subgrupo')->nullable();
            $table->text('local_fornecimento')->nullable();
            $table->text('campo_layout')->nullable();
            $table->string('fonte_versao')->nullable();
            $table->timestamps();
        });

        Schema::create('nfse_regras_incidencia', function (Blueprint $table) {
            $table->id();
            $table->string('cTribNac', 6)->unique();
            $table->text('descricao')->nullable();
            $table->boolean('li_estabelecimento_prestador')->default(false);
            $table->boolean('li_local_prestacao')->default(false);
            $table->boolean('li_estabelecimento_tomador')->default(false);
            $table->boolean('li_estabelecimento_emitente')->default(false);
            $table->boolean('exige_info_servico')->default(false);
            $table->text('informacoes_complementares')->nullable();
            $table->string('fonte_versao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfse_regras_incidencia');
        Schema::dropIfExists('nfse_ind_ops');
        Schema::dropIfExists('nfse_nbs');
        Schema::dropIfExists('nfse_servicos_nacionais');
    }
};
