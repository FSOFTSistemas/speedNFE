<?php

namespace App\Services;

use App\Models\FormaPag;
use Exception;
use Illuminate\Support\Facades\DB;

class FormaPagService{
    public function __construct()
    {

    }

    public function todos(){
        try{
            return FormaPag::all();
        } catch (Exception $e){
            return 0;
        }
    }

    public function store($descricao, $empresa_id){
        try{
            FormaPag::create([
                'descricao' => $descricao,
                'empresa_id' => $empresa_id
            ]);
            return 1;
        } catch (Exception $e){
            return $e;
        }
    }

    public function excluir($id){
        try {
            $forma = FormaPag::findOrFail($id);
            return $forma->delete();
        } catch (Exception $e){
            return 0;
        }
    }
}