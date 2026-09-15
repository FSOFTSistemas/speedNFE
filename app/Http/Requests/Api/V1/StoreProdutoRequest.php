<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->produtoRules();
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (array_key_exists('ncm', $data) && ! empty($data['ncm'])) {
            $data['ncm'] = preg_replace('/\D/', '', $data['ncm']);
        }

        foreach (['codigo_barras', 'codigo', 'referencia', 'produto', 'descricao', 'un', 'unidade'] as $field) {
            if (array_key_exists($field, $data) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        $this->merge($data);
    }

    protected function produtoRules(?int $produtoId = null): array
    {
        return [
            'empresa_id' => ['nullable', 'integer'],
            'empresa' => ['nullable', 'integer'],
            'categoria_id' => ['nullable', 'integer'],
            'categoria' => ['nullable', 'integer'],
            'codigo' => ['nullable', 'string', 'max:255'],
            'codigo_barras' => ['nullable', 'string', 'max:255', Rule::unique('produtos', 'codigo_barras')->ignore($produtoId)],
            'referencia' => ['nullable', 'string', 'max:255'],
            'produto' => ['required_without:descricao', 'string', 'max:255'],
            'descricao' => ['required_without:produto', 'string', 'max:255'],
            'un' => ['nullable', 'string', 'max:20'],
            'unidade' => ['nullable', 'string', 'max:20'],
            'precocusto' => ['nullable', 'numeric', 'min:0'],
            'preco_custo' => ['nullable', 'numeric', 'min:0'],
            'precovenda' => ['nullable', 'numeric', 'min:0'],
            'preco_venda' => ['nullable', 'numeric', 'min:0'],
            'estoque' => ['nullable', 'numeric'],
            'estoque_minimo' => ['nullable', 'numeric'],
            'margem_lucro' => ['nullable', 'numeric'],
            'ativo' => ['nullable', 'boolean'],
            'ncm' => ['required', 'string', 'max:20'],
            'cest' => ['nullable', 'string', 'max:20'],
            'tpProd' => ['nullable', 'string', 'max:20'],
            'cfopinterno' => ['nullable', 'string', 'max:10'],
            'cfopexterno' => ['nullable', 'string', 'max:10'],
            'cfop' => ['nullable', 'string', 'max:10'],
            'cst' => ['nullable', 'string', 'max:10'],
            'cst_csosn' => ['nullable', 'string', 'max:10'],
            'csosn' => ['nullable', 'string', 'max:10'],
            'origem' => ['nullable', 'string', 'max:10'],
            'icms' => ['nullable', 'numeric'],
            'aliquota_icms' => ['nullable', 'numeric'],
            'pis' => ['nullable', 'numeric'],
            'aliquota_pis' => ['nullable', 'numeric'],
            'cofins' => ['nullable', 'numeric'],
            'aliquota_cofins' => ['nullable', 'numeric'],
            'ipi' => ['nullable', 'numeric'],
            'aliquota_ipi' => ['nullable', 'numeric'],
            'cst_pis' => ['nullable', 'string', 'max:10'],
            'cst_cofins' => ['nullable', 'string', 'max:10'],
            'cst_ipi' => ['nullable', 'string', 'max:10'],
            'cst_ibs_cbs' => ['nullable', 'string', 'max:10'],
            'cClassTrib' => ['nullable', 'string', 'max:20'],
            'pIBS' => ['nullable', 'numeric'],
            'pCBS' => ['nullable', 'numeric'],
            'pIS_imposto' => ['nullable', 'numeric'],
            'tpVeic' => ['nullable', 'string', 'max:20'],
            'chassiVeic' => ['nullable', 'string', 'max:255'],
            'renavanVeic' => ['nullable', 'string', 'max:30'],
            'anoFabVeic' => ['nullable', 'integer', 'digits:4'],
            'anoModVeic' => ['nullable', 'integer', 'digits:4'],
            'pesoLVeic' => ['nullable', 'numeric', 'min:0'],
            'pesoBVeic' => ['nullable', 'numeric', 'min:0'],
            'distVeic' => ['nullable', 'string', 'max:255'],
            'combVeic' => ['nullable', 'string', 'max:255'],
            'nMotorVeic' => ['nullable', 'string', 'max:255'],
            'cvVeic' => ['nullable', 'string', 'max:255'],
            'cm3Veic' => ['nullable', 'string', 'max:255'],
            'serieVeic' => ['nullable', 'string', 'max:255'],
            'tpPVeic' => ['nullable', 'string', 'max:255'],
            'corVeic' => ['nullable', 'string', 'max:255'],
            'cCorVeic' => ['nullable', 'string', 'max:255'],
            'cCorMontVeic' => ['nullable', 'string', 'max:255'],
            'cMarcaVeic' => ['nullable', 'string', 'max:255'],
            'condVeic' => ['nullable', 'integer'],
            'espVeic' => ['nullable', 'string', 'max:255'],
            'vinVeic' => ['nullable', 'string', 'max:255'],
            'lotVeic' => ['nullable', 'string', 'max:255'],
            'restriVeic' => ['nullable', 'integer'],
            'cargaVeic' => ['nullable', 'integer'],
            'operVeic' => ['nullable', 'string', 'max:255'],
        ];
    }
}
