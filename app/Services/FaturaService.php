<?php

namespace App\Services;

use App\Models\FaturaPedido;

class FaturaService
{

    public function create($subtotal, $pedido_id, $empresa)
    {
        return FaturaPedido::create([
            'valor' => $subtotal,
            'vencimento' => today(),
            'venda_id' => $pedido_id,
            'forma_pag_id' => 1,
            'empresa_id' => $empresa,
        ]);
    }

}