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
            $request->validate([
                'NFes' => 'required',
                'veiculoTracao' => 'required|numeric',
                'motorista' => 'required|numeric',
                'veiculoReboque' => 'nullable|numeric',
                'tipoTransporte' => 'required',
                'numero' => 'required',
                'serie' => 'required',
                'localCarregamento' => 'required',
                'municipio' => 'required',
                'codMunCarregamento' => 'required',
                'localDescarregamento' => 'required',
                'percurso' => 'required',
                'dataInicio' => 'required|date',
                'valorTotal' => 'required|numeric',
                'pesoTotal' => 'required|numeric',
                'produtoPredominante' => 'required',
                'tipoCarga' => 'required'
            ]);
            $MDFe = $this->notasService->save(
                1,
                $request->serie,
                $request->dataInicio,
                $request->localCarregamento,
                $request->localDescarregamento,
                $request->percurso,
                $request->valorTotal,
                $request->pesoTotal,
                $request->produtoPredominante,
                $request->ncm,
                $request->tipoCarga,
                Auth::user()->empresa_id,
                $request->veiculoTracao,
                $request->veiculoReboque,
                $request->motorista
            );
            foreach ($request->NFes as $NFe) {
                $this->notasService->saveNotas(
                    $NFe['tipoDocumento'],
                    $NFe['chave'],
                    $NFe['ufNFe'],
                    $NFe['cidade'],
                    $NFe['codMun'],
                    $NFe['valor'],
                    $NFe['peso'],
                    $NFe['serieNFe'],
                    $NFe['numeroNFe'],
                    $MDFe->id
                );
            }
            return redirect()->route('mdfe.index')->with('success', 'MDFe foi criada com sucesso!');
            // $empresa = $this->empresaService->buscarEmpresa(Auth::user()->empresa_id);
            // $MDFeService = new MDFeService([
            //     "atualizacao" => date('Y-m-d h:i:s'),
            //     "tpAmb" => (int) $empresa->ambiente,
            //     "razaosocial" => $empresa->razao,
            //     "siglaUF" => $empresa->endereco->uf,
            //     "cnpj" => '42879649000174',
            //     "schemes" => "PL_MDFe_300a",
            //     "versao" => "3.00",
            // ], $empresa);
            // $xml = $MDFeService->gerarXml((object) $request->all(), $empresa);
            // dd($xml);
        } catch (Exception $e) {
            dd($e);
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

    public function downloadXML()
    {
        try {
            return view('mdfes.download-xml');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

}
