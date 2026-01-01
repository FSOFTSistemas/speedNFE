<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Código de Classificação Tributária (Ex: '1001', '01')
            // String pois pode começar com zero
            $table->string('cClassTrib', 10)->nullable()->after('ncm'); 

            // Alíquotas (Percentuais)
            // Usei nullable, pois produtos antigos podem não ter isso setado ainda
            $table->decimal('pIBS', 8, 4)->default(0)->nullable()->comment('Alíquota IBS %');
            $table->decimal('pCBS', 8, 4)->default(0)->nullable()->comment('Alíquota CBS %');
            
            // Imposto Seletivo (Cuidado com a palavra chave 'is' do MySQL, usei sulfixo _imposto)
            $table->decimal('pIS_imposto', 8, 4)->default(0)->nullable()->comment('Alíquota Imposto Seletivo %');
            
            // CST específico do novo regime (ainda a ser padronizado, mas bom ter)
            $table->string('cst_ibs_cbs', 4)->nullable();
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['cClassTrib', 'pIBS', 'pCBS', 'pIS_imposto', 'cst_ibs_cbs']);
        });
    }
};