<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFCe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nro',
        'data',
        'serie',
        'chave',
        'contingencia',
        'situacao',
        'xml',
        'cupom_id',
        'empresa_id'
    ];

    public function cupom()
    {
        return $this->belongsTo(Cupom::class, 'cupom_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

}
