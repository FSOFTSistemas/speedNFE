<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Empresa;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\ClientesService;
use App\Services\EmpresasService;
use App\Services\EnderecosService;
use App\Services\UsersService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    public function show(){
        $sUsers = new UsersService();
        $user = $sUsers->getEmpresa(Auth::id());

        $sClientes = new ClientesService();
        $clientes = $sClientes->todos($user->empresa_id);

        $sEmpresa = new EmpresasService();

        if($user->empresa_id == 1){
            $empresas = $sEmpresa->todas();
        } else {
            $empresas = '';
        }

        return view('clientes.todos', ['clientes' => $clientes, 'empresa' => $user->empresa_id, 'empresas' => $empresas]);
    }

    public function new(){
        $sUsers = new UsersService();
        $user = $sUsers->getEmpresa(Auth::id());

        $sEmpresa = new EmpresasService();
        $empresas = $sEmpresa->todas();
        $cidades = Cidade::all();
        return view('clientes.cadastrar', ['empresa' => $user->empresa_id, 'empresas' => $empresas, 'cidades' => $cidades]);
    }

    public function salvar(Request $request){

        $sClientes = new ClientesService();
        $sEndereco = new EnderecosService();
        $sUsers = new UsersService();
        $user = $sUsers->getEmpresa(Auth::id());

        if($request->has('empresa')){
            $user = $request->empresa;
        } else {
            $user = $user->empresa_id;
        }


        if(DB::table('clientes')
        ->where('empresa_id', '=', $user)
        ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
        ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
        ->count() < Empresa::findOrFail($user)->limClientes or $user == 1){
            $endereco = $sEndereco->salvar($request->rua, $request->bairro, $request->numero, $request->cidade, $request->uf, $request->ibge, $request->cep, $request->complemento);
            $resp = $sClientes->salvar($request->codigo, $request->nome, $request->apelido, $request->cpf_cnpj, $request->rg_ie, $request->telefone, $request->celular, $request->tipo, $request->limite, $user, $endereco->id);

        } else {
            return redirect('/cliente')->with('error', 'Limite de clientes atingido');
        }

        if (is_int($resp)) {
            return redirect('/cliente')->with('success', 'Cliente cadastrado com sucesso');
        }
        return redirect('/cliente')->with('error', $resp);
        // return $request->codigo.' '.$request->nome.' '.$request->apelido.' '.$request->cpf_cnpj.' '.$request->rg_ie.' '.$request->telefone.' '.$request->celular.' '.$request->tipo.' '.$request->limite.' '.$user->empresa_id.' '.$endereco->id;
    }

    public function excluir($id){
        $sCliente = new ClientesService();
        $resp = $sCliente->excluir($id);

        if ($resp == 1){
            return redirect('/cliente')->with('success', 'Cliente excluído com sucesso');
        }
        return redirect('/cliente')->with('success', 'Não foi possível excluir o cliente selecionado');
    }

    public function editar($id){
        $sCliente = new ClientesService();
        $cliente = $sCliente->um($id);
        $emp = Empresa::find($cliente->empresa_id);
        $sEndereco = new EnderecosService();
        $sEmpresa = new EmpresasService();

        $endereco = $sEndereco->um($cliente->endereco_id);
        $empresas = $sEmpresa->todas();

        if ($cliente && $endereco){
            return view('clientes.editar', ['cliente' => $cliente, 'endereco' => $endereco,'empresas'=>$empresas,'emp'=>$emp]);
        }
        return redirect('/cliente')->with('error', 'Cliente não encontrado');
    }

    public function update($id, Request $request){
        $sCliente = new ClientesService();
        $cliente = $sCliente->um(intval($id));
        $sEndereco = new EnderecosService();
        $respE = $sEndereco->editar($cliente->endereco_id, $request->rua, $request->bairro, $request->numero, $request->cidade, $request->uf, $request->ibge, $request->cep, $request->complemento);
        $respC = $sCliente->editar($id, $request->tipo, $request->nome, $request->apelido, $request->cpf_cnpj, $request->rg_ie, $request->telefone, $request->telefone, $request->limite);

        if ($respE == 1 && $respC == 1){
            return redirect('/cliente')->with('success', 'Cliente atualizado com sucesso');
        }
        return redirect('/cliente')->with('error', 'Não foi possível atualizar o cliente');
        // return $request->rua.' '.$request->bairro.' '.$request->numero.' '.$request->cidade.' '.$request->uf.' '.$request->ibge;

    }

    public function BuscarCNPJ(Request $request){

        $cnpj = $request->cnpj;
        $URL = "https://receitaws.com.br/v1/";

        $res = 0;
        try{
            $client = new Client([
                'verify' => false,
                'base_uri' => $URL,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => 'https://receitaws.com.br'
                    ]
            ]);

            $response = $client->get("cnpj/".$cnpj);

            $body = $response->getBody()->getContents();

            $responseXml = json_decode($body);

            return $responseXml;

        }catch(Exception $e){
            // dd($e);
            return $res;
        }
    }

}
