<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDFeProdPred extends Model
{
    use HasFactory;

    protected $fillable = [
        'carga_predominante',
        'ncm',
        'codigo_gtin',
        'lat_carrregamento',
        'lon_carrregamento',
        'lat_descarrregamento',
        'lon_descarrregamento'
    ];
}
