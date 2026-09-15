<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFSeEvento extends Model
{
    use HasFactory;

    protected $table = 'nfse_eventos';

    protected $fillable = [
        'nfse_id',
        'tipo_evento',
        'codigo_evento',
        'sequencia',
        'situacao',
        'nProtocolo',
        'cStat',
        'xMotivo',
        'justificativa',
        'data_evento',
        'xml_pedido',
        'xml_retorno',
    ];

    protected $casts = [
        'data_evento' => 'datetime',
    ];

    public function nfse()
    {
        return $this->belongsTo(NFSe::class, 'nfse_id');
    }
}
