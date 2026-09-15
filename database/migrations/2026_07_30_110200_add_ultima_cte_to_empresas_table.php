<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $blueprint) {
            $blueprint->integer('ultimaCTe')->nullable()->after('ultimaNFCom');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $blueprint) {
            $blueprint->dropColumn('ultimaCTe');
        });
    }
};
