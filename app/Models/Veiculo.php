<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'capacidade',
        'renavan',
        'tara',
        'capacidade_m3',
        'tipo_carroceria',
        'tipo_veiculo',
        'tipo_rodado',
        'uf_veiculo',
        'tipo_propriedade',
    ];
}
