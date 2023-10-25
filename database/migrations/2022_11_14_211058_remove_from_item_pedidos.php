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
            $table->dropColumn('valor');
            $table->dropColumn('subtotal');
            $table->dropColumn('desconto');
            $table->dropColumn('total');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            //
        });
    }
};

