<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->string('dfe_referenciado_chave', 44)->nullable()->after('unitario');
            $table->unsignedSmallInteger('dfe_referenciado_n_item')->nullable()->after('dfe_referenciado_chave');
        });
    }

    public function down()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'dfe_referenciado_chave',
                'dfe_referenciado_n_item',
            ]);
        });
    }
};
