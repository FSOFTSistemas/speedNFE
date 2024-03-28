<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tabela_afetada',
        'acao',
        'dados_anteriores',
        'dados_atuais',
        'usuario_id',
    ];

}
