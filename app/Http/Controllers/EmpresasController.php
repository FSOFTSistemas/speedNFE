<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Endereco;
use App\Services\EmpresasService;
use App\Services\EnderecosService;
use App\Services\UsersService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NFePHP\Common\Certificate;

class EmpresasController extends Controller
{

    private EmpresasService $empresaServices;
    private UsersService $userServices;

    public function __construct(EmpresasService $empresaServices, UsersService $userServices)
    {
        $this->empresaServices = $empresaServices;
        $this->userServices = $userServices;
    }

    public function cadastrar()
    {
        return view('empresas.cadastrar');
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
            return view('empresas.empresa', ['empresa' => $empresa, 'user' => Auth::user()]);
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
            $empresa = Empresa::findOrFail($id);
            $endereco = Endereco::findOrFail($empresa->endereco_id);
            $response = $endereco->update([
                'rua' => $request->rua,
                'bairro' => $request->bairro,
                'numero' => $request->numero,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
                'codigoIBGE' => $request->ibge,
                'cep' => $request->cep,
                'complemento' => $request->complemento,
            ]);
            if ($request->hasFile('certificado')) {
                $path = $request->certificado->storeAs('storage/app/certificados', $request->nome . '.pfx');
                // $content = file_get_contents('storage/'.$request->nome.'.pfx');
                // $ctx = Certificate::readPfx($content, $request->senha);
                // $request->merge(['certificado' => $]);

                if ($request->senha != '') {
                    $response = $empresa->update([
                        'razao' => $request->nome,
                        'fantasia' => $request->fantasia,
                        'cpf_cnpj' => $request->cpf_cnpj,
                        'rg_ie' => $request->rg_ie,
                        'celular' => $request->telefone,
                        'ultimaNFe' => $request->nfe,
                        'serie' => $request->serie,
                        'senhaCertificado' => $request->senha,
                        'ambiente' => $request->ambiente,
                        'certificado' => $path,
                        'csc' => $request->csc,
                        'idCsc' => $request->idCsc,
                        'limNotas' => $request->notas,
                        'limProdutos' => $request->produtos,
                        'limClientes' => $request->clientes,
                    ]);
                } else {
                    $response = $empresa->update([
                        'razao' => $request->nome,
                        'fantasia' => $request->fantasia,
                        'cpf_cnpj' => $request->cpf_cnpj,
                        'rg_ie' => $request->rg_ie,
                        'celular' => $request->telefone,
                        'ultimaNFe' => $request->nfe,
                        'serie' => $request->serie,
                        'ambiente' => $request->ambiente,
                        'certificado' => $path,
                        'csc' => $request->csc,
                        'idCsc' => $request->idCsc,
                        'limNotas' => $request->notas,
                        'limProdutos' => $request->produtos,
                        'limClientes' => $request->clientes,
                    ]);
                }
            }

            if ($request->senha != '') {
                $response = $empresa->update([
                    'razao' => $request->nome,
                    'fantasia' => $request->fantasia,
                    'cpf_cnpj' => $request->cpf_cnpj,
                    'rg_ie' => $request->rg_ie,
                    'celular' => $request->telefone,
                    'ultimaNFe' => $request->nfe,
                    'serie' => $request->serie,
                    'senhaCertificado' => $request->senha,
                    'ambiente' => $request->ambiente,
                    'csc' => $request->csc,
                    'idCsc' => $request->idCsc,
                    'limNotas' => $request->notas,
                    'limProdutos' => $request->produtos,
                    'limClientes' => $request->clientes,
                ]);
            } else {
                $response = $empresa->update([
                    'razao' => $request->nome,
                    'fantasia' => $request->fantasia,
                    'cpf_cnpj' => $request->cpf_cnpj,
                    'rg_ie' => $request->rg_ie,
                    'celular' => $request->telefone,
                    'ultimaNFe' => $request->nfe,
                    'ambiente' => $request->ambiente,
                    'csc' => $request->csc,
                    'idCsc' => $request->idCsc,
                    'limNotas' => $request->notas,
                    'limProdutos' => $request->produtos,
                    'limClientes' => $request->clientes,
                ]);
            }

            if ($response == 1) {
                return redirect('/empresa');
            }
            return "Erro";
        } catch (Exception $e) {
            dd($e);
            return back();
        }
    }

    public function store(Request $request)
    {
        try {
            $sEndereco = new EnderecosService();
            $responseE = $sEndereco->salvar(
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
                $path = $request->certificado->storeAs('storage/app/certificados', $request->nome . '.pfx');
                $content = file_get_contents('../storage/app/certificados/' . $request->nome . '.pfx');
                $ctx = Certificate::readPfx($content, $request->senha);
            } else {
                $path = '';
            }
            $response = Empresa::salvar(
                $request->nome,
                $request->fantasia,
                $request->cpf_cnpj,
                $responseE->id,
                $request->rg_ie,
                $request->telefone,
                $request->nfe,
                $request->serie,
                $ctx,
                $request->senha,
                $request->ambiente,
                $request->csc,
                $request->idCsc, $request->notas, $request->clientes, $request->produtos);
            $this->userServices->store($request->email, $request->password, 'cliente', $response->id, $request->name);
            if ($response) {
                return redirect('/empresa');
            }
            return "Erro";
        } catch (Exception $e) {
            dd($e);
            return back();
        }
    }
}
