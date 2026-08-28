<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConverterPreVendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino' => ['required', Rule::in(['NFE', 'NFCE'])],
            'cfop' => ['required_if:destino,NFE', 'nullable', 'integer', 'exists:cfops,id'],
            'finalidade' => ['nullable', 'integer', Rule::in([1, 4])],
            'tipo' => ['nullable', 'integer', Rule::in([0, 1])],
            'ref_nfe' => ['required_if:finalidade,4', 'nullable', 'string', 'max:50'],
            'info_complementares' => ['nullable', 'string', 'max:1500'],
            'aut_xml' => ['nullable', 'string', 'max:18'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('destino')) {
            $this->merge(['destino' => strtoupper((string) $this->input('destino'))]);
        }

        if ($this->has('aut_xml')) {
            $this->merge(['aut_xml' => preg_replace('/\D/', '', (string) $this->input('aut_xml'))]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $autXml = (string) $this->input('aut_xml', '');

            if ($autXml !== '' && ! in_array(strlen($autXml), [11, 14], true)) {
                $validator->errors()->add('aut_xml', 'O CPF/CNPJ autorizado deve ter 11 ou 14 dígitos.');
            }
        });
    }
}
