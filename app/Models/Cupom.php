<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    protected $fillable = [
        'nroCupom',
        'data',
        'situacao',
        'gerado_nfce',
        'contingencia',
        'total',
        'desconto',
        'acrescimo',
        'troco',
        'subtotal',
        'cliente_id',
        'empresa_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemCupom::class, 'cupom_id');
    }

    public function formasPagamento()
    {
        return $this->hasMany(CupomForma::class, 'cupom_id');
    }

    public function nfce()
    {
        return $this->hasOne(NFCe::class, 'cupom_id');
    }

}
