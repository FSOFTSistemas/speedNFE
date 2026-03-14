<?php

namespace App\Models;

use App\Enums\TipoDocumentoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDFeNota extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_documento',
        'chave',
        'uf',
        'municipio',
        'codMun',
        'valor',
        'peso',
        'serie',
        'numero',
        'mdfe_id'
    ];

    protected $cast = [
        'tipo_documento' => TipoDocumentoEnum::class
    ];

}
