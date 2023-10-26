<?php

use App\Models\Categoria;
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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->integer('status');
            $table->timestamps();
        });
        $categorias = [
            'Alimentos',
            'Eletrônicos',
            'Vestuário',
            'Materiais de Construção',
            'Automóveis',
            'Saúde e Beleza',
            'Livros e Papelaria',
            'Móveis e Decoração',
            'Produtos Agrícolas',
            'Serviços',
        ];
        for ($i = 0; $i < 10; $i++) {
            Categoria::create([
                'descricao' => $categorias[$i],
                'status' => 1,
                'empresa_id' => 1,
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categoria');
    }
};
