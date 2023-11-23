<?php

namespace App\Models;

use App\Enum\EstadoEnum;
use App\Enum\TipoCargaEnum;
use App\Enum\TipoDocumentoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDFE extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'serie',
        'data',
        'situacao',
        'uf_inicio',
        'uf_percurso',
        'tipo_documento',
        'chave_acesso',
        'valor_total',
        'peso',
        'carga_predominante',
        'ncm',
        'tipo_carga',
        'veiculo_tracao_id',
        'veiculo_reboque_id',
        'motoristaId'
    ];

    protected $casts = [
        'situacao' => EstadoEnum::class,
        'tipo_documento' => TipoDocumentoEnum::class,
        'tipo_carga' => TipoCargaEnum::class
    ];

}
