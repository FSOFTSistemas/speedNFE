<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Usa SQL puro (em vez de ->change(), que exige doctrine/dbal) pra alargar
        // a coluna e comportar o valor criptografado, que é bem maior que a senha original.
        DB::statement('ALTER TABLE empresas MODIFY senhaCertificado TEXT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE empresas MODIFY senhaCertificado VARCHAR(255) NULL');
    }
};
