<?php

namespace App\Enums;

enum EstadoEnum:string {
    case PENDENTE = 'Pendente';
    case AUTORIZADO = 'Autorizado';
    case CANCELADO = 'Cancelado';
    case REJEITADO = 'Rejeitado';
    case ENCERRADO = 'Encerrado';
}
