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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa');
            $table->float('capacidade');
            $table->string('renavan');
            $table->float('tara');
            $table->float('capacidade_m3');
            $table->string('tipo_carroceria');
            $table->string('tipo_veiculo');
            $table->string('tipo_rodado');
            $table->string('uf_veiculo');
            $table->string('tipo_propriedade');
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
        Schema::dropIfExists('veiculos');
    }
};
