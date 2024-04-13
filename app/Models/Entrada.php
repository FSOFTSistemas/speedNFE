<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataEmissao',
        'dataEntrada',
        'numeroNota',
        'fornecedor',
        'chave',
        'valor',
        'empresa_id'
    ];

    public function itensEntradas()
    {
        return $this->hasMany(ItensEntrada::class, 'entrada_id', 'id');
    }

}
