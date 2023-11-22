<?php

namespace App\Enum;

enum TipoCarroceriaEnum:string
{
    case GRANELEIRA = 'Graneleira';
    case BAU_FECHADO = 'Baú fechado';
    case PORTA_CONTAINER = 'Porta container';
    case ABERTA = 'Aberta';
    case NAO_APLICAVEL = 'Não aplicável';
    case SIDER = 'Sider';
}