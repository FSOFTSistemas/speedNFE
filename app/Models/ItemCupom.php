<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCupom extends Model
{
    use HasFactory;

    protected $fillable = [
        'qtde',
        'unitario',
        'desconto',
        'acrescimo',
        'total',
        'subtotal',
        'cupom_id',
        'produto_id'
    ];

    public function cupom()
    {
        return $this->belongsTo(Cupom::class, 'cupom_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

}
