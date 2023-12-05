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
        Schema::create('m_d_fe_notas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('chave');
            $table->string('uf');
            $table->string('municipio');
            $table->string('codMun');
            $table->double('valor');
            $table->double('peso');
            $table->string('serie');
            $table->string('numero');
            $table->unsignedBigInteger('mdfe_id');
            $table->foreign('mdfe_id')->references('id')->on('m_d_f_e_s')->onDelete('cascade');
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
        Schema::dropIfExists('m_d_fe_notas');
    }
};
