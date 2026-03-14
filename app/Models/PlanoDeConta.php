<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoDeConta extends Model
{
    use HasFactory;

    protected $table = 'plano_de_contas';

    protected $fillable = [
        'codigo',
        'descricao',
        'tipo',
        'conta_pai_id',
        'empresa_id',
    ];

    public function contaPai()
    {
        return $this->belongsTo(PlanoDeConta::class, 'conta_pai_id');
    }

    public function subContas()
    {
        return $this->hasMany(PlanoDeConta::class, 'conta_pai_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function lancamentos()
    {
        return $this->hasMany(FluxoDeCaixa::class, 'plano_de_contas_id');
    }
}
