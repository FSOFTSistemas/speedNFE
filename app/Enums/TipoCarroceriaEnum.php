<?php

namespace App\Enums;

enum TipoCarroceriaEnum:string
{
    case GRANELEIRA_03 = 'Graneleira';
    case BAUFECHADO_02 = 'Baú fechado';
    case PORTACONTAINER_04 = 'Porta container';
    case ABERTA_01 = 'Aberta';
    case NAOAPLICAVEL_00 = 'Não aplicável';
    case SIDER_05 = 'Sider';
}