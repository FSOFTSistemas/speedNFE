<?php

namespace App\Services;

use App\Models\Cliente;
use Exception;
use Illuminate\Support\Facades\DB;

class ClientesService{
    public function __construct()
    {

    }

    public function um($id){
        try{
            return Cliente::findOrFail($id);
        } catch (Exception $e){
            return 0;
        }
    }

    public function todos($id_empresa){
        if($id_empresa == 1){
            $id_empresa = '%';
        }
        try {
            return DB::table('clientes')
            ->select('clientes.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'clientes.empresa_id')
            ->where('clientes.empresa_id', 'like', $id_empresa)
            ->get();
        } catch (Exception $e){
            return $e;
        }
    }

    public function salvar($codigo, $nome, $apelido, $cpf_cnpj, $rg_ie, $telefone, $celular, $tipo, $limite, $empresa, $endereco){
        try{
            Cliente::create([
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
                'empresa_id' => $empresa,
                'endereco_id' => $endereco
            ]);

            return 1;
        } catch (Exception $e){
            return $e;
        }
    }

    public function excluir($id){
        $cliente = Cliente::findOrFail($id);

        try{
            $cliente->delete();
            return 1;
        } catch (Exception $e){
            return 0;
        }
    }

    public function editar($id, $tipo, $nome, $apelido, $cpf_cnpj, $rg_ie, $telefone, $celular, $limite){

        try{
            $cliente = Cliente::findOrFail($id);
            $cliente->update([
                'tipo' => $tipo,
                'nome' => $nome,
                'apelido' => $apelido,
                'cpf_cnpj' => $cpf_cnpj,
                'rg_ie' => $rg_ie,
                'telefone' => $telefone,
                'celular' => $celular,
                'limite' => $limite
            ]);
            return 1;
        } catch (Exception $e){
            dd($e->getMessage());
            return 0;
        }
    }

}