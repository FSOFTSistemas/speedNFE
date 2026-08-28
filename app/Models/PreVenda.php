<?php

namespace App\Models;

use App\Enums\PreVendaDestinoEnum;
use App\Enums\PreVendaStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreVenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'empresa_id',
        'cliente_id',
        'cliente_nome',
        'cliente_documento',
        'user_id',
        'data',
        'validade_at',
        'status',
        'subtotal',
        'desconto',
        'acrescimo',
        'total',
        'observacoes',
        'destino',
        'pedido_id',
        'cupom_id',
        'convertida_por',
        'convertida_at',
    ];

    protected $casts = [
        'data' => 'date',
        'validade_at' => 'date',
        'convertida_at' => 'datetime',
        'status' => PreVendaStatusEnum::class,
        'destino' => PreVendaDestinoEnum::class,
        'subtotal' => 'decimal:2',
        'desconto' => 'decimal:2',
        'acrescimo' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function convertidaPor()
    {
        return $this->belongsTo(User::class, 'convertida_por');
    }

    public function itens()
    {
        return $this->hasMany(PreVendaItem::class);
    }

    public function pagamentos()
    {
        return $this->hasMany(PreVendaPagamento::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function cupom()
    {
        return $this->belongsTo(Cupom::class);
    }

    public function statusEfetivo(): PreVendaStatusEnum
    {
        if (
            $this->status === PreVendaStatusEnum::ABERTA
            && $this->validade_at
            && $this->validade_at->isBefore(today())
        ) {
            return PreVendaStatusEnum::EXPIRADA;
        }

        return $this->status;
    }
}
