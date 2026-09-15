<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoXml extends Model
{
    protected $fillable = ['pedido_id', 'tipo', 'xml'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
