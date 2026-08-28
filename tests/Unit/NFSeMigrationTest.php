<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NFSeMigrationTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = config('database.default');
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::create('empresas', function ($table) {
            $table->id();
            $table->binary('certificado')->nullable();
        });
        Schema::create('nfses', function ($table) {
            $table->id();
            $table->string('cClassTrib', 6)->nullable();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);

        parent::tearDown();
    }

    public function test_migrations_adicionam_certificado_e_campos_ibscbs(): void
    {
        $certificateMigration = require database_path('migrations/2026_08_27_010000_add_certificado_conteudo_to_empresas_table.php');
        $ibsCbsMigration = require database_path('migrations/2026_08_27_020000_add_ibscbs_fields_to_nfses_table.php');

        $certificateMigration->up();
        $ibsCbsMigration->up();

        $this->assertTrue(Schema::hasColumn('empresas', 'certificado_conteudo'));
        foreach (['cst_ibs_cbs', 'finNFSe', 'indFinal', 'indDest'] as $column) {
            $this->assertTrue(Schema::hasColumn('nfses', $column));
        }

        $ibsCbsMigration->down();
        $certificateMigration->down();

        $this->assertFalse(Schema::hasColumn('empresas', 'certificado_conteudo'));
        $this->assertFalse(Schema::hasColumn('nfses', 'cst_ibs_cbs'));
    }
}
