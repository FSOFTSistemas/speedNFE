<?php

namespace App\Enum;

enum EstadoEnum:string
{
    case PENDENTE = 'Pendente';
    case AUTORIZADO = 'Autorizado';
    case CANCELADO = 'Cancelado';
    case REJEITADO = 'Rejeitado';
}
