<?php

namespace App\Enums;

enum PreVendaStatusEnum: string
{
    case ABERTA = 'ABERTA';
    case CONVERTIDA = 'CONVERTIDA';
    case CANCELADA = 'CANCELADA';
    case EXPIRADA = 'EXPIRADA';
}
