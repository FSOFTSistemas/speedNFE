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
        Schema::create('n_f_ces', function (Blueprint $table) {
            $table->id();
            $table->string('nro');
            $table->timestamp('data');
            $table->integer('serie');
            $table->string('chave');
            $table->boolean('contingencia');
            $table->string('situacao');
            $table->binary('xml');
            $table->unsignedBigInteger('cupom_id');
            $table->foreign('cupom_id')->references('id')->on('cupoms')->onDelete('cascade');
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
        Schema::dropIfExists('n_f_ces');
    }
};
