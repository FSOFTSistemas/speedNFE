<?php

namespace App\Http\Requests\Api\V1;

class UpdateProdutoRequest extends StoreProdutoRequest
{
    public function rules(): array
    {
        return $this->produtoRules((int) $this->route('produto'));
    }
}
