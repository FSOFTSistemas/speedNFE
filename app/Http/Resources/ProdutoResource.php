<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'empresa_id' => $this->empresa_id,
            'categoria_id' => $this->categoria_id,
            'codigo' => $this->codigo,
            'produto' => $this->produto,
            'descricao' => $this->produto,
            'ncm' => $this->ncm,
            'cfop_interno' => $this->cfop_interno,
            'cfop_externo' => $this->cfop_externo,
            'cst_csosn' => $this->cst_csosn,
            'cst_pis' => $this->cst_pis,
            'cst_cofins' => $this->cst_cofins,
            'cst' => $this->cst,
            'icms' => $this->icms,
            'pis' => $this->pis,
            'cofins' => $this->cofins,
            'ipi' => $this->ipi,
            'un' => $this->un,
            'precocusto' => $this->precocusto,
            'precovenda' => $this->precovenda,
            'tpProd' => $this->tpProd,
            'cClassTrib' => $this->cClassTrib,
            'pIBS' => $this->pIBS,
            'pCBS' => $this->pCBS,
            'pIS_imposto' => $this->pIS_imposto,
            'cst_ibs_cbs' => $this->cst_ibs_cbs,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
