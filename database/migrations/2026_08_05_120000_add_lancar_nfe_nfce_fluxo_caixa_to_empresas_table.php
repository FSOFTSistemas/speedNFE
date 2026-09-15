<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('lancar_nfe_nfce_fluxo_caixa')->default(true)->after('crt');
        });
    }

    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('lancar_nfe_nfce_fluxo_caixa');
        });
    }
};
