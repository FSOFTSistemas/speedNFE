<?php

namespace App\Exceptions;

use Exception;

class TimeExceededException extends Exception
{
    protected $message = 'Prazo para cancelamento foi excedido';
    protected $code = 422;

    public function render()
    {
        return $this->message;
    }
}
