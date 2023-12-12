<?php

namespace App\Http\Controllers;

use App\Enum\TipoDocumentoEnum;
use App\Enum\UfEnum;
use App\Services\EmpresasService;
use App\Services\MDFeService;
use App\Services\MotoristaService;
use App\Services\NotasService;
use App\Services\VeiculosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MDFEController extends Controller
{

    private NotasService $notasService;
    private EmpresasService $empresaService;
    private VeiculosService $veiculoService;
    private MotoristaService $motoristaService;

    public function __construct(NotasService $notasService, EmpresasService $empresaService, VeiculosService $veiculoService, MotoristaService $motoristaService)
    {
        $this->notasService = $notasService;
        $this->empresaService = $empresaService;
        $this->veiculoService = $veiculoService;
        $this->motoristaService = $motoristaService;
    }

    public function index()
    {
        try {
            $MDFes = $this->notasService->buscarMDFes(Auth::user()->empresa_id);
            return view('mdfes.index', ['mdfes' => $MDFes]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $tiposDocumentos = TipoDocumentoEnum::cases();
            $ufs = UfEnum::cases();
            return view('mdfes.create', ['tiposDocumentos' => $tiposDocumentos, 'ufs' => $ufs]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'notas' => 'required',
                'veiculoTracao' => 'required|numeric',
                'motorista' => 'required|numeric',
                'veiculoReboque' => 'nullable|numeric',
                'tipoTransporte' => 'required',
                'numero' => 'required',
                'serie' => 'required',
                'localCarregamento' => 'required',
                'municipio' => 'required|max:255',
                'codMunCarregamento' => 'required',
                'localDescarregamento' => 'required',
                'percursos' => 'required',
                'dataInicio' => 'required|date',
                'valorTotal' => 'required|numeric',
                'pesoTotal' => 'required|numeric',
                'produtoPredominante' => 'required|max:255',
                'tipoCarga' => 'required',
                "info_fisco" => 'nullable|max:255',
                "info_contribuinte" => 'nullable|max:255',
                "numeroLacre" => 'required',
                "codigo_gtin" => 'required',
                "ncm" => 'required',
                "lat_carregamento" => 'required',
                "lon_carregamento" => 'required',
                "lat_descarregamento" => 'required',
                "lon_descarregamento" => 'required'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo :attibute pode ter no máximo 255 dígitos!'
            ]);
            DB::beginTransaction();
            $prod_pred_id = 1;
            $MDFe = $this->notasService->save(
                1,
                $request->serie,
                $request->dataInicio,
                $request->localCarregamento,
                $request->localDescarregamento,
                $request->percursos,
                $request->valorTotal,
                $request->pesoTotal,
                $request->tipoCarga,
                Auth::user()->empresa_id,
                $request->veiculoTracao,
                $request->numeroLacre,
                $request->info_fisco,
                $request->info_contribuinte,
                $prod_pred_id
            );
            if ($MDFe) {
                foreach ($request->notas as $nota) {
                    $this->notasService->saveNotas(
                        $nota['tipoDocumento'],
                        $nota['chave'],
                        $nota['ufNota'],
                        $nota['cidade'],
                        $nota['codMun'],
                        $nota['valor'],
                        $nota['peso'],
                        $nota['serieNota'],
                        $nota['numeroNota'],
                        $MDFe->id
                    );
                }
                DB::commit();
                return redirect()->route('mdfe.index')->with('success', 'MDFe foi criada com sucesso!');
            }
            return back()->with('warning', 'Limite de MDFes foi atingido, assine um plano com mais vantagens para aumentar o limite!');
         } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors));
         } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function edit($mdfeId)
    {
        try {
            return view('mdfes.edit');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'mdfeId' => 'required|numeric'
            ]);
            DB::beginTransaction();
            $this->notasService->deleteMDFe($request->mdfeId);
            DB::commit();
            return redirect()->route('mdfe.index')->with('success', 'MDFe foi deletada com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function enviarMDFe($mdfeId)
    {
        try {
            $mdfe = $this->notasService->buscarMDFe($mdfeId);
            $MDFeService = new MDFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int) $mdfe->empresa->ambiente,
                "razaosocial" => $mdfe->empresa->razao,
                "siglaUF" => $mdfe->empresa->endereco->uf,
                "cnpj" => '42879649000174',
                "schemes" => "PL_MDFe_300a",
                "versao" => "3.00",
            ], $mdfe->empresa);
            $xml = $MDFeService->gerarXml($mdfe, $mdfe->empresa);
            dd($xml);
        } catch (Exception $e) {
            dd($e);
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function downloadXML()
    {
        try {
            return view('mdfes.download-xml');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

}
