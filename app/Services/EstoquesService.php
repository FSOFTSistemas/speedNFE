<?php
namespace App\Services;

use App\Models\Estoque;

class EstoquesService{

    public function create($stock, $inputs, $outputs, $companyId, $productId)
    {
        return Estoque::create([
            'estoque_atual' => $stock,
            'estoque_anterior' => 0,
            'entradas' => $inputs ?? $stock,
            'saidas' => $outputs,
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

    public function entry()
    {

    }

    public function out()
    {

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
