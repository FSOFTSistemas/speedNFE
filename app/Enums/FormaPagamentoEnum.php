<?php

namespace App\Enums;

enum FormaPagamentoEnum:string {
    case DINHEIRO = 'DINHEIRO';
    case CREDITO = 'CARTÃO/CRÉDITO';
    case DEBITO = 'CARTÃO/DÉBITO';
    case PIX = 'PIX';
    case BOLETO = 'BOLETO';
}
