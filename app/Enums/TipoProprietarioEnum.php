<?php

namespace App\Enums;

enum TipoProprietarioEnum:string
{
    case TACAGREGADO_0 = 'TAC agregado';
    case TACINDEPENDENTE_1 = 'TAC independente';
    case OUTROS_2 = 'Outros';
}