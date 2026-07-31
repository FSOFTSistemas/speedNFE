<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfcoms', function (Blueprint $table) {
            $table->id();
            $table->string('nro');
            $table->timestamp('data');
            $table->integer('serie');
            $table->string('chave')->nullable();
            $table->string('situacao');
            $table->string('nProtocolo')->nullable();

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');

            // <assinante> - dados do contrato de telecom
            $table->string('iCodAssinante');
            $table->string('tpAssinante');
            $table->string('tpServUtil');
            $table->string('nContrato')->nullable();

            // <gFat> - faturamento
            $table->string('competFat');
            $table->date('dVencFat')->nullable();
            $table->date('dPerUsoIni')->nullable();
            $table->date('dPerUsoFim')->nullable();
            $table->string('codBarras')->nullable();

            // totais
            $table->decimal('vProd', 15, 2)->default(0);
            $table->decimal('vDesc', 15, 2)->default(0);
            $table->decimal('vNF', 15, 2)->default(0);

            $table->longText('xml')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfcoms');
    }
};
