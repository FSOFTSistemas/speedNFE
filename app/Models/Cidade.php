<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'cidade',
        'uf',
        'cep',
        'ibge',
        'estado',
        'municipio'
    ];
}
