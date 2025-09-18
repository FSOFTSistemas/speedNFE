<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna 'status' que pode ser 'ativo' ou 'inativo'
            // O padrão 'ativo' garante que seus usuários atuais não sejam afetados
            $table->string('status', 20)->default('ativo')->after('tipo');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};