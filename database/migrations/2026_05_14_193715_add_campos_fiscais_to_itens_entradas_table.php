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
        Schema::table('itens_entradas', function (Blueprint $table) {
            // Evita erro caso alguma coluna já exista
            if (!Schema::hasColumn('itens_entradas', 'numero_item')) {
                $table->integer('numero_item')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'codigo_fornecedor')) {
                $table->string('codigo_fornecedor')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'codigo_barras')) {
                $table->string('codigo_barras')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'codigo_barras_tributavel')) {
                $table->string('codigo_barras_tributavel')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'descricao')) {
                $table->string('descricao')->nullable();
            }
            // Campos abaixo são novos e não devem duplicar colunas já existentes
            if (!Schema::hasColumn('itens_entradas', 'ncm')) {
                $table->string('ncm')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'cest')) {
                $table->string('cest')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'cfop')) {
                $table->string('cfop')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'unidade')) {
                $table->string('unidade')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'unidade_tributavel')) {
                $table->string('unidade_tributavel')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'quantidade_tributavel')) {
                $table->decimal('quantidade_tributavel', 15, 4)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_unitario')) {
                $table->decimal('valor_unitario', 15, 6)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_unitario_tributavel')) {
                $table->decimal('valor_unitario_tributavel', 15, 6)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_total')) {
                $table->decimal('valor_total', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_desconto')) {
                $table->decimal('valor_desconto', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_frete')) {
                $table->decimal('valor_frete', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_seguro')) {
                $table->decimal('valor_seguro', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_outros')) {
                $table->decimal('valor_outros', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'origem_icms')) {
                $table->string('origem_icms')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'cst_icms')) {
                $table->string('cst_icms')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'csosn')) {
                $table->string('csosn')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'modalidade_bc_icms')) {
                $table->string('modalidade_bc_icms')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_bc_icms')) {
                $table->decimal('valor_bc_icms', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'aliquota_icms')) {
                $table->decimal('aliquota_icms', 8, 4)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_icms')) {
                $table->decimal('valor_icms', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'modalidade_bc_icms_st')) {
                $table->string('modalidade_bc_icms_st')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_bc_icms_st')) {
                $table->decimal('valor_bc_icms_st', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'aliquota_icms_st')) {
                $table->decimal('aliquota_icms_st', 8, 4)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_icms_st')) {
                $table->decimal('valor_icms_st', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_icms_desonerado')) {
                $table->decimal('valor_icms_desonerado', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'cst_ipi')) {
                $table->string('cst_ipi')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'enquadramento_ipi')) {
                $table->string('enquadramento_ipi')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_bc_ipi')) {
                $table->decimal('valor_bc_ipi', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'aliquota_ipi')) {
                $table->decimal('aliquota_ipi', 8, 4)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_ipi')) {
                $table->decimal('valor_ipi', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'cst_pis')) {
                $table->string('cst_pis')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_bc_pis')) {
                $table->decimal('valor_bc_pis', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'aliquota_pis')) {
                $table->decimal('aliquota_pis', 8, 4)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_pis')) {
                $table->decimal('valor_pis', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'cst_cofins')) {
                $table->string('cst_cofins')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_bc_cofins')) {
                $table->decimal('valor_bc_cofins', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'aliquota_cofins')) {
                $table->decimal('aliquota_cofins', 8, 4)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'valor_cofins')) {
                $table->decimal('valor_cofins', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'numero_pedido')) {
                $table->string('numero_pedido')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'item_pedido')) {
                $table->string('item_pedido')->nullable();
            }
            if (!Schema::hasColumn('itens_entradas', 'informacoes_adicionais')) {
                $table->text('informacoes_adicionais')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('itens_entradas', function (Blueprint $table) {
            if (Schema::hasColumn('itens_entradas', 'numero_item')) {
                $table->dropColumn('numero_item');
            }
            if (Schema::hasColumn('itens_entradas', 'codigo_fornecedor')) {
                $table->dropColumn('codigo_fornecedor');
            }
            if (Schema::hasColumn('itens_entradas', 'codigo_barras')) {
                $table->dropColumn('codigo_barras');
            }
            if (Schema::hasColumn('itens_entradas', 'codigo_barras_tributavel')) {
                $table->dropColumn('codigo_barras_tributavel');
            }
            if (Schema::hasColumn('itens_entradas', 'descricao')) {
                $table->dropColumn('descricao');
            }
            if (Schema::hasColumn('itens_entradas', 'ncm')) {
                $table->dropColumn('ncm');
            }
            if (Schema::hasColumn('itens_entradas', 'cest')) {
                $table->dropColumn('cest');
            }
            if (Schema::hasColumn('itens_entradas', 'cfop')) {
                $table->dropColumn('cfop');
            }
            if (Schema::hasColumn('itens_entradas', 'unidade')) {
                $table->dropColumn('unidade');
            }
            if (Schema::hasColumn('itens_entradas', 'unidade_tributavel')) {
                $table->dropColumn('unidade_tributavel');
            }
            if (Schema::hasColumn('itens_entradas', 'quantidade_tributavel')) {
                $table->dropColumn('quantidade_tributavel');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_unitario')) {
                $table->dropColumn('valor_unitario');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_unitario_tributavel')) {
                $table->dropColumn('valor_unitario_tributavel');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_total')) {
                $table->dropColumn('valor_total');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_desconto')) {
                $table->dropColumn('valor_desconto');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_frete')) {
                $table->dropColumn('valor_frete');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_seguro')) {
                $table->dropColumn('valor_seguro');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_outros')) {
                $table->dropColumn('valor_outros');
            }
            if (Schema::hasColumn('itens_entradas', 'origem_icms')) {
                $table->dropColumn('origem_icms');
            }
            if (Schema::hasColumn('itens_entradas', 'cst_icms')) {
                $table->dropColumn('cst_icms');
            }
            if (Schema::hasColumn('itens_entradas', 'csosn')) {
                $table->dropColumn('csosn');
            }
            if (Schema::hasColumn('itens_entradas', 'modalidade_bc_icms')) {
                $table->dropColumn('modalidade_bc_icms');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_bc_icms')) {
                $table->dropColumn('valor_bc_icms');
            }
            if (Schema::hasColumn('itens_entradas', 'aliquota_icms')) {
                $table->dropColumn('aliquota_icms');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_icms')) {
                $table->dropColumn('valor_icms');
            }
            if (Schema::hasColumn('itens_entradas', 'modalidade_bc_icms_st')) {
                $table->dropColumn('modalidade_bc_icms_st');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_bc_icms_st')) {
                $table->dropColumn('valor_bc_icms_st');
            }
            if (Schema::hasColumn('itens_entradas', 'aliquota_icms_st')) {
                $table->dropColumn('aliquota_icms_st');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_icms_st')) {
                $table->dropColumn('valor_icms_st');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_icms_desonerado')) {
                $table->dropColumn('valor_icms_desonerado');
            }
            if (Schema::hasColumn('itens_entradas', 'cst_ipi')) {
                $table->dropColumn('cst_ipi');
            }
            if (Schema::hasColumn('itens_entradas', 'enquadramento_ipi')) {
                $table->dropColumn('enquadramento_ipi');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_bc_ipi')) {
                $table->dropColumn('valor_bc_ipi');
            }
            if (Schema::hasColumn('itens_entradas', 'aliquota_ipi')) {
                $table->dropColumn('aliquota_ipi');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_ipi')) {
                $table->dropColumn('valor_ipi');
            }
            if (Schema::hasColumn('itens_entradas', 'cst_pis')) {
                $table->dropColumn('cst_pis');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_bc_pis')) {
                $table->dropColumn('valor_bc_pis');
            }
            if (Schema::hasColumn('itens_entradas', 'aliquota_pis')) {
                $table->dropColumn('aliquota_pis');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_pis')) {
                $table->dropColumn('valor_pis');
            }
            if (Schema::hasColumn('itens_entradas', 'cst_cofins')) {
                $table->dropColumn('cst_cofins');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_bc_cofins')) {
                $table->dropColumn('valor_bc_cofins');
            }
            if (Schema::hasColumn('itens_entradas', 'aliquota_cofins')) {
                $table->dropColumn('aliquota_cofins');
            }
            if (Schema::hasColumn('itens_entradas', 'valor_cofins')) {
                $table->dropColumn('valor_cofins');
            }
            if (Schema::hasColumn('itens_entradas', 'numero_pedido')) {
                $table->dropColumn('numero_pedido');
            }
            if (Schema::hasColumn('itens_entradas', 'item_pedido')) {
                $table->dropColumn('item_pedido');
            }
            if (Schema::hasColumn('itens_entradas', 'informacoes_adicionais')) {
                $table->dropColumn('informacoes_adicionais');
            }
        });
    }
};
