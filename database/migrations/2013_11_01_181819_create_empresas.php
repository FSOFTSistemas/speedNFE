<?php

use App\Models\Empresa;
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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razao');
            $table->string('fantasia');
            $table->string('cpf_cnpj')->unique();
            $table->unsignedBigInteger('endereco_id');
            $table->foreign('endereco_id')->references('id')->on('enderecos')->onDelete('cascade');
            $table->string('rg_ie')->nullable();
            $table->string('celular')->nullable();
            $table->string('csc');
            $table->integer('idCsc');
            $table->string('status')->nullable();
            $table->integer('sequenciaCupom');
            $table->integer('ultimaNFe')->nullable();
            $table->integer('ultimaNFCe')->nullable();
            $table->integer('ultimaMDFe')->nullable();
            $table->integer('serie')->nullable();
            $table->binary('certificado')->nullable();
            $table->string('senhaCertificado')->nullable();
            $table->integer('ambiente')->nullable();
            $table->integer('limNFes');
            $table->integer('limMDFes');
            $table->integer('limNFCes');
            $table->integer('limClientes');
            $table->integer('limProdutos');
            $table->timestamps();
        });
        Empresa::create([
            'razao' => 'JOSE LUCIANO A. DE CARVALHO',
            'fantasia' => 'FSOFT SISTEMAS',
            'cpf_cnpj' => '42.879.649/0001-74',
            'endereco_id' => 1,
            'rg_ie' => '0977173-55',
            'celular' => '(87) 98144-5566',
            'sequenciaCupom' => 1,
            'ultimaNFe' => 1,
            'ultimaNFCe' => 1,
            'ultimaMDFe' => 1,
            'serie' => 1,
            'certificado' => null,
            'senhaCertificado' => null,
            'ambiente' => 2,
            'status' => 1,
            'csc' => '060',
            'idCsc' => 1,
            'limClientes' => 10,
            'limProdutos' => 100,
            'limNFes' => 1000,
            'limNFCes' => 1000,
            'limMDFes' => 1000
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa');
    }
};
