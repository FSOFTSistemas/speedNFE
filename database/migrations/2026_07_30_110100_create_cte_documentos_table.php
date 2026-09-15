<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cte_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cte_id');
            $table->foreign('cte_id')->references('id')->on('ctes')->onDelete('cascade');

            // <infDoc> - documento transportado (infNFe)
            $table->string('tipo_documento');
            $table->string('chave');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cte_documentos');
    }
};
