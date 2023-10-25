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
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->float('desconto');
            $table->float('acrescimo');
            $table->float('unitario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_pedido', function (Blueprint $table) {
            //
        });
    }
};
