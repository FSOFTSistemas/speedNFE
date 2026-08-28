<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfses', function (Blueprint $table) {
            $table->string('cst_ibs_cbs', 3)->nullable()->after('cClassTrib');
            $table->string('finNFSe', 1)->default('0')->after('cst_ibs_cbs');
            $table->string('indFinal', 1)->default('0')->after('finNFSe');
            $table->string('indDest', 1)->default('0')->after('indFinal');
        });
    }

    public function down(): void
    {
        Schema::table('nfses', function (Blueprint $table) {
            $table->dropColumn([
                'cst_ibs_cbs',
                'finNFSe',
                'indFinal',
                'indDest',
            ]);
        });
    }
};
