<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StorePreVendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'data' => ['required', 'date'],
            'validade_at' => ['nullable', 'date', 'after_or_equal:data'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'desconto' => ['nullable', 'numeric', 'min:0'],
            'acrescimo' => ['nullable', 'numeric', 'min:0'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer', 'distinct', 'exists:produtos,id'],
            'itens.*.quantidade' => ['required', 'numeric', 'gt:0'],
            'itens.*.valor_unitario' => ['nullable', 'numeric', 'min:0'],
            'itens.*.desconto' => ['nullable', 'numeric', 'min:0'],
            'itens.*.acrescimo' => ['nullable', 'numeric', 'min:0'],
            'pagamentos' => ['nullable', 'array'],
            'pagamentos.*.forma_pag_id' => ['nullable', 'integer', 'exists:forma_pags,id'],
            'pagamentos.*.descricao' => ['required_without:pagamentos.*.forma_pag_id', 'nullable', 'string', 'max:255'],
            'pagamentos.*.valor' => ['required', 'numeric', 'gt:0'],
            'pagamentos.*.vencimento' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'itens.required' => 'Informe ao menos um item na pré-venda.',
            'itens.min' => 'Informe ao menos um item na pré-venda.',
            'itens.*.produto_id.distinct' => 'O mesmo produto não pode ser informado mais de uma vez.',
            'itens.*.quantidade.gt' => 'A quantidade deve ser maior que zero.',
            'pagamentos.*.valor.gt' => 'O valor do pagamento deve ser maior que zero.',
            'validade_at.after_or_equal' => 'A validade não pode ser anterior à data da pré-venda.',
        ];
    }
}
