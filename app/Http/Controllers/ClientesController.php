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
            if ($user->empresa_id == 1) {
                $empresas = $this->empresaServices->todas();
            }
            return view('clientes.todos', ['clientes' => $clientes, 'empresa' => $user->empresa_id, 'empresas' => $empresas]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function new ()
    {
        try {
            $empresas = $this->empresaServices->todas();
            $cidades = $this->cidadeServices->buscarCidades();
            return view('clientes.cadastrar', ['empresas' => $empresas, 'cidades' => $cidades]);
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
                'telefone' => 'required|max:15',
                'empresa' => 'required',
                'rua' => 'max:255',
                'numero' => '',
                'bairro' => 'max:255',
                'cidade' => 'max:255',
                'uf' => '',
                'cep' => '',
                'ibge' => '',
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
                $cliente = $this->clienteServices->salvar(
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
                return redirect()->route('index')->with('error', 'Limite de clientes atingido');
            }
            return redirect()->route('index')->with('success', 'Cliente cadastrado com sucesso');
        } catch (Exception $e) {
            return back()->with('error', $cliente);
        }
    }

    public function excluir($id)
    {
        try {
            $this->clienteServices->excluir($id);
            return redirect()->route('index')->with('success', 'Cliente excluído com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Não foi possível excluir o cliente selecionado');
        }
    }

    public function editar($id)
    {
        try {
            $cliente = $this->clienteServices->um($id);
            $empresas = $this->empresaServices->todas();
            $cidades = $this->cidadeServices->buscarCidades();
            return view('clientes.editar', ['cliente' => $cliente, 'empresas' => $empresas, 'cidades' => $cidades]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function update($id, Request $request)
    {
        $sCliente = new ClientesService();
        $cliente = $sCliente->um(intval($id));
        $sEndereco = new EnderecosService();
        $respE = $sEndereco->editar($cliente->endereco_id, $request->rua, $request->bairro, $request->numero, $request->cidade, $request->uf, $request->ibge, $request->cep, $request->complemento);
        $respC = $sCliente->editar($id, $request->tipo, $request->nome, $request->apelido, $request->cpf_cnpj, $request->rg_ie, $request->telefone, $request->telefone, $request->limite);

        if ($respE == 1 && $respC == 1) {
            return redirect('/cliente')->with('success', 'Cliente atualizado com sucesso');
        }
        return redirect('/cliente')->with('error', 'Não foi possível atualizar o cliente');
        // return $request->rua.' '.$request->bairro.' '.$request->numero.' '.$request->cidade.' '.$request->uf.' '.$request->ibge;

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
