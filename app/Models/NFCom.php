<?php

namespace App\Models;

use App\Enums\EstadoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFCom extends Model
{
    use HasFactory;

    protected $table = 'nfcoms';

    protected $fillable = [
        'nro',
        'data',
        'serie',
        'chave',
        'situacao',
        'nProtocolo',
        'cliente_id',
        'empresa_id',
        'iCodAssinante',
        'tpAssinante',
        'tpServUtil',
        'nContrato',
        'competFat',
        'dVencFat',
        'dPerUsoIni',
        'dPerUsoFim',
        'codBarras',
        'vProd',
        'vDesc',
        'vNF',
        'xml',
    ];

    protected $casts = [
        'situacao' => EstadoEnum::class,
        'data' => 'datetime',
        'dVencFat' => 'date',
        'dPerUsoIni' => 'date',
        'dPerUsoFim' => 'date',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function itens()
    {
        return $this->hasMany(NFComItem::class, 'nfcom_id');
    }
}
