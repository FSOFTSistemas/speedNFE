<?php

namespace App\Exceptions;

use Exception;

class MalformedXmlException extends Exception
{
    protected $message = 'Erro na formação do xml';
    protected $code = 422;

    public function render()
    {
        return $this->message;
    }
}
