<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'razao' => $this->razao,
            'fantasia' => $this->fantasia,
            'cpf_cnpj' => $this->cpf_cnpj,
            'rg_ie' => $this->rg_ie,
            'inscricao_municipal' => $this->inscricao_municipal,
            'celular' => $this->celular,
            'contador' => $this->contador,
            'ambiente' => $this->ambiente,
            'status' => $this->status,
            'crt' => $this->crt,
            'limites' => [
                'clientes' => $this->limClientes,
                'produtos' => $this->limProdutos,
                'nfe' => $this->limNFes,
                'nfce' => $this->limNFCes,
                'nfse' => $this->limNFSe,
                'mdfe' => $this->limMDFes,
            ],
            'endereco' => $this->whenLoaded('endereco', fn () => $this->endereco ? [
                'rua' => $this->endereco->rua,
                'numero' => $this->endereco->numero,
                'bairro' => $this->endereco->bairro,
                'complemento' => $this->endereco->complemento,
                'cidade' => $this->endereco->cidade,
                'uf' => $this->endereco->uf,
                'cep' => $this->endereco->cep,
                'codigoIBGE' => $this->endereco->codigoIBGE,
            ] : null),
        ];
    }
}
