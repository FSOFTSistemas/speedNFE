<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFSe extends Model
{
    use HasFactory;

    protected $table = 'nfses';

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'servico_id',
        'nro',
        'serie',
        'nDPS',
        'serieDPS',
        'chave',
        'codigo_verificacao',
        'nDFSe',
        'nProtocolo',
        'situacao',
        'cStat',
        'xMotivo',
        'tpAmb',
        'tpEmit',
        'data_competencia',
        'data_emissao',
        'data_processamento',
        'cLocEmi',
        'cLocPrestacao',
        'cLocIncid',
        'cTribNac',
        'cTribMun',
        'cNBS',
        'cIndOp',
        'cClassTrib',
        'vServ',
        'vDescIncond',
        'vDescCond',
        'vDeducaoReducao',
        'vBC',
        'pAliq',
        'vISSQN',
        'vTotalRet',
        'vLiq',
        'vIBS',
        'vCBS',
        'vTotNF',
        'discriminacao',
        'informacoes_complementares',
    ];

    protected $casts = [
        'data_competencia' => 'date',
        'data_emissao' => 'datetime',
        'data_processamento' => 'datetime',
        'vServ' => 'decimal:2',
        'vDescIncond' => 'decimal:2',
        'vDescCond' => 'decimal:2',
        'vDeducaoReducao' => 'decimal:2',
        'vBC' => 'decimal:2',
        'pAliq' => 'decimal:4',
        'vISSQN' => 'decimal:2',
        'vTotalRet' => 'decimal:2',
        'vLiq' => 'decimal:2',
        'vIBS' => 'decimal:2',
        'vCBS' => 'decimal:2',
        'vTotNF' => 'decimal:2',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    public function xmls()
    {
        return $this->hasMany(NFSeXml::class, 'nfse_id');
    }

    public function eventos()
    {
        return $this->hasMany(NFSeEvento::class, 'nfse_id');
    }

    public function xmlDps()
    {
        return $this->hasOne(NFSeXml::class, 'nfse_id')->where('tipo', 'dps');
    }

    public function xmlAutorizado()
    {
        return $this->hasOne(NFSeXml::class, 'nfse_id')->where('tipo', 'autorizado');
    }
}
