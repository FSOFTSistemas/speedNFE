<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fluxo_de_caixas', function (Blueprint $table) {
            $table->string('origem')->nullable()->after('plano_de_contas_id');
            $table->unsignedBigInteger('origem_id')->nullable()->after('origem');
            $table->index(['origem', 'origem_id']);
        });
    }

    public function down()
    {
        Schema::table('fluxo_de_caixas', function (Blueprint $table) {
            $table->dropIndex(['origem', 'origem_id']);
            $table->dropColumn(['origem', 'origem_id']);
        });
    }
};
