<?php

use App\Models\Endereco;
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
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('rua')->nullable();
            $table->string('bairro')->nullable();
            $table->string('numero')->nullable();
            $table->string('cidade')->nullable();
            $table->string('complemento')->nullable();
            $table->string('uf')->nullable();
            $table->integer('codigoIBGE')->nullable();
            $table->string('cep')->nullable();
            $table->timestamps();
        });
        Endereco::create([
            'rua' => 'Rua Dom Luiz de brito',
            'bairro' => 'Centro',
            'numero' => '53',
            'cidade' => 'GARANHUNS',
            'uf' => 'PE',
            'codigoIBGE' => '123456',
            'complemento' => 'n/d',
            'cep' => '55295050'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('endereco');
    }
};
