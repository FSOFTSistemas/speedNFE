<?php

namespace App\Services;

use App\Models\FaturaPedido;

class FaturaService
{

    public function create($subtotal, $pedido_id, $orderFin, $empresa)
    {
        return FaturaPedido::create([
            'valor' => $subtotal,
            'vencimento' => today(),
            'venda_id' => $pedido_id,
            'forma_pag_id' => $orderFin == 1 ? 1 : 12,
            'empresa_id' => $empresa,
        ]);
    }

    public function update($subtotal, $faturaId)
    {
        $fatura = FaturaPedido::find($faturaId);
        return $fatura->update([
            'valor' => $subtotal,
            'vencimento' => today(),
        ]);
    }

}
