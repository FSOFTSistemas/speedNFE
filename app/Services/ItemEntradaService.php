<?php

namespace App\Services;

use App\Models\ItensEntrada;

class ItemEntradaService {

    public function createInputItems($entradaId, $productsList, $empresaId)
    {
        foreach ($productsList as $prod) {
            ItensEntrada::create([
                'entrada_id' => $entradaId,
                'produto_id' => $prod['produtoId'],
                'qtde' => $prod['qtde'],
                'empresa_id' => $empresaId
            ]);
        }
    }

}