<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CupomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'empresa_id' => $this->empresa_id,
            'nroCupom' => $this->nroCupom,
            'data' => optional($this->data)->toISOString(),
            'situacao' => $this->situacao,
            'gerado_nfce' => $this->gerado_nfce,
            'subtotal' => $this->subtotal,
            'desconto' => $this->desconto,
            'acrescimo' => $this->acrescimo,
            'total' => $this->total,
            'troco' => $this->troco,
            'cliente_id' => $this->cliente_id,
            'cliente' => $this->whenLoaded('cliente', fn () => $this->cliente ? [
                'id' => $this->cliente->id,
                'nome' => $this->cliente->nome,
                'cpf_cnpj' => $this->cliente->cpf_cnpj,
            ] : null),
            'itens' => $this->whenLoaded('itens', fn () => $this->itens->map(fn ($item) => [
                'id' => $item->id,
                'produto_id' => $item->produto_id,
                'produto' => $item->relationLoaded('produto') && $item->produto
                    ? ['id' => $item->produto->id, 'produto' => $item->produto->produto, 'codigo' => $item->produto->codigo]
                    : null,
                'qtde' => $item->qtde,
                'unitario' => $item->unitario,
                'desconto' => $item->desconto,
                'acrescimo' => $item->acrescimo,
                'subtotal' => $item->subtotal,
                'total' => $item->total,
            ])),
            'formas_pagamento' => $this->whenLoaded('formasPagamento', fn () => $this->formasPagamento->map(fn ($forma) => [
                'forma' => $forma->forma,
                'valor' => $forma->valor,
            ])),
            'nfce' => $this->whenLoaded('nfce', fn () => $this->nfce ? [
                'id' => $this->nfce->id,
                'chave' => $this->nfce->chave,
                'situacao' => $this->nfce->situacao,
            ] : null),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
