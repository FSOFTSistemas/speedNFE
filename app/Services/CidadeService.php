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
        return Cidade::where('uf', $uf)->get();
    }

    public function buscarCidade($cidade)
    {
        return Cidade::where('cidade', $cidade)->first();
    }
}