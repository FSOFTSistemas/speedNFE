<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dados fiscais editáveis por item do pedido (NFe). Quando fiscal_personalizado = false,
 * o NFeService continua usando os tributos do cadastro do produto e o CFOP do pedido.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->boolean('fiscal_personalizado')->default(false)->after('dfe_referenciado_n_item');
            $table->string('cfop_item', 4)->nullable()->after('fiscal_personalizado');
            $table->string('cst_csosn', 3)->nullable()->after('cfop_item');

            $table->decimal('icms_base', 15, 2)->nullable()->after('cst_csosn');
            $table->decimal('icms_aliquota', 7, 4)->nullable()->after('icms_base');
            $table->decimal('icms_valor', 15, 2)->nullable()->after('icms_aliquota');

            $table->decimal('icms_st_mva', 7, 4)->nullable()->after('icms_valor');
            $table->decimal('icms_st_base', 15, 2)->nullable()->after('icms_st_mva');
            $table->decimal('icms_st_aliquota', 7, 4)->nullable()->after('icms_st_base');
            $table->decimal('icms_st_valor', 15, 2)->nullable()->after('icms_st_aliquota');

            $table->string('cst_pis', 2)->nullable()->after('icms_st_valor');
            $table->decimal('pis_base', 15, 2)->nullable()->after('cst_pis');
            $table->decimal('pis_aliquota', 7, 4)->nullable()->after('pis_base');
            $table->decimal('pis_valor', 15, 2)->nullable()->after('pis_aliquota');

            $table->string('cst_cofins', 2)->nullable()->after('pis_valor');
            $table->decimal('cofins_base', 15, 2)->nullable()->after('cst_cofins');
            $table->decimal('cofins_aliquota', 7, 4)->nullable()->after('cofins_base');
            $table->decimal('cofins_valor', 15, 2)->nullable()->after('cofins_aliquota');
        });
    }

    public function down()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'fiscal_personalizado',
                'cfop_item',
                'cst_csosn',
                'icms_base',
                'icms_aliquota',
                'icms_valor',
                'icms_st_mva',
                'icms_st_base',
                'icms_st_aliquota',
                'icms_st_valor',
                'cst_pis',
                'pis_base',
                'pis_aliquota',
                'pis_valor',
                'cst_cofins',
                'cofins_base',
                'cofins_aliquota',
                'cofins_valor',
            ]);
        });
    }
};
