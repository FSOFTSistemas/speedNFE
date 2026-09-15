<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CupomForma extends Model
{
    use HasFactory;

    protected $fillable = [
        'forma',
        'valor',
        'cupom_id'
    ];

    public function cupom()
    {
        return $this->belongsTo(Cupom::class, 'cupom_id');
    }

}
