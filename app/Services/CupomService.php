<?php

namespace App\Services;

use App\Models\Cupom;

class CupomService
{

    public function buscarCupom($id){
        return Cupom::find($id);
    }

    public function createCupom($total, $desconto, $acrescimo, $subtotal, $clientId, $empresaId) {
        return Cupom::create([
            'nroCupom' => '123456',
            'data' => date('d/m/Y'),
            'situacao' => 'Finalizada',
            'total' => $total,
            'desconto' => $desconto,
            'acrescimo' => $acrescimo,
            'subtotal' => $subtotal,
            'cliente_id' => $clientId,
            'empresa_id' => $empresaId
        ])->id;
    }

}
