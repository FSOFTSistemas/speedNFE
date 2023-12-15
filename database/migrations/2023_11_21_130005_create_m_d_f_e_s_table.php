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
        Schema::create('m_d_f_e_s', function (Blueprint $table) {
            $table->id();
            $table->integer('numero');
            $table->integer('serie');
            $table->date('data');
            $table->string('situacao');
            $table->string('uf_inicio');
            $table->string('uf_termino');
            $table->string('uf_percurso');
            $table->string('codMunCarregamento');
            $table->string('municipioCarregamento');
            $table->string('tipo_documento');
            $table->string('chave_acesso')->nullable();
            $table->double('valor_total');
            $table->double('peso');
            $table->string('numeroLacre')->nullable();
            $table->string('tipo_carga');
            $table->string('info_fisco', 255)->nullable();
            $table->string('info_contribuinte', 255)->nullable();
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->unsignedBigInteger('veiculo_tracao_id');
            $table->foreign('veiculo_tracao_id')->references('id')->on('veiculos')->onDelete('cascade');
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
        Schema::dropIfExists('m_d_f_e_s');
    }
};
