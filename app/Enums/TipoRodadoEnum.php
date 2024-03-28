<?php

namespace App\Enums;

enum TipoRodadoEnum: string
{
    case CAVALOMECANICO_03 = 'Cavalo mecanico';
    case TOCO_02 = 'Toco';
    case CAMINHAO_01 = 'Caminhão';
    case UTILITARIO_05 = 'Utilitário';
    case VAN_04 = 'Van';
    case OUTROS_06 = 'Outros';
}
