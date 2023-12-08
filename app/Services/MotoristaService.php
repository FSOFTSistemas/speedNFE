<?php

namespace App\Services;

use App\Models\Motorista;

class MotoristaService
{

    public function create($nome, $cpf, $empresaId)
    {
        return Motorista::create([
            'nome' => $nome,
            'cpf' => $cpf,
            'empresaId' => $empresaId,
        ]);
    }

    public function update($nome, $cpf, $motoristaId)
    {
        $motorista = Motorista::find($motoristaId);
        return $motorista->update([
            'nome' => $nome,
            'cpf' => $cpf
        ]);
    }

    public function delete($motoristaId)
    {
        $motorista = Motorista::find($motoristaId);
        return $motorista->delete();
    }

    public function buscarMotorista($motoristaId)
    {
        return Motorista::select('motoristas.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'motoristas.empresaId')
            ->where('motoristas.id', $motoristaId)
            ->first();
    }

    public function buscarMotoristas($empresaId)
    {
        return Motorista::select('motoristas.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'motoristas.empresaId')
            ->where('motoristas.empresaId', $empresaId)
            ->get();
    }

}
