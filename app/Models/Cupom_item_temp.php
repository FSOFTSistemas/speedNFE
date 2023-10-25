<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom_item_temp extends Model
{
    use HasFactory;

    protected $fillable = ['pedido_id', 'produto_id', 'qtde', 'empresa_id'];
}
