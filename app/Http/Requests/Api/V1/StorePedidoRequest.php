<?php

namespace App\Http\Requests\Api\V1;

use App\Services\PedidosService;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->pedidoRules();
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'vendaItens.required' => 'Deve existir pelo menos um item no pedido!',
            'numeric' => 'O campo :attribute deve ser um valor numérico!',
            'max' => 'O campo :attribute deve conter no máximo :max caracteres',
            'vendaItens.*.dfe_referenciado_chave.required' => 'Informe a chave da NF-e de origem em cada item da devolução.',
            'vendaItens.*.dfe_referenciado_chave.digits' => 'A chave da NF-e de origem deve conter 44 dígitos.',
            'vendaItens.*.dfe_referenciado_n_item.required' => 'Informe o número do item correspondente na NF-e de origem.',
            'vendaItens.*.dfe_referenciado_n_item.integer' => 'O número do item da NF-e de origem deve ser inteiro.',
            'vendaItens.*.dfe_referenciado_n_item.between' => 'O número do item da NF-e de origem deve estar entre 1 e 990.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (array_key_exists('aut_xml', $data) && $data['aut_xml'] !== null) {
            $data['aut_xml'] = preg_replace('/\D/', '', (string) $data['aut_xml']);
        }

        if (array_key_exists('vendaItens', $data) && is_array($data['vendaItens'])) {
            foreach ($data['vendaItens'] as &$item) {
                if (is_array($item) && array_key_exists('dfe_referenciado_chave', $item)) {
                    $item['dfe_referenciado_chave'] = preg_replace('/\D/', '', (string) $item['dfe_referenciado_chave']);
                }
            }
            unset($item);
        }

        $this->merge($data);
    }

    protected function pedidoRules(): array
    {
        $isDevolucao = (int) $this->input('finalidade') === 4;
        $usarReferenciaPorItem = $isDevolucao && PedidosService::referenciaItemDevolucaoHabilitada();
        $referenciaRequired = $usarReferenciaPorItem ? 'required' : 'nullable';

        return [
            'empresa_id' => ['nullable', 'integer'],
            'empresa' => ['nullable', 'integer'],
            'finalidade' => ['required', 'numeric'],
            'tipo' => ['required', 'numeric'],
            'ref_nfe' => [$isDevolucao && ! $usarReferenciaPorItem ? 'required' : 'nullable', 'string'],
            'cliente' => ['required', 'integer', 'exists:clientes,id'],
            'cfop' => ['required', 'integer', 'exists:cfops,id'],
            'info_complementares' => ['nullable', 'string', 'max:255'],
            'aut_xml' => ['nullable', 'string'],
            'vendaItens' => ['required', 'array', 'min:1'],
            'vendaItens.*.produto_id' => ['required', 'integer', 'exists:produtos,id'],
            'vendaItens.*.quantidade' => ['required', 'numeric', 'min:0.001'],
            'vendaItens.*.unitario' => ['required', 'numeric', 'min:0'],
            'vendaItens.*.desconto' => ['nullable', 'numeric', 'min:0'],
            'vendaItens.*.dfe_referenciado_chave' => [$referenciaRequired, 'digits:44'],
            'vendaItens.*.dfe_referenciado_n_item' => [$referenciaRequired, 'integer', 'between:1,990'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validarAutXml($validator);
            $this->validarReferenciasDuplicadas($validator);
        });
    }

    private function validarAutXml(Validator $validator): void
    {
        $autXml = (string) $this->input('aut_xml', '');

        if ($autXml !== '' && ! in_array(strlen($autXml), [11, 14], true)) {
            $validator->errors()->add('aut_xml', 'CPF/CNPJ autorizado para XML deve ter 11 ou 14 dígitos.');
        }
    }

    private function validarReferenciasDuplicadas(Validator $validator): void
    {
        $isDevolucao = (int) $this->input('finalidade') === 4;
        $usarReferenciaPorItem = $isDevolucao && PedidosService::referenciaItemDevolucaoHabilitada();

        if (! $usarReferenciaPorItem) {
            return;
        }

        $referencias = [];

        foreach ($this->input('vendaItens', []) as $index => $item) {
            $referencia = ($item['dfe_referenciado_chave'] ?? '').':'.($item['dfe_referenciado_n_item'] ?? '');

            if (isset($referencias[$referencia])) {
                $validator->errors()->add(
                    "vendaItens.$index.dfe_referenciado_n_item",
                    'A mesma chave e o mesmo item da NF-e de origem foram informados mais de uma vez.'
                );

                continue;
            }

            $referencias[$referencia] = true;
        }
    }
}
