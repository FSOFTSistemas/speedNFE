<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    protected $fillable = ['pedido_id', 'produto_id', 'qtde', 'empresa_id', 'desconto', 'acrescimo', 'unitario'];

    public function produto(){
        return $this->hasOne(Produto::class, 'id', 'produto_id');
    }

    public function cfop(){
        return $this->hasOneThrough(cfop::class, Pedido::class, 'id', 'id', 'pedido_id', 'cfop_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    use HasFactory;
}
