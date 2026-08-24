<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->integer('ultimaNFSe')->nullable()->after('ultimaCTe');
            $table->integer('ultimaDPS')->nullable()->after('ultimaNFSe');
            $table->integer('serieNFSe')->nullable()->after('serie');
            $table->integer('limNFSe')->nullable()->after('limNFCes');
            $table->string('inscricao_municipal')->nullable()->after('rg_ie');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'ultimaNFSe',
                'ultimaDPS',
                'serieNFSe',
                'limNFSe',
                'inscricao_municipal',
            ]);
        });
    }
};
