<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItensEntrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrada_id',
        'produto',
        'codbarra',
        'qtde',
        'unitario',
        'total',
        'NCM',
        'CST',
        'CFOP',
        'CSOSN',
        'IPI',
        'PIS',
        'COFINS',
        'ALIQUOTA',
        'empresa_id',
    ];
    
}
