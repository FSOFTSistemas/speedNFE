<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CartaCorrecaoPedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'justificativa' => ['required', 'string', 'min:15', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'justificativa.required' => 'Informe a justificativa da carta de correção.',
            'justificativa.min' => 'A justificativa deve ter pelo menos :min caracteres.',
        ];
    }
}
