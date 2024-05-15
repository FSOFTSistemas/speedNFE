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
        Schema::create('cupom_formas', function (Blueprint $table) {
            $table->id();
            $table->string('forma');
            $table->double('valor');
            $table->unsignedBigInteger('cupom_id');
            $table->foreign('cupom_id')->references('id')->on('cupoms')->onDelete('cascade');
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
        Schema::dropIfExists('cupom_formas');
    }
};
