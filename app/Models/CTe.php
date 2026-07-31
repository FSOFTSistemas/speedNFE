<?php

namespace App\Models;

use App\Enums\EstadoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CTe extends Model
{
    use HasFactory;

    protected $table = 'ctes';

    protected $fillable = [
        'numero',
        'serie',
        'data',
        'situacao',
        'chave',
        'nProtocolo',
        'empresa_id',
        'remetente_id',
        'destinatario_id',
        'toma',
        'veiculo_id',
        'motorista_id',
        'cfop',
        'uf_inicio',
        'mun_ini_codigo',
        'mun_ini_nome',
        'uf_fim',
        'mun_fim_codigo',
        'mun_fim_nome',
        'xProd',
        'qCarga',
        'vCarga',
        'vTPrest',
        'vRec',
        'picms',
        'info_fisco',
        'info_contribuinte',
        'xml',
    ];

    protected $casts = [
        'situacao' => EstadoEnum::class,
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function remetente()
    {
        return $this->belongsTo(Cliente::class, 'remetente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Cliente::class, 'destinatario_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function motorista()
    {
        return $this->belongsTo(Motorista::class, 'motorista_id');
    }

    public function documentos()
    {
        return $this->hasMany(CTeDocumento::class, 'cte_id');
    }
}
