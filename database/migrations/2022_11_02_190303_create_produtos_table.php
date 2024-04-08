<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('produto');
            $table->string('un');
            $table->double('precocusto');
            $table->double('precovenda');
            $table->string('ncm');
            $table->integer('cfop_interno');
            $table->integer('cfop_externo');
            $table->string('cst_csosn');
            $table->string('cst_pis');
            $table->string('cst_cofins');
            $table->string('cst');
            $table->integer('icms');
            $table->string('pis');
            $table->string('cofins');
            $table->string('ipi');
            $table->integer('tpProd')->nullable();
            $table->string('tpVeic')->nullable();
            $table->string('chassiVeic')->nullable();
            $table->string('cenavanVeic')->nullable();
            $table->integer('anoFabVeic')->nullable();
            $table->integer('anoModVeic')->nullable();
            $table->float('pesoLVeic')->nullable();
            $table->float('pesoBVeic')->nullable();
            $table->string('distVeic')->nullable();
            $table->string('combVeic')->nullable();
            $table->string('nMotorVeic')->nullable();
            $table->string('cvVeic')->nullable();
            $table->string('cm3Veic')->nullable();
            $table->string('serieVeic')->nullable();
            $table->string('tpPVeic')->nullable();
            $table->string('corVeic')->nullable();
            $table->string('cCorVeic')->nullable();
            $table->string('cCorMontVeic')->nullable();
            $table->string('cMarcaVeic')->nullable();
            $table->integer('condVeic')->nullable();
            $table->string('espVeic')->nullable();
            $table->string('vinVeic')->nullable();
            $table->string('lotVeic')->nullable();
            $table->integer('restriVeic')->nullable();
            $table->integer('cargaVeic')->nullable();
            $table->string('operVeic')->nullable();
            $table->string('renavanVeic', 30)->nullable();
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos');
    }
};
