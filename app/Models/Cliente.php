<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'nome', 'apelido' ,'cpf_cnpj', 'rg_ie', 'telefone' ,'celular', 'tipo', 'situacao', 'limite', 'contribuinte', 'endereco_id', 'empresa_id'];

    public function endereco(){
        return $this->hasOne(Endereco::class, 'id', 'endereco_id');
    }

    public function nfses()
    {
        return $this->hasMany(NFSe::class, 'cliente_id');
    }
}
