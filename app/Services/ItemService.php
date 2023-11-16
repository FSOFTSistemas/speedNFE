<?php

namespace App\Services;

use App\Models\ItemPedido;

class ItemService
{

    public function create($pedido_id, $prod, $qtde, $empresa, $desconto, $unitario)
    {
        return ItemPedido::create([
            'pedido_id' => $pedido_id,
            'produto_id' => $prod->id,
            'qtde' => $qtde,
            'empresa_id' => $empresa,
            'desconto' => $desconto,
            'acrescimo' => 0,
            'unitario' => $unitario,
        ]);
    }

    public function deleteItems($pedido_id)
    {
        return ItemPedido::where('pedido_id', '=', $pedido_id)->delete();
    }

}