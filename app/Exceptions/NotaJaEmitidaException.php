<?php

namespace App\Exceptions;

use Exception;

class NotaJaEmitidaException extends Exception
{
    protected $message = 'Já foi emitida a NFe dessa venda, não é possível realizar alterações.';

    protected $code = 422;

    public function render()
    {
        return $this->message;
    }
}
