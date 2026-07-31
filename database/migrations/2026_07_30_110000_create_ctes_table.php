<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ctes', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->timestamp('data');
            $table->integer('serie');
            $table->string('chave')->nullable();
            $table->string('situacao');
            $table->string('nProtocolo')->nullable();

            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->unsignedBigInteger('remetente_id');
            $table->foreign('remetente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->unsignedBigInteger('destinatario_id');
            $table->foreign('destinatario_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->unsignedTinyInteger('toma');

            $table->unsignedBigInteger('veiculo_id');
            $table->foreign('veiculo_id')->references('id')->on('veiculos')->onDelete('cascade');
            $table->unsignedBigInteger('motorista_id')->nullable();
            $table->foreign('motorista_id')->references('id')->on('motoristas')->onDelete('set null');

            // <ide> - percurso da prestação
            $table->string('cfop');
            $table->string('uf_inicio');
            $table->string('mun_ini_codigo');
            $table->string('mun_ini_nome');
            $table->string('uf_fim');
            $table->string('mun_fim_codigo');
            $table->string('mun_fim_nome');

            // <infCarga>
            $table->string('xProd');
            $table->decimal('qCarga', 15, 4)->default(0);
            $table->decimal('vCarga', 15, 2)->default(0);

            // <vPrest>
            $table->decimal('vTPrest', 15, 2)->default(0);
            $table->decimal('vRec', 15, 2)->default(0);

            // <ICMS00> - aliquota informada na emissao, CST fixo em 00 no Service
            $table->decimal('picms', 5, 2)->default(0);

            $table->string('info_fisco')->nullable();
            $table->string('info_contribuinte')->nullable();

            $table->longText('xml')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ctes');
    }
};
