<?php

namespace App\Services;

use App\Models\MDFeMotorista;

class MDFeMotoristaService
{

    public function createMotorista($motorista_id, $mdfe_id)
    {
        return MDFeMotorista::create([
            'motorista_id' => $motorista_id,
            'mdfe_id' => $mdfe_id
        ]);
    }

}