<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class ClientesService
{

    public function um($id)
    {
        return Cliente::select('clientes.*', 'empresas.razao', 'enderecos.rua', 'enderecos.numero', 'enderecos.bairro', 'enderecos.cidade',
            'enderecos.cep', 'enderecos.codigoIBGE', 'enderecos.uf')
            ->join('empresas', 'empresas.id', 'clientes.empresa_id')
            ->join('enderecos', 'enderecos.id', 'clientes.endereco_id')
            ->where('clientes.id', $id)
            ->first();
    }

    public function todos($id_empresa)
    {
        if ($id_empresa == 1) {
            $id_empresa = '%';
        }
        return DB::table('clientes')
            ->select('clientes.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'clientes.empresa_id')
            ->where('clientes.empresa_id', 'like', $id_empresa)
            ->get();
    }

    public function salvar($codigo, $nome, $apelido, $cpf_cnpj, $rg_ie, $telefone, $celular, $tipo, $limite, $empresa, $endereco)
    {
        $contribuinte = 0;
        if ($rg_ie) {
            $contribuinte = 1;
        }
        return Cliente::create([
            'codigo' => $codigo,
            'nome' => $nome,
            'apelido' => $apelido,
            'cpf_cnpj' => $cpf_cnpj,
            'rg_ie' => $rg_ie,
            'telefone' => $telefone,
            'celular' => $celular,
            'tipo' => $tipo,
            'situacao' => 0,
            'limite' => $limite,
            'contribuinte' => $contribuinte,
            'empresa_id' => $empresa,
            'endereco_id' => $endereco,
        ]);
    }

    public function excluir($id)
    {
        $cliente = Cliente::findOrFail($id);

        return $cliente->delete();
    }

    public function editar($id, $tipo, $nome, $apelido, $cpf_cnpj, $rg_ie, $telefone, $celular, $limite)
    {
        $cliente = Cliente::find($id);
        $cliente->update([
            'tipo' => $tipo,
            'nome' => $nome,
            'apelido' => $apelido,
            'cpf_cnpj' => $cpf_cnpj,
            'rg_ie' => $rg_ie,
            'telefone' => $telefone,
            'celular' => $celular,
            'limite' => $limite,
        ]);
        return $cliente;
    }

    public function contagemClientes($id_empresa)
    {
        return Cliente::where('empresa_id', '=', $id_empresa)
            ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
            ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
            ->count();
    }

}
