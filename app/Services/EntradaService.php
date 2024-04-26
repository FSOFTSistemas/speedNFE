<?php

namespace App\Services;

use App\Models\Entrada;

class EntradaService {

    public function createEntrada($request, $empresaId)
    {
        return Entrada::create([
            'dataEmissao' => $request->ide->dhEmi,
            'dataEntrada' => $request->ide->dhSaiEnt,
            'numeroNota' => $request->ide->nNF,
            'fornecedor' => $request->emit->xNome . ' / ' .$request->emit->CNPJ,
            'chave' => $request->chNFe,
            'valor' => $request->vNF,
            'empresa_id' => $empresaId
        ]);
    }

}