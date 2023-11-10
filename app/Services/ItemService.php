<?php

namespace App\Services;

use App\Models\ItemPedido;

class ItemService
{

    public function create($pedido, $prod, $qtde, $empresa, $desconto, $unitario)
    {
        return ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $prod->id,
            'qtde' => $qtde,
            'empresa_id' => $empresa,
            'desconto' => $desconto,
            'acrescimo' => 0,
            'unitario' => $unitario,
        ]);
    }

}