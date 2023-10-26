<?php

use App\Models\FormaPag;
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
        Schema::create('forma_pags', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->timestamps();
        });
        $forma_pagamento = [
            'Dinheiro',
            'Transferência Bancária',
            'Cartão de Crédito',
            'Cartão de Débito',
            'Boleto Bancário',
            'Cheque',
            'Pagamento Eletrônico',
            'PIX',
            'Carta de Crédito',
            'Permuta',
            'Pagamento à Prazo'
        ];
        for ($i = 0; $i < 11; $i++) {
        FormaPag::create([
            'descricao' => $forma_pagamento[$i],
            'empresa_id' => 1
        ]);
    }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forma_pags');
    }
};
