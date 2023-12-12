<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDFeMotorista extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorista_id',
        'mdfe_id'
    ];

    public function motorista()
    {
        return $this->hasOne(Motorista::class, 'motorista_id');
    }
}
