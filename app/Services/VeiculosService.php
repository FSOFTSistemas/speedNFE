<?php

namespace App\Services;

use App\Models\Proprietario;
use App\Models\Veiculo;

class VeiculosService
{

    public function salvar($request)
    {
        return Veiculo::create($request);
    }

    public function update($request, $veiculoID)
    {
        $veiculo = Veiculo::with('proprietario')->find($veiculoID);
        $veiculo->update($request);
        return $veiculo->proprietario;
    }

    public function delete($veiculoID)
    {
        $veiculo = Veiculo::find($veiculoID);
        return $veiculo->delete();
    }

    public function salvarProprietario($cpf_cnpj, $ie, $isento, $nome, $uf_prop, $rntrc, $tipo_proprietario, $tipo_transportador, $veiculo, $empresa)
    {
        if ($cpf_cnpj) {
            return Proprietario::create([
                'cpf_cnpj' => $cpf_cnpj,
                'ie' => $ie,
                'isento' => $isento ? true : false,
                'nome_proprietario' => $nome,
                'uf_proprietario' => $uf_prop,
                'rntrc' => $rntrc,
                'tipo_proprietario' => $tipo_proprietario,
                'tipo_transportador' => $tipo_transportador,
                'veiculo_id' => $veiculo->id
            ]);
        } else {
            $prop = Proprietario::where('empresa_id', $empresa->id)->first();
            if ($prop) {
                return $prop->id;
            }
            return Proprietario::create([
                'cpf_cnpj' => $empresa->cpf_cnpj,
                'ie' => $empresa->rg_ie,
                'isento' => 1,
                'nome_proprietario' => $empresa->razao,
                'uf_proprietario' => $empresa->uf,
                'rntrc' => '00000000',
                'tipo_proprietario' => 'TAC independente',
                'tipo_transportador' => 'TAC',
                'empresa_id' => $empresa->id,
                'veiculo_id' => $veiculo->id
            ]);
        }
    }

    public function atualizarProprietario($cpf_cnpj, $ie, $isento, $nome, $uf_prop, $rntrc, $tipo_proprietario, $tipo_transportador, $proprietario)
    {
        if ($cpf_cnpj) {
            $proprietario = Proprietario::find($proprietario->id);
            return $proprietario->update([
                'cpf_cnpj' => $cpf_cnpj,
                'ie' => $ie,
                'isento' => $isento ? true : false,
                'nome_proprietario' => $nome,
                'uf_proprietario' => $uf_prop,
                'rntrc' => $rntrc,
                'tipo_proprietario' => $tipo_proprietario,
                'tipo_transportador' => $tipo_transportador,
            ]);
        }
    }

    public function buscarVeiculo($veiculoID)
    {
        return Veiculo::with('proprietario')->select('veiculos.*', 'empresas.fantasia')
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

    public function buscarVeiculosTracao()
    {
        return Veiculo::select('veiculos.*', 'empresas.cpf_cnpj', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'veiculos.empresaId')
            ->where('veiculos.tipo_veiculo', 'Tração')
            ->get();
    }

    public function buscarReboques()
    {
        return Veiculo::select('veiculos.*', 'empresas.cpf_cnpj', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'veiculos.empresaId')
            ->where('veiculos.tipo_veiculo', 'Reboque')
            ->get();
    }
}
