<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->string('cTribNac', 6)->nullable()->after('cClass');
            $table->string('cTribMun')->nullable()->after('cTribNac');
            $table->string('cNBS', 20)->nullable()->after('cTribMun');
            $table->string('cIndOp', 6)->nullable()->after('cNBS');
            $table->string('cClassTrib', 6)->nullable()->after('cIndOp');
            $table->string('tribISSQN')->nullable()->after('cClassTrib');
            $table->string('tpRetISSQN')->nullable()->after('tribISSQN');
            $table->decimal('pAliqISSQN', 7, 4)->nullable()->after('tpRetISSQN');
            $table->string('cst_ibs_cbs', 3)->nullable()->after('pAliqISSQN');
            $table->decimal('pRedutorIBSCBS', 7, 4)->nullable()->after('cst_ibs_cbs');
            $table->string('finNFSe')->nullable()->after('pRedutorIBSCBS');
            $table->string('indFinal')->nullable()->after('finNFSe');
            $table->string('indDest')->nullable()->after('indFinal');
        });
    }

    public function down(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dropColumn([
                'cTribNac',
                'cTribMun',
                'cNBS',
                'cIndOp',
                'cClassTrib',
                'tribISSQN',
                'tpRetISSQN',
                'pAliqISSQN',
                'cst_ibs_cbs',
                'pRedutorIBSCBS',
                'finNFSe',
                'indFinal',
                'indDest',
            ]);
        });
    }
};
