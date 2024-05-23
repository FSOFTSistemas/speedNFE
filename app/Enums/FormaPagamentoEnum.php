<?php

namespace App\Enums;

enum FormaPagamentoEnum:string {
    case DINHEIRO = 'DINHEIRO';
    case PIX = 'PIX';
    case CREDITO = 'CARTÃO/CRÉDITO';
    case DEBITO = 'CARTÃO/DÉBITO';
}
