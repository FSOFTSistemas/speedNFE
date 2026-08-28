<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero')->nullable();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('cliente_nome')->nullable();
            $table->string('cliente_documento')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('data');
            $table->date('validade_at')->nullable();
            $table->string('status', 20)->default('ABERTA');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('desconto', 15, 2)->default(0);
            $table->decimal('acrescimo', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->string('destino', 10)->nullable();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->foreignId('cupom_id')->nullable()->constrained('cupoms')->nullOnDelete();
            $table->foreignId('convertida_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('convertida_at')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'numero']);
            $table->index(['empresa_id', 'status', 'data']);
            $table->index(['empresa_id', 'cliente_id']);
        });

        Schema::create('pre_venda_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_venda_id')->constrained('pre_vendas')->cascadeOnDelete();
            $table->foreignId('produto_id')->nullable()->constrained('produtos')->nullOnDelete();
            $table->string('codigo')->nullable();
            $table->string('descricao');
            $table->string('unidade', 20)->nullable();
            $table->decimal('quantidade', 15, 4);
            $table->decimal('valor_unitario', 15, 4);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('desconto', 15, 2)->default(0);
            $table->decimal('acrescimo', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->timestamps();

            $table->index(['pre_venda_id', 'produto_id']);
        });

        Schema::create('pre_venda_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_venda_id')->constrained('pre_vendas')->cascadeOnDelete();
            $table->foreignId('forma_pag_id')->nullable()->constrained('forma_pags')->nullOnDelete();
            $table->string('descricao');
            $table->decimal('valor', 15, 2);
            $table->date('vencimento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_venda_pagamentos');
        Schema::dropIfExists('pre_venda_itens');
        Schema::dropIfExists('pre_vendas');
    }
};
