<?php

namespace App\Services;

use App\Models\Cidade;

class CidadeService {

    public function buscarCidades()
    {
        return Cidade::all();
    }

}