<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InutilizarNumeracaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['nullable', 'integer'],
            'numero_inicial' => ['required', 'integer', 'min:1'],
            'numero_final' => ['required', 'integer', 'min:1'],
            'justificativa' => ['required', 'string', 'min:15', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'justificativa.min' => 'A justificativa deve ter pelo menos :min caracteres.',
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ((int) $this->input('numero_final') < (int) $this->input('numero_inicial')) {
                $validator->errors()->add('numero_final', 'O número final deve ser maior ou igual ao número inicial.');
            }
        });
    }
}
