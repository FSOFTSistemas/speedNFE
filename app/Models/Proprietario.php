<?php

namespace App\Models;

use App\Enum\TipoProprietarioEnum;
use App\Enum\TipoTransportadorEnum;
use App\Enum\UfEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proprietario extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf_cnpj',
        'ie',
        'isento',
        'nome_proprietario',
        'uf_proprietario',
        'rntrc',
        'tipo_proprietario',
        'tipo_transportador',
        'empresa_id',
        'veiculo_id'
    ];

    protected $casts = [
        'uf_proprietario' => UfEnum::class,
        'tipo_proprietario' => TipoProprietarioEnum::class,
        'tipo_transportador' => TipoTransportadorEnum::class
    ];

}
