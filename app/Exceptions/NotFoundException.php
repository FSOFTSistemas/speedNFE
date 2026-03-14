<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Item não encontrado';
    protected $code = 404;

    public function render()
    {
        return $this->message;
    }
}
