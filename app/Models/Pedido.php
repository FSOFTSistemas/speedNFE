<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'empresa_id', 'cliente_id', 'data', 'forma_pag_id', 'subtotal', 'total', 'desconto', 'numero_nfe', 'sequencia_evento', 'chave', 'estado', 'cfop_id'];

    public function itens(){
        return $this->hasMany(ItemPedido::class, 'pedido_id', 'id');
    }

    public function produtos(){
        return $this->hasManyThrough(Produto::class, ItemPedido::class, 'pedido_id', 'id', 'id', 'produto_id');
    }

    public function cfop(){
        return $this->hasOne(cfop::class, 'id', 'cfop_id');
    }

    public function endereco_cliente(){
        return $this->hasOneThrough(Endereco::class, Cliente::class, 'id', 'id', 'cliente_id', 'endereco_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    public function fatura(){
        return $this->hasMany(FaturaPedido::class, 'venda_id', 'id');
    }

}
