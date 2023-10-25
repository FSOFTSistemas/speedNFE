<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Endereco;
use App\Services\EmpresasService;
use App\Services\EnderecosService;
use App\Services\UsersService;
use Exception;
use Illuminate\Support\Facades\Auth;
use NFePHP\Common\Certificate;

class EmpresasController extends Controller
{
    public function cadastrar(){
        return view('empresas.cadastrar');
    }

    public function desativarReativar($id){
        $empresa = Empresa::findOrFail($id);

        if($empresa->status == 0){
            $response = $empresa->update([
                'status' => 1
            ]);
        } else {
            $response = $empresa->update([
                'status' => 0
            ]);
        }

        if ($response == 1){
            return redirect('/empresa')->with('success', 'Status da empresa atualizado com sucesso');
        }
        return redirect('/empresa')->with('error', 'Não foi possível atualizar o status da empresa');
    }

    public function editar($id){
        $empresa = Empresa::findOrFail($id);

        $sEndereco = new EnderecosService();
        $endereco = $sEndereco->getEndereco($empresa->endereco_id);

        return view('empresas.empresa', ['endereco' => $endereco, 'empresa' => $empresa, 'user' => Auth::user()]);
    }

    public function show(){
        $sUser = new UsersService();
        $empresaId = $sUser->getEmpresa(Auth::id());

        $sEmpresa = new EmpresasService();
        $empresa = $sEmpresa->getEmpresa($empresaId->empresa_id);

        $sEndereco = new EnderecosService();
        $endereco = $sEndereco->getEndereco($empresa->endereco_id);

        if ($empresaId->empresa_id != 1){
            // return view('empresas.empresa', ['endereco' => $endereco, 'empresa' => $empresa]);
            return redirect('/empresa/editar/'.$empresaId->empresa_id);
        } else {
            $empresa = Empresa::all();
            return view('empresas.todos', ['empresas' => $empresa]);
        }
    }

    public function view($id)
    {
        try {
            $sUser = new UsersService();
            $empresa = $sUser->buscarEmpresa($id);
            return view('empresas.view', ['empresa' => $empresa]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function update($id, Request $request){
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
            'complemento' => $request->complemento
        ]);

        if($request->hasFile('certificado')){
            $path = $request->certificado->storeAs('storage/app/certificados', $request->nome.'.pfx');
            // $content = file_get_contents('storage/'.$request->nome.'.pfx');
            // $ctx = Certificate::readPfx($content, $request->senha);
            // $request->merge(['certificado' => $]);

            if($request->senha != ''){
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
                    'limClientes' => $request->clientes
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
                    'limClientes' => $request->clientes
                ]);
            }
        }

        if($request->senha != ''){
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
                'limClientes' => $request->clientes
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
                'limClientes' => $request->clientes
            ]);
        }

        if ($response == 1){
            return redirect('/empresa');
        }

        return "Erro";
    }

    public function store(Request $request){
      try {
        $sEndereco = new EnderecosService();
        $sUser = new UsersService();
        $responseE = $sEndereco->salvar($request->rua, $request->bairro, $request->numero, $request->cidade, $request->uf, $request->ibge, $request->cep, $request->complemento);
        $ctx = null;
        if($request->hasFile('certificado')){
            $path = $request->certificado->storeAs('storage/app/certificados', $request->nome.'.pfx');

            $content = file_get_contents('../storage/app/certificados/'.$request->nome.'.pfx');

            $ctx = Certificate::readPfx($content, $request->senha);

        } else {
            $path = '';
        }

        $response = Empresa::salvar($request->nome, $request->fantasia, $request->cpf_cnpj, $responseE->id,  $request->rg_ie, $request->telefone, $request->nfe, $request->serie, $ctx, $request->senha, $request->ambiente, $request->csc, $request->idCsc, $request->notas, $request->clientes, $request->produtos);
        $sUser->store($request->email, $request->password, 'cliente', $response->id, $request->name);

        if ($response){
            return redirect('/empresa');
        }

        return "Erro";
    } catch (Exception $e) {
        dd($e);
    }
    }
}
