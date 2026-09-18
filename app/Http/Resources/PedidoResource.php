<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'empresa_id' => $this->empresa_id,
            'cliente_id' => $this->cliente_id,
            'cliente' => $this->whenLoaded('cliente', fn () => [
                'id' => $this->cliente->id,
                'nome' => $this->cliente->nome,
                'cpf_cnpj' => $this->cliente->cpf_cnpj,
            ]),
            'data' => optional($this->data)->toDateString(),
            'subtotal' => $this->subtotal,
            'desconto' => $this->desconto,
            'total' => $this->total,
            'finNF' => $this->finNF,
            'tpNF' => $this->tpNF,
            'cfop' => $this->cfop,
            'estado' => $this->estado?->value,
            'chave' => $this->chave,
            'numero_nfe' => $this->numero_nfe,
            'ref_nfe' => $this->ref_nfe,
            'aut_xml' => $this->aut_xml,
            'info_complementares' => $this->info_complementares,
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
                'dfe_referenciado_chave' => $item->dfe_referenciado_chave,
                'dfe_referenciado_n_item' => $item->dfe_referenciado_n_item,
            ])),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
