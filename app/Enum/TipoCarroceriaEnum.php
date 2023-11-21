<?php

namespace App\Enum;

enum TipoCarriceria:string
{
    case GRANELEIRA = 'GRANELEIRA';
    case BAU_FECHADO = 'BAU_FECHADO';
    case PORTA_CONTAINER = 'PORTA_CONTAINER';
    case ABERTA = 'ABERTA';
    case NAO_APLICAVEL = 'NAO_APLICAVEL';
    case SIDER = 'SIDER';
}