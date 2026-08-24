<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFSeRegraIncidencia extends Model
{
    use HasFactory;

    protected $table = 'nfse_regras_incidencia';

    protected $fillable = [
        'cTribNac',
        'descricao',
        'li_estabelecimento_prestador',
        'li_local_prestacao',
        'li_estabelecimento_tomador',
        'li_estabelecimento_emitente',
        'exige_info_servico',
        'informacoes_complementares',
        'fonte_versao',
    ];

    protected $casts = [
        'li_estabelecimento_prestador' => 'boolean',
        'li_local_prestacao' => 'boolean',
        'li_estabelecimento_tomador' => 'boolean',
        'li_estabelecimento_emitente' => 'boolean',
        'exige_info_servico' => 'boolean',
    ];
}
