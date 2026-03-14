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
        Schema::create('plano_de_contas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('descricao');
            $table->enum('tipo', ['Receita', 'Despesa', 'Ativo', 'Passivo']);
            $table->foreignId('conta_pai_id')->nullable()->constrained('plano_de_contas')->onDelete('cascade'); 
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plano_de_contas');
    }
};
