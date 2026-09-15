<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatLon extends Model
{
    use HasFactory;

    protected $fillable = [
        'uf',
        'municipio',
        'lon',
        'lat'
    ];

}
