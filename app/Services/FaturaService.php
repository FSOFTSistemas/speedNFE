<?php

namespace App\Services;

use App\Models\FaturaPedido;

class FaturaService
{

    public function create($subtotal, $pedido, $empresa)
    {
        return FaturaPedido::create([
            'valor' => $subtotal,
            'vencimento' => today(),
            'venda_id' => $pedido->id,
            'forma_pag_id' => 1,
            'empresa_id' => $empresa,
        ]);
    }

}