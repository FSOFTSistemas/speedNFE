<?php

namespace App\Services;

use App\Models\CupomForma;

class CupomFormaService
{

    public function createCupomFormas($listMethods, $cupomId)
    {
        foreach ($listMethods as $listMethod) {
            CupomForma::create([
                'forma' => $listMethod['forma'],
                'valor' => $listMethod['valorRecebimento'],
                'cupom_id' => $cupomId
            ]);
        }
    }

}
