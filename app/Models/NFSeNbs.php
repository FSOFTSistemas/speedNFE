<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFSeNbs extends Model
{
    use HasFactory;

    protected $table = 'nfse_nbs';

    protected $fillable = [
        'codigo',
        'descricao',
        'fonte_versao',
    ];
}
