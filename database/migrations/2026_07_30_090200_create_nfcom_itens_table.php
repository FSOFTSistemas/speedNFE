<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfcom_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nfcom_id');
            $table->foreign('nfcom_id')->references('id')->on('nfcoms')->onDelete('cascade');

            // <prod> - serviço de comunicação faturado
            $table->string('cProd');
            $table->string('xProd');
            $table->string('cClass');
            $table->string('cfop')->nullable();
            $table->string('uMed');
            $table->decimal('qFaturada', 15, 4);
            $table->decimal('vItem', 15, 4);
            $table->decimal('vDesc', 15, 2)->default(0);
            $table->decimal('vOutro', 15, 2)->default(0);
            $table->decimal('vProd', 15, 2);

            // <ICMS00>
            $table->string('icms_cst')->nullable();
            $table->decimal('icms_vBC', 15, 2)->nullable();
            $table->decimal('icms_pICMS', 5, 2)->nullable();
            $table->decimal('icms_vICMS', 15, 2)->nullable();
            $table->decimal('icms_pFCP', 5, 2)->nullable();
            $table->decimal('icms_vFCP', 15, 2)->nullable();

            // <PIS>
            $table->string('pis_cst')->nullable();
            $table->decimal('pis_vBC', 15, 2)->nullable();
            $table->decimal('pis_pPIS', 7, 4)->nullable();
            $table->decimal('pis_vPIS', 15, 2)->nullable();

            // <COFINS>
            $table->string('cofins_cst')->nullable();
            $table->decimal('cofins_vBC', 15, 2)->nullable();
            $table->decimal('cofins_pCOFINS', 7, 4)->nullable();
            $table->decimal('cofins_vCOFINS', 15, 2)->nullable();

            // <FUST>
            $table->decimal('fust_vBC', 15, 2)->nullable();
            $table->decimal('fust_pFUST', 7, 4)->nullable();
            $table->decimal('fust_vFUST', 15, 2)->nullable();

            // <FUNTTEL>
            $table->decimal('funttel_vBC', 15, 2)->nullable();
            $table->decimal('funttel_pFUNTTEL', 7, 4)->nullable();
            $table->decimal('funttel_vFUNTTEL', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfcom_itens');
    }
};
