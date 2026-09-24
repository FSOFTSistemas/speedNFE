<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Percentual de redução da base de cálculo do ICMS (pRedBC, CST 20/70) por item do pedido.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->decimal('icms_reducao', 7, 4)->nullable()->after('cst_csosn');
        });
    }

    public function down()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->dropColumn('icms_reducao');
        });
    }
};
