<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FaturaPedido extends Model
{
    use HasFactory;
    protected $fillable = ['valor', 'vencimento', 'venda_id', 'forma_pag_id', 'empresa_id'];

    public function forma_pag(){
        return $this->hasOne(FormaPag::class, 'id', 'forma_pag_id');
    }
}
