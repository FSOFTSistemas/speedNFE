<?php

namespace App\Services;

use App\Models\ItemPedido;
use App\Models\Produto;

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

    public static function verificaVendaPorProduto($empresaId, $produto)
    {
        $produtoValido = Produto::where('empresa_id', $empresaId)
                                ->where('id', $produto->id)
                                ->where('tpProd', 1)
                                ->first();

        if (!$produtoValido) {
            return; // Produto não é do tipo esperado, nada a verificar
        }

        $itemExiste = ItemPedido::where('empresa_id', $empresaId)
                                ->where('produto_id', $produtoValido->id)
                                ->exists();

        if ($itemExiste) {
            throw new \Exception('Já existe uma venda para esse chassi!');
        }
    }


}