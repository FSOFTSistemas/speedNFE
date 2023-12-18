<?php

namespace App\Models;

use App\Enum\TipoCarriceria;
use App\Enum\TipoCarroceriaEnum;
use App\Enum\TipoPropriedadeEnum;
use App\Enum\TipoRodadoEnum;
use App\Enum\TipoVeiculoEnum;
use App\Enum\UfEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'capacidade',
        'renavan',
        'tara',
        'capacidade_m3',
        'tipo_carroceria',
        'tipo_veiculo',
        'tipo_rodado',
        'uf_veiculo',
        'tipo_propriedade',
        'descricao',
        'empresaId'
    ];

    protected $casts = [
        'tipo_carroceria' => TipoCarroceriaEnum::class,
        'tipo_veiculo' => TipoVeiculoEnum::class,
        'tipo_rodado' => TipoRodadoEnum::class,
        'uf_veiculo' => UfEnum::class,
        'tipo_propriedade' => TipoPropriedadeEnum::class
    ];

    public function proprietario()
    {
        return $this->hasOne(Proprietario::class, 'veiculo_id');
    }
}
