<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabela de CSTs do IBS/CBS
        Schema::create('cst_ibs_cbs', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 3)->unique(); // Ex: 010, 200
            $table->string('descricao');           // Ex: Tributação integral
            $table->timestamps();
        });

        // 2. Tabela de Classificação Tributária (cClassTrib)
        Schema::create('c_class_tribs', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 6)->unique();      // Ex: 100001
            $table->string('descricao', 1000);          // Descrição pode ser longa
            $table->string('cst_compativel', 3)->index(); // O vínculo com a tabela de cima
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('c_class_tribs');
        Schema::dropIfExists('cst_ibs_cbs');
    }
};