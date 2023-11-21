<?php

namespace App\Services;

use App\Models\Veiculo;
class VeiculosService
{

    public function salvar($request)
    {
       return Veiculo::create($request->all());
    }
}