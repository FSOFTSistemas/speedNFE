<?php

namespace App\Services;

use App\Models\Veiculo;

class VeiculosService
{

    public function salvar($request)
    {
        return Veiculo::create($request);
    }

    public function update($request, $veiculoID)
    {
        $veiculo = Veiculo::find($veiculoID);
        return $veiculo->update($request);
    }

    public function delete($veiculoID)
    {
        $veiculo = Veiculo::find($veiculoID);
        return $veiculo->delete();
    }

    public function buscarVeiculo($veiculoID)
    {
        return Veiculo::select('veiculos.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'veiculos.empresaId')
            ->where('veiculos.id', $veiculoID)
            ->first();
    }

    public function buscarVeiculos()
    {
        return Veiculo::select('veiculos.*', 'empresas.cpf_cnpj', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'veiculos.empresaId')
            ->get();
    }
}
