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
        Schema::create('cupoms', function (Blueprint $table) {
            $table->id();
            $table->string('nroCupom');
            $table->timestamp('data');
            $table->string('situacao');
            $table->boolean('gerado_nfce');
            $table->boolean('contingencia');
            $table->double('total');
            $table->double('desconto');
            $table->double('acrescimo');
            $table->double('subtotal');
            $table->double('troco');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
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
        Schema::dropIfExists('cupoms');
    }
};
