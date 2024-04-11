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
        Schema::create('itens_entradas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrada_id');
            $table->foreign('entrada_id')->references('id')->on('entradas')->onDelete('cascade');
            $table->string('produto');
            $table->string('codbarra')->nullable();
            $table->decimal('qtde', 10, 2);
            $table->decimal('unitario', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('NCM')->nullable();
            $table->string('CST')->nullable();
            $table->string('CFOP')->nullable();
            $table->string('CSOSN')->nullable();
            $table->string('IPI')->nullable();
            $table->string('PIS')->nullable();
            $table->string('COFINS')->nullable();
            $table->decimal('ALIQUOTA', 10, 2)->nullable();
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas');
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
        Schema::dropIfExists('itens_entradas');
    }
};
