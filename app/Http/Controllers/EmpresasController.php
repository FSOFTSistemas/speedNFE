<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Services\CidadeService;
use App\Services\EmpresasService;
use App\Services\EnderecosService;
use App\Services\UsersService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmpresasController extends Controller
{

    private EmpresasService $empresaServices;
    private UsersService $userServices;
    private EnderecosService $enderecoServices;
    private CidadeService $cidadeServices;

    public function __construct(EmpresasService $empresaServices, UsersService $userServices, EnderecosService $enderecoServices, CidadeService $cidadeServices)
    {
        $this->empresaServices = $empresaServices;
        $this->userServices = $userServices;
        $this->enderecoServices = $enderecoServices;
        $this->cidadeServices = $cidadeServices;
    }

    public function cadastrar()
    {
        $cidades = $this->cidadeServices->buscarCidades();
        return view('empresas.cadastrar', ['cidades' => $cidades]);
    }

    public function updateCities($uf)
    {
        return $this->cidadeServices->buscarCidadesPorUf($uf);
    }

    public function desativarReativar($id)
    {
        try {
            $this->empresaServices->reativarDesativar($id);
            return redirect()->route('empresa.index')->with('success', 'Status da empresa atualizado com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Não foi possível atualizar o status da empresa');
        }
    }

    public function editar($id)
    {
        try {
            $empresa = $this->empresaServices->buscarEmpresa($id);
            $cidades = $this->cidadeServices->buscarCidadesPorUf($empresa->uf);
            return view('empresas.empresa', ['empresa' => $empresa, 'user' => Auth::user(), 'cidades' => $cidades]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function show()
    {
        try {
            $empresa = $this->empresaServices->buscarEmpresa(Auth::user()->empresa_id);
            if ($empresa->id != 1) {
                return redirect('/empresa/editar/' . $empresa->id);
            } else {
                $empresa = $this->empresaServices->todas();
                return view('empresas.todos', ['empresas' => $empresa]);
            }
        } catch (Exception $e) {
            return back();
        }
    }

    public function view($id)
    {
        try {
            $empresa = $this->empresaServices->buscarEmpresa($id);
            return view('empresas.view', ['empresa' => $empresa]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|max:255',
                'fantasia' => 'required|max:255',
                'rg_ie' => 'required',
                'telefone' => 'required',
                'rua' => 'required|max:255',
                'numero' => 'required',
                'bairro' => 'required|max:128',
                'cep' => 'required',
                'cidade' => 'required|max:128',
                'uf' => 'required',
                'complemento' => 'max:255',
                'ibge' => 'required',
                'nfe' => 'required|numeric',
                'mdfe' => 'required|numeric',
                'serie' => 'required',
                'senha' => '',
                'csc' => 'required',
                'idCsc' => 'required',
                'ambiente' => 'required|numeric',
                'clientes' => 'required|numeric',
                'produtos' => 'required|numeric',
                'nfes' => 'required|numeric',
                'mdfes' => 'required|numeric'
            ]);

            $empresa = $this->empresaServices->atualizar($id, $request);
            $this->enderecoServices->editar(
                $empresa->endereco_id,
                $request->rua,
                $request->bairro,
                $request->numero,
                $request->cidade,
                $request->uf,
                $request->ibge,
                $request->cep,
                $request->complemento,
            );
            return redirect()->route('editar_empresa', [$empresa->id])->with('success', 'Empresa foi atualizada com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado updateEmpresa, tente novamente em outro momento! Erro: ' . $e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|max:255',
                'fantasia' => 'required|max:255',
                'cpf_cnpj' => 'required',
                'rg_ie' => 'required',
                'telefone' => 'required',
                'rua' => 'required',
                'numero' => 'required',
                'bairro' => 'required',
                'cep' => 'required',
                'cidade' => 'required',
                'uf' => 'required',
                'complemento' => 'nullable',
                'ibge' => 'required',
                'nfe' => 'required',
                'mdfe' => 'required',
                'serie' => 'required',
                'senha' => 'required',
                'csc' => 'required',
                'idCsc' => 'required',
                'ambiente' => 'required',
                'clientes' => 'required',
                'produtos' => 'required',
                'nfes' => 'required|numeric',
                'mdfes' => 'required|numeric',
                'name' => 'required|max:255',
                'email' => 'required',
                'confirm_email' => 'required',
                'password' => 'required',
                'confirm_password' => 'required',
            ]);
            DB::beginTransaction();
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
            $ctx = null;
            if ($request->hasFile('certificado')) {
                $ctx = $this->empresaServices->storeCertificate($request->certificado, $request->nome, $request->senha);
            }
            $empresa = Empresa::salvar(
                $request->nome,
                $request->fantasia,
                $request->cpf_cnpj,
                $endereco->id,
                $request->rg_ie,
                $request->telefone,
                $request->nfe,
                $request->mdfe,
                $request->serie,
                $ctx,
                $request->senha,
                $request->ambiente,
                $request->csc,
                $request->idCsc,
                $request->nfes,
                $request->mdfes,
                $request->clientes,
                $request->produtos
            );
            $this->userServices->store(
                $request->email,
                $request->password,
                $request->cargo,
                $empresa->id,
                $request->name
            );
            DB::commit();
            return redirect()->route('empresa.index')->with('success', 'Empresa foi criada com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e);
        }
    }
}
