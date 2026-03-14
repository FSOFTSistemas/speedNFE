<?php

namespace App\Enums;

enum SituacaoEnum:string
{
    case ATIVO = "ATIVO";
    case CANCELADO = "CANCELADO";
    case REJEITADO = "REJEITADO";
}
