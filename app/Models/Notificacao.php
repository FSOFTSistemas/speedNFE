<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    protected $fillable = [
        'titulo',
        'mensagem',
        'tipo',
        'empresa_id',
        'criado_por',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function leituras()
    {
        return $this->hasMany(NotificacaoLeitura::class, 'notificacao_id');
    }
}
