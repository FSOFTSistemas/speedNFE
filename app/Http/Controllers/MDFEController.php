<?php

namespace App\Http\Controllers;

use App\Enum\TipoDocumentoEnum;
use App\Enum\UfEnum;
use App\Services\MDFeMotoristaService;
use App\Services\EmpresasService;
use App\Services\MDFeReboqueService;
use App\Services\MDFeService;
use App\Services\NotasService;
use App\Services\ProdPredService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MDFEController extends Controller
{

    private NotasService $notasService;
    private ProdPredService $prodPredService;
    private EmpresasService $empresaService;
    private MDFeReboqueService $reboqueService;
    private MDFeMotoristaService $motoristaService;

    public function __construct(NotasService $notasService, EmpresasService $empresaService, ProdPredService $prodPredService, MDFeReboqueService $reboqueService, MDFeMotoristaService $motoristaService)
    {
        $this->notasService = $notasService;
        $this->empresaService = $empresaService;
        $this->prodPredService = $prodPredService;
        $this->reboqueService = $reboqueService;
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
                'motoristas' => 'required',
                'veiculosReboque' => 'nullable',
                'tipoTransporte' => 'required',
                'numero' => 'required',
                'serie' => 'required',
                'localCarregamento' => 'required',
                'municipio' => 'required|max:255',
                'codMunCarregamento' => 'required',
                'localDescarregamento' => 'required',
                'percursos' => 'nullable',
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
                "lon_descarregamento" => 'required',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo :attibute pode ter no máximo 255 dígitos!',
            ]);
            DB::beginTransaction();
                $MDFe = $this->notasService->save(
                    1,
                    $request->serie,
                    $request->dataInicio,
                    $request->localCarregamento,
                    $request->localDescarregamento,
                    $request->codMunCarregamento,
                    $request->municipio,
                    $request->percursos,
                    $request->valorTotal,
                    $request->pesoTotal,
                    $request->tipoCarga,
                    $this->empresaService->buscarEmpresa(Auth::user()->empresa_id),
                    $request->veiculoTracao,
                    $request->numeroLacre,
                    $request->info_fisco,
                    $request->info_contribuinte,
                );
            if ($MDFe) {
                $this->prodPredService->createProdPred(
                    $request->produtoPredominante,
                    $request->ncm,
                    $request->codigo_gtin,
                    $request->lat_carregamento,
                    $request->lon_carregamento,
                    $request->lat_descarregamento,
                    $request->lon_descarregamento,
                    $MDFe->id
                );
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
                if ($request->reboques) {
                    foreach ($request->reboques as $reboque) {
                        $this->reboqueService->createReboque(
                            $reboque,
                            $MDFe->id
                        );
                    }
                }
                foreach ($request->motoristas as $motorista) {
                    $this->motoristaService->createMotorista(
                        $motorista,
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
                'mdfeId' => 'required|numeric',
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
            DB::beginTransaction();
            $xml = $MDFeService->gerarXml($mdfe, $mdfe->empresa);
            if ($xml && !isset($xml['erros_xml'])) {
                $signedXml = $MDFeService->sign($xml['xml']);
                $result = $MDFeService->transmitir($signedXml, $xml['chave'], 'xml_mdfe/' . $mdfe->empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Autorizadas');
                if (isset($result['sucesso'])) {
                    $mdfe->chave_acesso = $xml['chave'];
                    $mdfe->situacao = 'Autorizado';
                    $mdfe->numero = $xml['nMDF'];
                    $mdfe->save();
                    $mdfe->empresa->update(['ultimaMDFe' => $mdfe->empresa->ultimaMDFe + 1]);
                    DB::commit();
                    return redirect()->route('mdfe.index')->with('success', 'Nota enviada com sucesso');
                } else {
                    $mdfe->situacao = 'Rejeitado';
                    $mdfe->save();
                    DB::commit();
                    return redirect()->route('mdfe.index')->with('warning', $result['erro']);
                }
            }
            DB::rollBack();
            return redirect()->route('mdfe.index')->with('warning', 'Não foi possível enviar a nota, pois sua situação não permite!');
        } catch (Exception $e) {
            DB::rollBack();
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
