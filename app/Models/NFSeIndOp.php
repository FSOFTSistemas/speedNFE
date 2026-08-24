<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFSeIndOp extends Model
{
    use HasFactory;

    protected $table = 'nfse_ind_ops';

    protected $fillable = [
        'codigo',
        'artigo',
        'tipo_operacao',
        'local_operacao',
        'caracteristica_fornecimento',
        'grupo',
        'subgrupo',
        'local_fornecimento',
        'campo_layout',
        'fonte_versao',
    ];
}
