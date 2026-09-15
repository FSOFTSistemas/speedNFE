<?php

namespace App\Services;

use App\Models\ItemCupom;

class ItemCupomService
{

    public function createItemsCupom($itemsList, $cupomId)
    {
        foreach ($itemsList as $item) {
            ItemCupom::create([
                'qtde' => $item['qtde'],
                'unitario' => $item['unitario'],
                'desconto' => $item['desconto'],
                'acrescimo' => $item['acrescimo'],
                'total' => $item['total'],
                'subtotal' => $item['subtotal'],
                'cupom_id' => $cupomId,
                'produto_id' => $item['prodId']
            ]);
        }
    }

}
