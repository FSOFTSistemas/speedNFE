<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CidadeService;
use App\Services\ClientesService;
use App\Services\EmpresasService;
use App\Services\EnderecosService;
use App\Services\UsersService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClientesController extends Controller
{
    private UsersService $userServices;
    private EnderecosService $enderecoServices;
    private EmpresasService $empresaServices;
    private CidadeService $cidadeServices;
    private ClientesService $clienteServices;

    public function __construct(UsersService $userServices, EnderecosService $enderecoServices, EmpresasService $empresaServices, CidadeService $cidadeServices, ClientesService $clienteServices)
    {
        $this->userServices = $userServices;
        $this->enderecoServices = $enderecoServices;
        $this->empresaServices = $empresaServices;
        $this->cidadeServices = $cidadeServices;
        $this->clienteServices = $clienteServices;
    }

    public function show()
    {
        try {
            $user = $this->userServices->getEmpresa(Auth::id());
            $clientes = $this->clienteServices->todos($user->empresa_id);
            return view('clientes.todos', ['clientes' => $clientes, 'empresa' => $user->empresa_id]);
        } catch (Exception $e) {
            return back()->with('error', 'Erro interno, ocorreu um problema inesperado, tente novamento em outro momento!');
        }
    }

    public function new()
    {
        try {
            $user = Auth::user();
            $empresas = $this->empresaServices->todas();
            return view('clientes.cadastrar', ['empresas' => $empresas, 'user' => $user]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function salvar(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|max:255',
                'apelido' => 'required|max:255',
                'codigo' => 'required',
                'limite' => 'required',
                'cpf_cnpj' => 'required',
                'rg_ie' => 'required',
                'tipo' => 'required',
                'telefone' => 'required|max:20',
                'empresa' => 'required',
                'rua' => 'max:255',
                'numero' => 'nullable',
                'bairro' => 'max:255',
                'cidade' => 'max:255',
                'uf' => 'nullable',
                'cep' => 'nullable',
                'ibge' => 'nullable',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo ":attribute" deve conter no máximo :max caracteres!'
            ]);
            $id_empresa = Auth::user()->id_empresa;
            if ($request->has('empresa')) {
                $id_empresa = $request->empresa;
            }
            if ($this->clienteServices->contagemClientes($id_empresa) < $this->empresaServices->buscarEmpresa($id_empresa)->limClientes || $id_empresa == 1) {
                $endereco = $this->enderecoServices->salvar(
                    $request->rua,
                    $request->bairro,
                    $request->numero,
                    $request->cidade,
                    $request->uf,
                    $request->ibge,
                    $request->cep,
                    $request->complemento
                );
                $this->clienteServices->salvar(
                    $request->codigo,
                    $request->nome,
                    $request->apelido,
                    $request->cpf_cnpj,
                    $request->rg_ie,
                    $request->telefone,
                    $request->celular,
                    $request->tipo,
                    $request->limite,
                    $id_empresa,
                    $endereco->id
                );
            } else {
                return redirect()->route('cliente.index')->with('warning', 'Limite de clientes atingido');
            }
            return redirect()->route('cliente.index')->with('success', 'Cliente cadastrado com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            return back()->with('warning', implode(PHP_EOL, $errors))->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e->getMessage());
        }
    }

    public function excluir(Request $request)
    {
        try {
            $request->validate([
                'idCliente' => 'required'
            ]);
            $this->clienteServices->excluir($request->idCliente);
            return redirect()->route('cliente.index')->with('success', 'Cliente excluído com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function view($id)
    {
        try {
            $cliente = $this->clienteServices->um($id);
            return view('clientes.view', ['cliente' => $cliente]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function editar($id)
    {
        try {
            $cliente = $this->clienteServices->um($id);
            $empresas = $this->empresaServices->todas();
            return view('clientes.editar', ['cliente' => $cliente, 'empresas' => $empresas]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'tipo' => 'required|max:127',
                'nome' => 'required|max:255',
                'apelido' => 'required|max:255',
                'cpf_cnpj' => 'required',
                'rg_ie' => 'required',
                'telefone' => 'required',
                'limite' => 'required',
                'rua' => 'required',
                'bairro' => 'required',
                'numero' => 'required',
                'cidade' => 'required',
                'uf' => 'required',
                'ibge' => 'required',
                'cep' => 'required',
                'complemento' => ''
            ]);
            $cliente = $this->clienteServices->editar(
                $id,
                $request->tipo,
                $request->nome,
                $request->apelido,
                $request->cpf_cnpj,
                $request->rg_ie,
                $request->telefone,
                $request->telefone,
                $request->limite
            );
            $this->enderecoServices->editar(
                $cliente->endereco_id,
                $request->rua,
                $request->bairro,
                $request->numero,
                $request->cidade,
                $request->uf,
                $request->ibge,
                $request->cep,
                $request->complemento
            );
            return redirect()->route('cliente.index')->with('success', 'Cliente atualizado com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function BuscarCNPJ(Request $request)
    {
        try {
            $cnpj = $request->cnpj;
            $URL = "https://receitaws.com.br/v1/";
            $client = new Client([
                'verify' => false,
                'base_uri' => $URL,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => 'https://receitaws.com.br',
                ],
            ]);
            $response = $client->get("cnpj/" . $cnpj);
            $body = $response->getBody()->getContents();
            $responseXml = json_decode($body);
            return $responseXml;
        } catch (Exception $e) {
            return back();
        }
    }
}
