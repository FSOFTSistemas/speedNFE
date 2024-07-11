<?php

namespace App\Exceptions;

use Exception;

class LimitExceededException extends Exception
{
    protected $message = 'Limite foi atingido';
    protected $code = 422;

    public function render()
    {
        return $this->message;
    }
}
