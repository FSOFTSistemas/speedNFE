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
        Schema::table('users', function (Blueprint $table) {
            // Adiciona o campo 'tipo' do tipo ENUM depois da coluna 'cargo'
            // Define 'usuario' como o valor padrão para novos registros ou registros existentes
            $table->enum('tipo', ['admin', 'usuario'])->default('usuario')->after('cargo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove a coluna caso precise reverter a migration
            $table->dropColumn('tipo');
        });
    }
};