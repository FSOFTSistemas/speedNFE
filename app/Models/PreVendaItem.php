<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreVendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_venda_id',
        'produto_id',
        'codigo',
        'descricao',
        'unidade',
        'quantidade',
        'valor_unitario',
        'subtotal',
        'desconto',
        'acrescimo',
        'total',
    ];

    protected $casts = [
        'quantidade' => 'decimal:4',
        'valor_unitario' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'desconto' => 'decimal:2',
        'acrescimo' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function preVenda()
    {
        return $this->belongsTo(PreVenda::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
