<?php

namespace App\Services;

use App\Models\Empresa;
use Exception;
use Illuminate\Support\Facades\DB;

class EmpresasService{

    public function todas(){
        return Empresa::all();
    }

    public function minhaEmpresa($id)
    {
        return Empresa::select('empresas.*')
            ->where('empresas.id', $id)
            ->get();
    }

    public function buscarEmpresa($id)
    {
        return Empresa::select('empresas.*', 'users.name', 'users.email', 'enderecos.rua', 'enderecos.bairro', 'enderecos.numero', 'enderecos.cidade',
        'enderecos.complemento', 'enderecos.uf', 'enderecos.cep', 'enderecos.codigoIBGE')
            ->join('users', 'users.empresa_id', 'empresas.id')
            ->join('enderecos', 'enderecos.id', 'empresas.endereco_id')
            ->where('empresas.id', $id)
            ->first();
    }

    public function todos($empresa){
        if($empresa == 1){
            $empresa = '%';
        }
        return DB::table('empresas')
        ->select('*')
        ->where('id', 'like', $empresa)
        ->get();
    }

    public function getEmpresa($id_empresa){
        try{
            return DB::table('empresas')
            ->where('id', '=', $id_empresa)
            ->first();
        } catch (Exception $e) {
            return 0;
        }
    }
}