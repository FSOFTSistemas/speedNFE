<?php
namespace App\Services;

use App\Models\Estoque;

class EstoquesService{

    public function create($stock, $companyId, $productId)
    {
        return Estoque::create([
            'estoque_atual' => $stock ?? 1,
            'estoque_anterior' => 0,
            'entradas' => $stock ?? 1,
            'saidas' => 0,
            'empresa_id' => $companyId,
            'produto_id' => $productId
        ]);
    }

    public function update($newStock, $newPreviousStock, $inputs, $outputs, $stockId)
    {
        $stock = Estoque::find($stockId);
        return $stock->update([
            'estoque_atual' => $newStock,
            'estoque_anterior' => $newPreviousStock,
            'entradas' => $inputs,
            'saidas' => $outputs
        ]);
    }

    public function reverseStock($prodId, $amount)
    {
        $stock = Estoque::whereProdutoId($prodId)->first();
        return $stock->update([
            'estoque_atual' => $stock->estoque_anterior,
            'estoque_anterior' => $stock->estoque_anterior == $stock->entradas ? 0 : $stock->estoque_anterior + $amount,
            'saidas' => $stock->saidas - $amount
        ]);
    }

    public function out($prodId, $amount)
    {
        $stock = Estoque::whereProdutoId($prodId)->first();
        return $stock->update([
            'estoque_anterior' => $stock->estoque_atual,
            'estoque_atual' => $stock->estoque_atual - $amount,
            'saidas' => $stock->saidas + $amount
        ]);
    }

    public function getCompanyStocks($companyId)
    {
        return Estoque::whereEmpresaId($companyId)->get();
    }

    public function getStock($stockId)
    {
        return Estoque::find($stockId);
    }

}
