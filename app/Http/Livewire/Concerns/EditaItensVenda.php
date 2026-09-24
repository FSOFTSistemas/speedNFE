<?php

namespace App\Http\Livewire\Concerns;

use App\Services\ItemFiscalService;

/**
 * Edição inline (quantidade e valor unitário) de itens já adicionados em $vendaItens.
 * Usado pelas telas de novo pedido e de edição de pedido.
 */
trait EditaItensVenda
{
    public $itemEditando = null;
    public $editQuantidade = 1;
    public $editUnitario = 0;

    public function editarItem($index)
    {
        if (!isset($this->vendaItens[$index])) {
            return;
        }

        $this->resetErrorBag('itemEdicao');
        $this->itemEditando = $index;
        $this->editQuantidade = $this->vendaItens[$index]['quantidade'];
        $this->editUnitario = $this->vendaItens[$index]['unitario'];
    }

    public function cancelarEdicaoItem()
    {
        $this->resetErrorBag('itemEdicao');
        $this->itemEditando = null;
    }

    public function salvarEdicaoItem()
    {
        $index = $this->itemEditando;

        if ($index === null || !isset($this->vendaItens[$index])) {
            $this->cancelarEdicaoItem();
            return;
        }

        $quantidade = (float) str_replace(',', '.', $this->editQuantidade);
        $unitario = (float) str_replace(',', '.', $this->editUnitario);
        $desconto = (float) $this->vendaItens[$index]['desconto'];

        if ($quantidade <= 0) {
            $this->addError('itemEdicao', 'A quantidade deve ser maior que zero.');
            return;
        }

        if ($unitario <= 0) {
            $this->addError('itemEdicao', 'O valor unitário deve ser maior que zero.');
            return;
        }

        if ($quantidade * $unitario < $desconto) {
            $this->addError('itemEdicao', 'O total do item não pode ficar menor que o desconto (R$ ' . number_format($desconto, 2, ',', '.') . ').');
            return;
        }

        if (!empty($this->vendaItens[$index]['fiscal'])) {
            $this->vendaItens[$index]['fiscal'] = (new ItemFiscalService())->reajustarParaNovoValor(
                $this->vendaItens[$index]['fiscal'],
                (float) $this->vendaItens[$index]['quantidade'] * (float) $this->vendaItens[$index]['unitario'],
                $quantidade * $unitario
            );
        }

        $this->vendaItens[$index]['quantidade'] = $quantidade;
        $this->vendaItens[$index]['unitario'] = $unitario;
        $this->vendaItens[$index]['total'] = ($quantidade * $unitario) - $desconto;

        $this->subtotal = array_sum(array_column($this->vendaItens, 'total'));
        $this->cancelarEdicaoItem();
    }
}
