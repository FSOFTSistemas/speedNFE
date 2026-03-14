<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItensEntrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrada_id',
        'produto_id',
        'qtde',
        'empresa_id',
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'entrada_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

}
