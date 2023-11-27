<?php

namespace App\Services;

use App\Models\Cidade;

class CidadeService {

    public function buscarCidades()
    {
        return Cidade::all();
    }

    public function buscarCidadesPorUf($uf)
    {
        dd($uf);
        return Cidade::where('uf', $uf)->get();
    }

}