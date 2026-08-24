<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFSeServicoNacional extends Model
{
    use HasFactory;

    protected $table = 'nfse_servicos_nacionais';

    protected $fillable = [
        'codigo',
        'item',
        'subitem',
        'desdobro',
        'descricao',
        'fonte_versao',
    ];
}
