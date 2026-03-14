<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CstIbsCbs extends Model
{
    protected $table = 'cst_ibs_cbs';
    protected $fillable = ['codigo', 'descricao'];
}