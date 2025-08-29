<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoDeCaixa extends Model
{
    use HasFactory;

    protected $table = 'fluxo_de_caixas';

    protected $fillable = [
        'data',
        'descricao',
        'valor',
        'tipo',
        'plano_de_contas_id',
        'empresa_id',
    ];

    public function planoDeContas()
    {
        return $this->belongsTo(PlanoDeConta::class, 'plano_de_contas_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
