<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItensEntrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrada_id',
        'produto_id',
        'empresa_id',

        // Identificação do item na NFe
        'numero_item',
        'codigo_fornecedor',
        'codigo_barras',
        'codigo_barras_tributavel',
        'descricao',
        'ncm',
        'cest',
        'cfop',
        'unidade',
        'unidade_tributavel',

        // Quantidades e valores
        'qtde',
        'quantidade_tributavel',
        'valor_unitario',
        'valor_unitario_tributavel',
        'valor_total',
        'valor_desconto',
        'valor_frete',
        'valor_seguro',
        'valor_outros',

        // ICMS
        'origem_icms',
        'cst_icms',
        'csosn',
        'modalidade_bc_icms',
        'valor_bc_icms',
        'aliquota_icms',
        'valor_icms',
        'modalidade_bc_icms_st',
        'valor_bc_icms_st',
        'aliquota_icms_st',
        'valor_icms_st',
        'valor_icms_desonerado',

        // IPI
        'cst_ipi',
        'enquadramento_ipi',
        'valor_bc_ipi',
        'aliquota_ipi',
        'valor_ipi',

        // PIS
        'cst_pis',
        'valor_bc_pis',
        'aliquota_pis',
        'valor_pis',

        // COFINS
        'cst_cofins',
        'valor_bc_cofins',
        'aliquota_cofins',
        'valor_cofins',

        // Pedido/compra
        'numero_pedido',
        'item_pedido',

        // Dados extras do XML para auditoria futura
        'informacoes_adicionais',
    ];

    protected $casts = [
        'qtde' => 'decimal:4',
        'quantidade_tributavel' => 'decimal:4',
        'valor_unitario' => 'decimal:6',
        'valor_unitario_tributavel' => 'decimal:6',
        'valor_total' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_frete' => 'decimal:2',
        'valor_seguro' => 'decimal:2',
        'valor_outros' => 'decimal:2',
        'valor_bc_icms' => 'decimal:2',
        'aliquota_icms' => 'decimal:4',
        'valor_icms' => 'decimal:2',
        'valor_bc_icms_st' => 'decimal:2',
        'aliquota_icms_st' => 'decimal:4',
        'valor_icms_st' => 'decimal:2',
        'valor_icms_desonerado' => 'decimal:2',
        'valor_bc_ipi' => 'decimal:2',
        'aliquota_ipi' => 'decimal:4',
        'valor_ipi' => 'decimal:2',
        'valor_bc_pis' => 'decimal:2',
        'aliquota_pis' => 'decimal:4',
        'valor_pis' => 'decimal:2',
        'valor_bc_cofins' => 'decimal:2',
        'aliquota_cofins' => 'decimal:4',
        'valor_cofins' => 'decimal:2',
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'entrada_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

}
