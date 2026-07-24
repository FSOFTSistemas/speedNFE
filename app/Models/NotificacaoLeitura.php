<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacaoLeitura extends Model
{
    use HasFactory;

    protected $table = 'notificacao_leituras';

    protected $fillable = [
        'notificacao_id',
        'user_id',
        'lida_em',
    ];

    protected $casts = [
        'lida_em' => 'datetime',
    ];

    public function notificacao()
    {
        return $this->belongsTo(Notificacao::class, 'notificacao_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
