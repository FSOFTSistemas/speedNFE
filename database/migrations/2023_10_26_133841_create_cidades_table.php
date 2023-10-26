<?php

use App\Models\Cidade;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cidades', function (Blueprint $table) {
            $table->id();
            $table->string('cidade', 40);
            $table->string('uf', 2);
            $table->string('cep', 10)->nullable();
            $table->string('ibge', 10);
            $table->string('estado', 127);
            $table->string('municipio', 127)->nullable();
            $table->timestamps();
        });
        $path = Storage::path('public/cidades/cidades.txt');
        $cidades = explode("\r\n", file_get_contents($path));
        foreach ($cidades as $city) {
            if ($city) {
                $cidade = (explode(";", $city));
                Cidade::create([
                    'cidade' => $cidade[1],
                    'uf' => $cidade[2],
                    'cep' => $cidade[3],
                    'ibge' => $cidade[4],
                    'estado' => $cidade[5],
                    'municipio' => $cidade[6],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cidades');
    }
};
