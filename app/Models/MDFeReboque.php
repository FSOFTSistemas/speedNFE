<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDFeReboque extends Model
{
    use HasFactory;

    protected $fillable = [
        'reboque_id',
        'mdfe_id'
    ];

    public function reboque()
    {
        return $this->belongsTo(Veiculo::class, 'reboque_id');
    }

}
