<?php

namespace App\Services;

use App\Models\Cupom;

class CupomService
{

    public function buscarCupom($id){
        return Cupom::find($id);
    }

    public function createCupom($request) {
        return Cupom::create([
            'nroCupom' => $request->nroCupom,
            'data' => $request->data,
            'situacao' => $request->situacao,
            'total' => $request->total,
            'desconto' => $request->desconto,
            'acrescimo' => $request->acrescimo,
            'subtotal' => $request->subtotal,
            'cliente_id' => $request->cliente_id,
            'empresa_id' => $request->empresa_id
        ]);
    }

}
