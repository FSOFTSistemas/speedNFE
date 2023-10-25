<?php

namespace App\Services;

use App\Models\Empresa;
use Exception;
use Illuminate\Support\Facades\DB;

class EmpresasService{
    public function __construct()
    {

    }

    public function todas(){
        return Empresa::all();
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