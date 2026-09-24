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

    /** O projeto não tem tradução pt-br do validator; mensagens do cadastro de produto em português. */
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'required_without' => 'O campo :attribute é obrigatório.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'min' => 'O campo :attribute não pode ser negativo.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'exists' => 'O valor informado em :attribute não existe.',
            'unique' => 'Já existe um produto com este :attribute.',
        ];
    }

    public function attributes(): array
    {
        return [
            'categoria_id' => 'categoria',
            'categoria' => 'categoria',
            'produto' => 'nome do produto',
            'descricao' => 'nome do produto',
            'codigo' => 'código de barras',
            'codigo_barras' => 'código de barras',
            'un' => 'unidade',
            'precocusto' => 'preço de custo',
            'precovenda' => 'preço de venda',
            'ncm' => 'NCM',
            'cfopinterno' => 'CFOP interno',
            'cfopexterno' => 'CFOP externo',
            'cst_csosn' => 'CST/CSOSN',
            'cst_pis' => 'CST PIS',
            'cst_cofins' => 'CST COFINS',
            'icms' => 'ICMS',
            'pis' => 'PIS',
            'cofins' => 'COFINS',
            'ipi' => 'IPI',
        ];
    }

    protected function produtoRules(?int $produtoId = null): array
    {
        return [
            'empresa_id' => ['nullable', 'integer'],
            'empresa' => ['nullable', 'integer'],
            'categoria_id' => ['required_without:categoria', 'nullable', 'integer', Rule::exists('categorias', 'id')],
            'categoria' => ['required_without:categoria_id', 'nullable', 'integer', Rule::exists('categorias', 'id')],
            'codigo' => ['required', 'string', 'max:255'],
            'codigo_barras' => ['nullable', 'string', 'max:255', Rule::unique('produtos', 'codigo_barras')->ignore($produtoId)],
            'referencia' => ['nullable', 'string', 'max:255'],
            'produto' => ['required_without:descricao', 'string', 'max:255'],
            'descricao' => ['required_without:produto', 'string', 'max:255'],
            'un' => ['required_without:unidade', 'nullable', 'string', 'max:20'],
            'unidade' => ['nullable', 'string', 'max:20'],
            'precocusto' => ['required_without:preco_custo', 'nullable', 'numeric', 'min:0'],
            'preco_custo' => ['nullable', 'numeric', 'min:0'],
            'precovenda' => ['required_without:preco_venda', 'nullable', 'numeric', 'min:0'],
            'preco_venda' => ['nullable', 'numeric', 'min:0'],
            'estoque' => ['nullable', 'numeric'],
            'estoque_minimo' => ['nullable', 'numeric'],
            'margem_lucro' => ['nullable', 'numeric'],
            'ativo' => ['nullable', 'boolean'],
            'ncm' => ['required', 'string', 'max:20'],
            'cest' => ['nullable', 'string', 'max:20'],
            'tpProd' => ['nullable', 'string', 'max:20'],
            'cfopinterno' => ['required_without:cfop', 'nullable', 'string', 'max:10'],
            'cfopexterno' => ['required', 'string', 'max:10'],
            'cfop' => ['nullable', 'string', 'max:10'],
            'cst' => ['nullable', 'string', 'max:10'],
            'cst_csosn' => ['required_without:csosn', 'nullable', 'string', 'max:10'],
            'csosn' => ['nullable', 'string', 'max:10'],
            'origem' => ['nullable', 'string', 'max:10'],
            'icms' => ['required_without:aliquota_icms', 'nullable', 'numeric'],
            'aliquota_icms' => ['nullable', 'numeric'],
            'pis' => ['required_without:aliquota_pis', 'nullable', 'numeric'],
            'aliquota_pis' => ['nullable', 'numeric'],
            'cofins' => ['required_without:aliquota_cofins', 'nullable', 'numeric'],
            'aliquota_cofins' => ['nullable', 'numeric'],
            'ipi' => ['required_without:aliquota_ipi', 'nullable', 'numeric'],
            'aliquota_ipi' => ['nullable', 'numeric'],
            'cst_pis' => ['required', 'string', 'max:10'],
            'cst_cofins' => ['required', 'string', 'max:10'],
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
