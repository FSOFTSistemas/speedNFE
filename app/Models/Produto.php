<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'produto',
        'precocusto',
        'precovenda',
        'ncm',
        'cfop_interno',
        'cst_csosn',
        'cst_pis',
        'cst_cofins',
        'cst',
        'icms',
        'pis',
        'cofins',
        'ipi',
        'cfop_externo',
        'un',
        'empresa_id',
        'categoria_id',
        'tpProd',
        'tpVeic',
        'chassiVeic',
        'renavanVeic',
        'anoFabVeic',
        'anoModVeic',
        'pesoLVeic',
        'pesoBVeic',
        'distVeic',
        'combVeic',
        'nMotorVeic',
        'cvVeic',
        'cm3Veic',
        'serieVeic',
        'tpPVeic',
        'corVeic',
        'cCorVeic',
        'cCorMontVeic',
        'cMarcaVeic',
        'condVeic',
        'espVeic',
        'vinVeic',
        'lotVeic',
        'restriVeic',
        'cargaVeic',
        'operVeic'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function estoque()
    {
        return $this->hasOne(Estoque::class, 'produto_id');
    }

}
