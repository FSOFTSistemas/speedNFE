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
            $table->float('precocusto');
            $table->float('precovenda');
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
