<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\FormaPagamentoEnum;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCupomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['nullable', 'integer'],
            'empresa' => ['nullable', 'integer'],
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'troco' => ['nullable', 'numeric', 'min:0'],
            'enviar_agora' => ['nullable', 'boolean'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer', 'exists:produtos,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
            'itens.*.unitario' => ['required', 'numeric', 'min:0'],
            'itens.*.desconto' => ['nullable', 'numeric', 'min:0'],
            'itens.*.acrescimo' => ['nullable', 'numeric', 'min:0'],
            'formas' => ['required', 'array', 'min:1'],
            'formas.*' => ['numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'itens.required' => 'Deve existir pelo menos um item no cupom!',
            'formas.required' => 'Informe ao menos uma forma de pagamento!',
            'numeric' => 'O campo :attribute deve ser um valor numérico!',
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validarFormasDePagamento($validator);
        });
    }

    private function validarFormasDePagamento(Validator $validator): void
    {
        $formas = $this->input('formas', []);

        if (! is_array($formas)) {
            return;
        }

        $validas = array_map(fn ($case) => $case->value, FormaPagamentoEnum::cases());

        foreach (array_keys($formas) as $forma) {
            if (! in_array($forma, $validas, true)) {
                $validator->errors()->add('formas', "Forma de pagamento inválida: {$forma}. Valores aceitos: ".implode(', ', $validas));
            }
        }
    }
}
