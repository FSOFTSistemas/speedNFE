<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContasARecebers extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id', 'empresa_id', 'pedido_id', 'total', 'status', 'valor_original', 'valor_pago', 'valor_atual', 'vencimento'];
}
