<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFSeXml extends Model
{
    use HasFactory;

    protected $table = 'nfse_xmls';

    protected $fillable = [
        'nfse_id',
        'tipo',
        'xml',
    ];

    public function nfse()
    {
        return $this->belongsTo(NFSe::class, 'nfse_id');
    }
}
