<?php

namespace App\Services;

use App\Models\Entrada;

class EntradaService {

    public function createEntrada($request, $empresaId)
    {
        return Entrada::create([
            'dataEmissao' => $request->dhEmi,
            'dataEntrada' => $request->dhSaiEnt,
            'numeroNota' => $request->nNF,
            'fornecedor' => $request->fornecedor . ' / ' . $request->CNPJ,
            'chave' => $request->chNFe,
            'valor' => $request->vNF,
            'empresa_id' => $empresaId
        ]);
    }

}