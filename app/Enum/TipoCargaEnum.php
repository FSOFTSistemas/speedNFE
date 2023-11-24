<?php

namespace App\Enum;

enum TipoCargaEnum:string
{
    case CARGAGERAL = 'Carga Geral';
    case GRANELSOLIDO = 'Granel sólido';
    case GRANELLIQUIDO = 'Granel líquido';
    case FRIGORIFICADA = 'Frigorificada';
    case CONTEINERIZADA = 'Conteinerizada';
    case NEOGRANEL = 'Neogranel';
}