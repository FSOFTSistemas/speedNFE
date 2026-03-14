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
    
    // public static function verificaVendaPorProduto($empresaId, $produto)
    // {
    //     // Valida se é um produto válido
    //     $produtoValido = Produto::where('empresa_id', $empresaId)
    //         ->where('id', $produto->id)
    //         ->where('tpProd', 1)
    //         ->first();
    
    //     if (!$produtoValido) {
    //         return; // Produto não é do tipo esperado, nada a verificar
    //     }
    
    //     // Verifica se existe algum pedido AUTORIZADO com esse produto
    //     $itemExiste = ItemPedido::where('empresa_id', $empresaId)
    //         ->where('produto_id', $produtoValido->id)
    //         ->whereHas('pedido', function ($query) {
    //             $query->where('estado', 'Autorizado'); // só pedidos autorizados
    //         })
    //         ->exists();
    
    //     if ($itemExiste) {
    //         throw new \Exception('Já existe uma venda autorizada para esse chassi!');
    //     }
    // }
    
    public static function verificaVendaPorProduto($empresaId, $produto)
    {
        return;
        // 1) Valida se é um produto válido da empresa e do tipo esperado
        $produtoValido = Produto::where('empresa_id', $empresaId)
          ->where('id', $produto->id)
            ->where('tpProd', 1) // mantenha seu critério
            ->first();
    
        if (!$produtoValido) {
            return; // não é do tipo/empresa esperado → nada a verificar
        }
    
        // 2) Procura item com pedido AUTORIZADO, ignorando CFOP 493 e 132
        //    (mantendo a estrutura com whereHas). Usamos first() para obter numero_nfe.
        $item = ItemPedido::where('empresa_id', $empresaId)
            ->where('produto_id', $produtoValido->id)
            ->whereHas('pedido', function ($q) {
                $q->where('estado', 'Autorizado')
                  ->whereNotIn('cfop', [493, 132]); // se CFOP for numérico, use [493,132]
            })
            ->with('pedido:id,numero_nfe') // carrega numero_nfe do pedido relacionado
            ->first();
    
        if ($item && $item->pedido) {
            $nf = $item->pedido->numero_nfe ?? null;
            $msg = 'Já existe uma venda autorizada para esse chassi!';
            if ($nf) {
                $msg .= ' NF-e: ' . $nf;
            }
            throw new \Exception($msg);
        }
    }


}