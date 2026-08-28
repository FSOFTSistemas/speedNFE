<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreVendaPagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_venda_id',
        'forma_pag_id',
        'descricao',
        'valor',
        'vencimento',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'vencimento' => 'date',
    ];

    public function preVenda()
    {
        return $this->belongsTo(PreVenda::class);
    }

    public function formaPagamento()
    {
        return $this->belongsTo(FormaPag::class, 'forma_pag_id');
    }
}
