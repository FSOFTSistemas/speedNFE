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
        Schema::create('fluxo_de_caixas', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->enum('tipo', ['Entrada', 'Saída']);
            $table->unsignedBigInteger('plano_de_contas_id');
            $table->foreign('plano_de_contas_id')->references('id')->on('plano_de_contas')->onDelete('cascade');
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
        Schema::dropIfExists('fluxo_de_caixas');
    }
};
