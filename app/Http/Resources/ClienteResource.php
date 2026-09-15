<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'empresa_id' => $this->empresa_id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'apelido' => $this->apelido,
            'cpf_cnpj' => $this->cpf_cnpj,
            'rg_ie' => $this->rg_ie,
            'telefone' => $this->telefone,
            'celular' => $this->celular,
            'tipo' => $this->tipo,
            'situacao' => $this->situacao,
            'limite' => $this->limite,
            'contribuinte' => $this->contribuinte,
            'endereco' => $this->whenLoaded('endereco', fn () => [
                'id' => $this->endereco->id,
                'rua' => $this->endereco->rua,
                'numero' => $this->endereco->numero,
                'bairro' => $this->endereco->bairro,
                'cidade' => $this->endereco->cidade,
                'uf' => $this->endereco->uf,
                'ibge' => $this->endereco->codigoIBGE,
                'cep' => $this->endereco->cep,
                'complemento' => $this->endereco->complemento,
            ]),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
