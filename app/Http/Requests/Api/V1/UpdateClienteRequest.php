<?php

namespace App\Http\Requests\Api\V1;

class UpdateClienteRequest extends StoreClienteRequest
{
    public function rules(): array
    {
        return $this->clienteRules((int) $this->route('cliente'));
    }
}
