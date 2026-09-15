<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdfe_xmls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mdfe_id');
            $table->foreign('mdfe_id')->references('id')->on('m_d_f_e_s')->onDelete('cascade');
            $table->string('tipo', 20);
            $table->longText('xml');
            $table->timestamps();

            $table->unique(['mdfe_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdfe_xmls');
    }
};
