<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_leituras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notificacao_id');
            $table->foreign('notificacao_id')->references('id')->on('notificacoes')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('lida_em')->nullable();
            $table->timestamps();
            $table->unique(['notificacao_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_leituras');
    }
};
