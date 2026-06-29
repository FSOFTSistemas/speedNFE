<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->clienteRules();
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        foreach (['cpf_cnpj', 'telefone', 'celular', 'cep'] as $field) {
            if (array_key_exists($field, $data) && ! empty($data[$field])) {
                $data[$field] = preg_replace('/\D/', '', $data[$field]);
            }
        }

        foreach ($data as $field => $value) {
            if (is_string($value)) {
                $data[$field] = trim($value);
            }
        }

        if (array_key_exists('uf', $data) && ! empty($data['uf'])) {
            $data['uf'] = strtoupper($data['uf']);
        }

        $this->merge($data);
    }

    protected function clienteRules(?int $clienteId = null): array
    {
        return [
            'empresa_id' => ['nullable', 'integer'],
            'empresa' => ['nullable', 'integer'],
            'codigo' => ['required', 'string', 'max:255'],
            'nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'cpf_cnpj' => ['required', 'string', Rule::unique('clientes', 'cpf_cnpj')->ignore($clienteId)],
            'rg_ie' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:255'],
            'celular' => ['nullable', 'string', 'max:255'],
            'limite' => ['required', 'numeric'],
            'situacao' => ['nullable'],
            'rua' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'uf' => ['required', 'string', 'max:2'],
            'ibge' => ['required', 'string', 'max:20'],
            'cep' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
        ];
    }
}
