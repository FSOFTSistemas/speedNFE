<?php

namespace App\Enum;

enum TipoRodadoEnum: string
{
    case CAVALO_MECANICO = 'Cavalo mecanico';
    case TOCO = 'Toco';
    case CAMINHAO = 'Caminhão';
    case UTILITARIO = 'Utilitário';
    case VAN = 'Van';
    case OUTROS = 'Outros';
}
