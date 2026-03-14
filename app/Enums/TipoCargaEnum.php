<?php

namespace App\Enums;

enum TipoCargaEnum:string
{
    case CARGAGERAL_05 = 'Carga Geral';
    case GRANELSOLIDO_01 = 'Granel sólido';
    case GRANELLIQUIDO_02 = 'Granel líquido';
    case FRIGORIFICADA_03 = 'Frigorificada';
    case CONTEINERIZADA_04 = 'Conteinerizada';
    case NEOGRANEL_06 = 'Neogranel';
}