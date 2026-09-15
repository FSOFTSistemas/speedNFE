<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');

            $table->string('codigo');
            $table->string('descricao');
            $table->string('cClass');
            $table->string('cfop')->nullable();
            $table->string('uMed');
            $table->decimal('valor', 15, 4);

            // defaults de tributo - regime normal
            $table->string('icms_cst')->nullable();
            $table->decimal('icms_pICMS', 5, 2)->nullable();
            $table->decimal('icms_pFCP', 5, 2)->nullable();

            // defaults de tributo - Simples Nacional
            $table->string('icms_orig')->nullable();
            $table->string('icms_csosn')->nullable();

            $table->string('pis_cst')->nullable();
            $table->decimal('pis_pPIS', 7, 4)->nullable();
            $table->string('cofins_cst')->nullable();
            $table->decimal('cofins_pCOFINS', 7, 4)->nullable();
            $table->decimal('fust_pFUST', 7, 4)->nullable();
            $table->decimal('funttel_pFUNTTEL', 7, 4)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
