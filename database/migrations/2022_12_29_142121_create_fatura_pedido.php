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
        Schema::create('fatura_pedidos', function (Blueprint $table) {
            $table->id();
            $table->float('valor');
            $table->date('vencimento');
            $table->unsignedBigInteger('venda_id');
            $table->foreign('venda_id')->references('id')->on('pedidos')->onDelete('cascade');
            $table->unsignedBigInteger('forma_pag_id');
            $table->foreign('forma_pag_id')->references('id')->on('forma_pags');
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
        Schema::dropIfExists('fatura_pedido');
    }
};
