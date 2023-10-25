<?php
namespace App\Services;

use App\Models\Endereco;
use Exception;
use Illuminate\Support\Facades\DB;

class EnderecosService{
    public function __construct()
    {

    }

    public function getEndereco($empresa_id){
        try{
            return Endereco::where('id', '=', $empresa_id)
            ->first();
        } catch (Exception $e) {
            return 0;
        }
    }

    public function um($id){
        try{
            return Endereco::findOrFail($id);
        } catch (Exception $e){
            return 0;
        }
    }

    public function salvar($rua, $bairro, $numero, $cidade, $uf, $codigoIBGE, $cep, $complemento){
        try {
            return Endereco::create([
                'rua' => $rua,
                'bairro' => $bairro,
                'numero' => $numero,
                'cidade' => $cidade,
                'uf' => $uf,
                'codigoIBGE' => $codigoIBGE,
                'cep' => $cep,
                'complemento' => $complemento
            ]);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function editar($id, $rua, $bairro, $numero, $cidade, $uf, $codigoIBGE, $cep, $complemento){
        try{
            $endereco = Endereco::findOrFail($id);

            $endereco->update([
                'rua' => $rua,
                'bairro' => $bairro,
                'numero' => $numero,
                'cidade' => $cidade,
                'uf' => $uf,
                'codigoIBGE' => $codigoIBGE,
                'cep' => $cep,
                'complemento' => $complemento
            ]);
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }
}