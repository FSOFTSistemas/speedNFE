<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CClassTrib extends Model
{
    protected $fillable = ['codigo', 'descricao', 'cst_compativel'];
    
    // Relacionamento opcional, caso queira usar
    public function cst()
    {
        return $this->belongsTo(CstIbsCbs::class, 'cst_compativel', 'codigo');
    }
}