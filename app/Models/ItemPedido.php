<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    protected $fillable = [
        'pedido_id',
        'produto_id',
        'qtde',
        'empresa_id',
        'desconto',
        'acrescimo',
        'unitario',
        'dfe_referenciado_chave',
        'dfe_referenciado_n_item',
        'fiscal_personalizado',
        'cfop_item',
        'cst_csosn',
        'icms_reducao',
        'icms_base',
        'icms_aliquota',
        'icms_valor',
        'icms_st_mva',
        'icms_st_base',
        'icms_st_aliquota',
        'icms_st_valor',
        'cst_pis',
        'pis_base',
        'pis_aliquota',
        'pis_valor',
        'cst_cofins',
        'cofins_base',
        'cofins_aliquota',
        'cofins_valor',
    ];

    protected $casts = [
        'fiscal_personalizado' => 'boolean',
    ];

    public function produto(){
        return $this->hasOne(Produto::class, 'id', 'produto_id');
    }

    public function cfop(){
        return $this->hasOneThrough(cfop::class, Pedido::class, 'id', 'id', 'pedido_id', 'cfop_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    use HasFactory;
}
