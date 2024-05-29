<?php

namespace App\Services;

use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use NFePHP\Common\Certificate;

class EmpresasService
{

    public function atualizar($id, $request)
    {
        $empresa = Empresa::find($id);
        if ($request->hasFile('certificado')) {
            // Storage::delete('public/certificados/'. $request->nome . '.pfx');
            $path = $request->certificado->storeAs('public/certificados', $request->nome . '.pfx');
            // $content = file_get_contents('storage/'.$request->nome.'.pfx');
            // $ctx = Certificate::readPfx($content, $request->senha);
            // $request->merge(['certificado' => $]);

            if ($request->senha != '') {
                $empresa->update([
                    'razao' => $request->nome,
                    'fantasia' => $request->fantasia,
                    'rg_ie' => $request->rg_ie,
                    'celular' => $request->telefone,
                    'ultimaNFe' => $request->nfe,
                    'ultimaNFCe' => $request->nfce,
                    'ultimaMDFe' => $request->mdfe,
                    'serie' => $request->serie,
                    'senhaCertificado' => $request->senha,
                    'ambiente' => $request->ambiente,
                    'certificado' => $path,
                    'csc' => $request->csc,
                    'idCsc' => $request->idCsc,
                    'limNFes' => $request->nfes,
                    'limMDFes' => $request->mdfes,
                    'limProdutos' => $request->produtos,
                    'limClientes' => $request->clientes,
                ]);
            } else {
                $empresa->update([
                    'razao' => $request->nome,
                    'fantasia' => $request->fantasia,
                    'rg_ie' => $request->rg_ie,
                    'celular' => $request->telefone,
                    'ultimaNFe' => $request->nfe,
                    'ultimaNFCe' => $request->nfce,
                    'ultimaMDFe' => $request->mdfe,
                    'serie' => $request->serie,
                    'ambiente' => $request->ambiente,
                    'certificado' => $path,
                    'csc' => $request->csc,
                    'idCsc' => $request->idCsc,
                    'limNFes' => $request->nfes,
                    'limMDFes' => $request->mdfes,
                    'limProdutos' => $request->produtos,
                    'limClientes' => $request->clientes,
                ]);
            }
        }

        if ($request->senha != '') {
            $empresa->update([
                'razao' => $request->nome,
                'fantasia' => $request->fantasia,
                'rg_ie' => $request->rg_ie,
                'celular' => $request->telefone,
                'ultimaNFe' => $request->nfe,
                'ultimaNFCe' => $request->nfce,
                'ultimaMDFe' => $request->mdfe,
                'serie' => $request->serie,
                'senhaCertificado' => $request->senha,
                'ambiente' => $request->ambiente,
                'csc' => $request->csc,
                'idCsc' => $request->idCsc,
                'limNFes' => $request->nfes,
                'limMDFes' => $request->mdfes,
                'limProdutos' => $request->produtos,
                'limClientes' => $request->clientes,
            ]);
        } else {
            $empresa->update([
                'razao' => $request->nome,
                'fantasia' => $request->fantasia,
                'rg_ie' => $request->rg_ie,
                'celular' => $request->telefone,
                'ultimaNFe' => $request->nfe,
                'ultimaNFCe' => $request->nfce,
                'ultimaMDFe' => $request->mdfe,
                'ambiente' => $request->ambiente,
                'csc' => $request->csc,
                'idCsc' => $request->idCsc,
                'limNFes' => $request->nfes,
                'limMDFes' => $request->mdfes,
                'limProdutos' => $request->produtos,
                'limClientes' => $request->clientes,
            ]);
        }
        return $empresa;
    }

    public function todas()
    {
        return Empresa::all();
    }

    public function reativarDesativar($id)
    {
        $empresa = Empresa::find($id);

        if ($empresa->status == 0) {
            return $empresa->update([
                'status' => 1,
            ]);
        } else {
            return $empresa->update([
                'status' => 0,
            ]);
        }
    }

    public function minhaEmpresa($id)
    {
        return Empresa::select('empresas.*')
            ->where('empresas.id', $id)
            ->get();
    }

    public function buscarEmpresa($id)
    {
        return Empresa::select('empresas.*', 'users.name', 'users.email', 'enderecos.rua', 'enderecos.bairro', 'enderecos.numero', 'enderecos.cidade',
            'enderecos.complemento', 'enderecos.uf', 'enderecos.cep', 'enderecos.codigoIBGE')
            ->join('users', 'users.empresa_id', 'empresas.id')
            ->join('enderecos', 'enderecos.id', 'empresas.endereco_id')
            ->where('empresas.id', $id)
            ->first();
    }

    public function todos($empresa)
    {
        if ($empresa == 1) {
            $empresa = '%';
        }
        return DB::table('empresas')
            ->select('*')
            ->where('id', 'like', $empresa)
            ->get();
    }

    public function getEmpresa($id_empresa)
    {
        return DB::table('empresas')
            ->where('id', '=', $id_empresa)
            ->first();
    }

    public function storeCertificate($certificado, $nome, $senha)
    {
        $path = $certificado->storeAs('public/certificados', $nome . '.pfx');
        $content = file_get_contents('../storage/app/public/certificados/' . $nome . '.pfx');
        Certificate::readPfx($content, $senha);
        return $path;
    }

    public function incrementCupomSequence($companyId)
    {
        $company = Empresa::find($companyId);
        $company->sequenciaCupom = $company->sequenciaCupom + 1;
        $company->save();
        return $company->sequenciaCupom;
    }

}
