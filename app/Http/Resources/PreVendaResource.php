<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PreVendaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'empresa_id' => $this->empresa_id,
            'cliente_id' => $this->cliente_id,
            'cliente_nome' => $this->cliente_nome,
            'cliente_documento' => $this->cliente_documento,
            'user_id' => $this->user_id,
            'data' => $this->data?->format('Y-m-d'),
            'validade_at' => $this->validade_at?->format('Y-m-d'),
            'status' => $this->statusEfetivo()->value,
            'subtotal' => (float) $this->subtotal,
            'desconto' => (float) $this->desconto,
            'acrescimo' => (float) $this->acrescimo,
            'total' => (float) $this->total,
            'observacoes' => $this->observacoes,
            'destino' => $this->destino?->value,
            'pedido_id' => $this->pedido_id,
            'cupom_id' => $this->cupom_id,
            'convertida_por' => $this->convertida_por,
            'convertida_at' => $this->convertida_at?->toISOString(),
            'cliente' => $this->whenLoaded('cliente', fn () => [
                'id' => $this->cliente?->id,
                'nome' => $this->cliente?->nome ?? $this->cliente_nome,
                'cpf_cnpj' => $this->cliente?->cpf_cnpj ?? $this->cliente_documento,
            ]),
            'empresa' => $this->whenLoaded('empresa', fn () => [
                'id' => $this->empresa?->id,
                'fantasia' => $this->empresa?->fantasia,
            ]),
            'itens' => $this->whenLoaded('itens', fn () => $this->itens->map(fn ($item) => [
                'id' => $item->id,
                'produto_id' => $item->produto_id,
                'codigo' => $item->codigo,
                'descricao' => $item->descricao,
                'unidade' => $item->unidade,
                'quantidade' => (float) $item->quantidade,
                'valor_unitario' => (float) $item->valor_unitario,
                'subtotal' => (float) $item->subtotal,
                'desconto' => (float) $item->desconto,
                'acrescimo' => (float) $item->acrescimo,
                'total' => (float) $item->total,
            ])),
            'pagamentos' => $this->whenLoaded('pagamentos', fn () => $this->pagamentos->map(fn ($pagamento) => [
                'id' => $pagamento->id,
                'forma_pag_id' => $pagamento->forma_pag_id,
                'descricao' => $pagamento->descricao,
                'valor' => (float) $pagamento->valor,
                'vencimento' => $pagamento->vencimento?->format('Y-m-d'),
            ])),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
