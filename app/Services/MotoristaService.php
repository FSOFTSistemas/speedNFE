<?php

namespace App\Services;

use App\Models\Motorista;

class MotoristaService
{

    public function buscarMotoristas($empresaId)
    {
        return Motorista::select('motoristas.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'motoristas.empresaId')
            ->where('motoristas.empresaId', $empresaId)
            ->get();
    }

}