<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $blueprint) {
            // Adicionando o campo CRT como inteiro, permitindo nulo ou definindo um padrão
            $blueprint->integer('crt')->nullable()->after('status'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $blueprint) {
            $blueprint->dropColumn('crt');
        });
    }
};