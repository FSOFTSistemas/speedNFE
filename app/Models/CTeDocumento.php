<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CTeDocumento extends Model
{
    use HasFactory;

    protected $table = 'cte_documentos';

    protected $fillable = [
        'cte_id',
        'tipo_documento',
        'chave',
    ];

    public function cte()
    {
        return $this->belongsTo(CTe::class, 'cte_id');
    }
}
