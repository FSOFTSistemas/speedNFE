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
        Schema::create('item_cupoms', function (Blueprint $table) {
            $table->id();
            $table->integer('qtde');
            $table->double('unitario');
            $table->double('desconto');
            $table->double('acrescimo');
            $table->double('total');;
            $table->double('subtotal');
            $table->unsignedBigInteger('cupom_id');
            $table->foreign('cupom_id')->references('id')->on('cupoms')->onDelete('cascade');
            $table->unsignedBigInteger('produto_id');
            $table->foreign('produto_id')->references('id')->on('produtos')->onDelete('cascade');
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
        Schema::dropIfExists('item_cupoms');
    }
};
