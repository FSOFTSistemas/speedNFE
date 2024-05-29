<?php

namespace App\Services;

use App\Models\CupomForma;

class CupomFormaService
{

    public function createCupomFormas($listMethods, $cupomId)
    {
        foreach ($listMethods as $index => $method) {
            if (isset($method)) {
                CupomForma::create([
                    'forma' => $index,
                    'valor' => $method,
                    'cupom_id' => $cupomId
                ]);
            }
        }
    }

}
