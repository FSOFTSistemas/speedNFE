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
        'uf_termino',
        'uf_percurso',
        'tipo_documento',
        'chave_acesso',
        'valor_total',
        'peso',
        'carga_predominante',
        'ncm',
        'tipo_carga',
        'empresa_id',
        'veiculo_tracao_id',
        'veiculo_reboque_id',
        'motoristaId'
    ];

    protected $casts = [
        'situacao' => EstadoEnum::class,
        'tipo_documento' => TipoDocumentoEnum::class,
        'tipo_carga' => TipoCargaEnum::class
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function veiculoTracao()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_tracao_id');
    }

    public function veiculoReboque()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_reboque_id');
    }

    public function motorista()
    {
        return $this->belongsTo(Motorista::class, 'motoristaId');
    }

    public function notas()
    {
        return $this->hasMany(MDFeNota::class, 'mdfe_id');
    }
}
