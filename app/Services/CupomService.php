<?php

namespace App\Services;

use App\Enums\SituacaoEnum;
use App\Models\Cupom;

class CupomService
{

    public function buscarCupom($id){
        return Cupom::find($id);
    }

    public function createCupom($nroCupom, $total, $desconto, $acrescimo, $subtotal, $clientId, $empresaId) {
        return Cupom::create([
            'nroCupom' => $nroCupom,
            'situacao' => SituacaoEnum::ATIVO,
            'gerado_nfce' => false,
            'contingencia' => false,
            'total' => $total,
            'desconto' => $desconto,
            'acrescimo' => $acrescimo,
            'subtotal' => $subtotal,
            'cliente_id' => $clientId,
            'empresa_id' => $empresaId
        ])->id;
    }

}
