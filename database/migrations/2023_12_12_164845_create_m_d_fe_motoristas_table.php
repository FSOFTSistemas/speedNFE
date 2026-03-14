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
        Schema::create('m_d_fe_motoristas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('motorista_id');
            $table->foreign('motorista_id')->references('id')->on('motoristas')->onDelete('cascade');
            $table->unsignedBigInteger('mdfe_id');
            $table->foreign('mdfe_id')->references('id')->on('m_d_f_e_s')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_fe_motoristas');
    }
};
