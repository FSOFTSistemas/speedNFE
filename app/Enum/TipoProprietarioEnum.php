<?php

namespace App\Enum;

enum TipoProprietarioEnum:string
{
    case TACAGREGADO = 'TAC agregado';
    case TACINDEPENDENTE = 'TAC independente';
    case OUTROS = 'Outros';
}