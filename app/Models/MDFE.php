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
        'codMunCarregamento',
        'municipioCarregamento',
        'tipo_documento',
        'chave_acesso',
        'valor_total',
        'peso',
        'tipo_carga',
        'info_fisco',
        'info_contribuinte',
        'numeroLacre',
        'empresa_id',
        'veiculo_tracao_id',
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

    public function reboques()
    {
        return $this->hasMany(MDFeReboque::class, 'mdfe_id');
    }

    public function motoristas()
    {
        return $this->hasMany(MDFeMotorista::class, 'mdfe_id');
    }

    public function notas()
    {
        return $this->hasMany(MDFeNota::class, 'mdfe_id');
    }

    public function prodPred()
    {
        return $this->hasOne(MDFeProdPred::class, 'mdfe_id');
    }
}
