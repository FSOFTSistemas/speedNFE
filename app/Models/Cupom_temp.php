<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom_temp extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'cliente_id', 'status', 'subtotal', 'desconto', 'total', 'empresa_id'];
}
