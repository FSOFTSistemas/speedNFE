<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class PreVendaCalculator
{
    public function calcular(array $itens, float $desconto = 0, float $acrescimo = 0): array
    {
        $subtotal = 0.0;
        $totalItens = 0.0;
        $itensCalculados = [];

        foreach ($itens as $index => $item) {
            $quantidade = (float) $item['quantidade'];
            $valorUnitario = (float) $item['valor_unitario'];
            $descontoItem = round((float) ($item['desconto'] ?? 0), 2);
            $acrescimoItem = round((float) ($item['acrescimo'] ?? 0), 2);
            $subtotalItem = round($quantidade * $valorUnitario, 2);
            $totalItem = round($subtotalItem - $descontoItem + $acrescimoItem, 2);

            if ($totalItem < 0) {
                throw ValidationException::withMessages([
                    "itens.{$index}.desconto" => 'O desconto não pode superar o valor do item.',
                ]);
            }

            $subtotal += $subtotalItem;
            $totalItens += $totalItem;
            $itensCalculados[] = array_merge($item, [
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'subtotal' => $subtotalItem,
                'desconto' => $descontoItem,
                'acrescimo' => $acrescimoItem,
                'total' => $totalItem,
            ]);
        }

        $subtotal = round($subtotal, 2);
        $desconto = round($desconto, 2);
        $acrescimo = round($acrescimo, 2);
        $total = round($totalItens - $desconto + $acrescimo, 2);

        if ($total < 0) {
            throw ValidationException::withMessages([
                'desconto' => 'O desconto não pode superar o valor da pré-venda.',
            ]);
        }

        return [
            'itens' => $itensCalculados,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'acrescimo' => $acrescimo,
            'total' => $total,
        ];
    }
}
