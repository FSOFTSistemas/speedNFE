<?php

namespace App\Exceptions;

use Exception;

class AlreadyExistException extends Exception
{
    protected $message = 'Item já existente';
    protected $code = 422;

    public function render()
    {
        return $this->message;
    }
}
