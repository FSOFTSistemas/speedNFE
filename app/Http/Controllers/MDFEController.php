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
use NFePHP\DA\MDFe\Daevento;
use NFePHP\DA\MDFe\Damdfe;

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
        // dd($request->all());
        try {
            $request->validate([
                'notas' => 'required',
                'numeroNotas_' => 'required',
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
                "numeroLacre" => 'nullable',
                "codigo_gtin" => 'required',
                "ncm" => 'required',
                "lat_carregamento" => 'required',
                "lon_carregamento" => 'required',
                "lat_descarregamento" => 'required',
                "lon_descarregamento" => 'required',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo :attribute pode ter no máximo 255 dígitos!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!'
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
                    $request->numeroNotas_['NFe'],
                    $request->numeroNotas_['MDFe'],
                    $request->numeroNotas_['CTe'],
                    $this->empresaService->buscarEmpresa(Auth::user()->empresa_id),
                    $request->veiculoTracao,
                    $request->numeroLacre,
                    $request->info_fisco,
                    $request->info_contribuinte
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
                            explode('/', $reboque)[0],
                            $MDFe->id
                        );
                    }
                }
                foreach ($request->motoristas as $motorista) {
                    $this->motoristaService->createMotorista(
                        explode('/', $motorista)[0],
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
            $nota = $this->notasService->buscarMDFe($mdfeId);
            return view('mdfes.edit', ['nota' => $nota]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $mdfeId)
    {
        // dd($request->all());
        try {
            $request->validate([
                'notas' => 'required',
                'numeroNotas_' => 'required',
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
                'prodPred_id' => 'required|numeric',
                'tipoCarga' => 'required',
                "info_fisco" => 'nullable|max:255',
                "info_contribuinte" => 'nullable|max:255',
                "numeroLacre" => 'nullable',
                "codigo_gtin" => 'required',
                "ncm" => 'required',
                "lat_carregamento" => 'required',
                "lon_carregamento" => 'required',
                "lat_descarregamento" => 'required',
                "lon_descarregamento" => 'required',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo :attribute pode ter no máximo 255 dígitos!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!'
            ]);
            DB::beginTransaction();
            $this->notasService->update(
                $request->numero,
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
                $request->numeroNotas_['NFe'],
                $request->numeroNotas_['MDFe'],
                $request->numeroNotas_['CTe'],
                $request->veiculoTracao,
                $request->numeroLacre,
                $request->info_fisco,
                $request->info_contribuinte,
                $mdfeId
            );
            $this->prodPredService->updateProdPred(
                $request->produtoPredominante,
                $request->ncm,
                $request->codigo_gtin,
                $request->lat_carregamento,
                $request->lon_carregamento,
                $request->lat_descarregamento,
                $request->lon_descarregamento,
                $request->prodPred_id
            );
            $this->notasService->deleteNotas($request->notas, $mdfeId);
            foreach ($request->notas as $nota) {
                $this->notasService->updateOrCreateNotas(
                    $nota['tipoDocumento'],
                    $nota['chave'],
                    $nota['ufNota'],
                    $nota['cidade'],
                    $nota['codMun'],
                    $nota['valor'],
                    $nota['peso'],
                    $nota['serieNota'],
                    $nota['numeroNota'],
                    $nota['nota_id'],
                    $mdfeId
                );
            }
            if ($request->reboques) {
                $this->reboqueService->deleteReboques($request->reboques, $mdfeId);
                foreach ($request->reboques as $reboque) {
                    $this->reboqueService->createReboque(
                        explode('/', $reboque)[0],
                        $mdfeId
                    );
                }
            }
            $this->motoristaService->deleteMotoristas($request->motoristas, $mdfeId);
            foreach ($request->motoristas as $motorista) {
                $this->motoristaService->createMotorista(
                    explode('/', $motorista)[0],
                    $mdfeId
                );
            }
            DB::commit();
            return redirect()->route('mdfe.edit', [$mdfeId])->with('success', 'Nota atuzalizada com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
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
                if (isset($result['sucesso']) && isset($result['nProt'])) {
                    $mdfe->chave_acesso = $xml['chave'];
                    $mdfe->situacao = 'Autorizado';
                    $mdfe->numero = $xml['nMDF'];
                    $mdfe->nProtocolo = $result['nProt'];
                    $mdfe->save();
                    $mdfe->empresa->update(['ultimaMDFe' => $mdfe->empresa->ultimaMDFe + 1]);
                    DB::commit();
                    return redirect()->route('mdfe.index')->with('success', 'Nota enviada com sucesso!');
                } else {
                    $mdfe->situacao = 'Rejeitado';
                    $mdfe->save();
                    DB::commit();
                    return redirect()->route('mdfe.index')->with('warning', $result['erro']);
                }
            }
            DB::rollBack();
            return redirect()->route('mdfe.index')->with('warning', $xml['erros_xml']);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function encerrarMDFe($mdfeId)
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
            $result = $MDFeService->encerrar($mdfe, 'xml_mdfe/' . $mdfe->empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Encerradas');
            if (!isset($result['erro'])) {
                $mdfe->situacao = 'Encerrado';
                $mdfe->nProtocolo = $result['nProt'];
                $mdfe->save();
                DB::commit();
                return redirect()->route('mdfe.index')->with('success', 'Nota encerrada com sucesso!');
            } else {
                DB::rollBack();
                return redirect()->route('mdfe.index')->with('warning', $result['erro']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function cancelarMDFe(Request $request)
    {
        try {
            $request->validate([
                'justificativa' => 'required|max:255',
                'mdfe_id' => 'required|numeric'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo :attribute deve conter no máximo :max dígitos!'
            ]);
            $mdfe = $this->notasService->buscarMDFe($request->mdfe_id);
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
            $result = $MDFeService->cancelar($mdfe, $request->justificativa,  'xml_mdfe/' . $mdfe->empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Canceladas');
            if (!isset($result['erro'])) {
                $mdfe->situacao = 'Cancelado';
                $mdfe->nProtocolo = $result['nProt'];
                $mdfe->save();
                DB::commit();
                return redirect()->route('mdfe.index')->with('success', 'Nota cancelada com sucesso!');
            } else {
                DB::rollBack();
                return redirect()->route('mdfe.index')->with('warning', $result['erro']);
            }
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

    public function imprimirMDFe($mdfeId, $modo)
    {
        try {
            $mdfe = $this->notasService->buscarMDFe($mdfeId);
            if ($modo == 0) {
                $xml = file_get_contents('xml_mdfe/' . $mdfe->empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Autorizadas/' . $mdfe->chave_acesso . '.xml');
                $damdfe = new Damdfe($xml);
                $pdf = $damdfe->render();
                return response($pdf)
                    ->header('Content-Type', 'application/pdf');
            } else if ($modo == 1) {
                $xml = file_get_contents('xml_mdfe/' . $mdfe->empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Encerradas/' . $mdfe->chave_acesso . '.xml');
                $daevento = new Daevento($xml, $mdfe->empresa);
                $daevento->debugMode(true);
                $pdf = $daevento->render();
                return response($pdf)->header('Content-Type', 'application/pdf');
            } else {
                $xml = file_get_contents('xml_mdfe/' . $mdfe->empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Canceladas/' . $mdfe->chave_acesso . '.xml');
                $daevento = new Daevento($xml, $mdfe->empresa);
                $daevento->debugMode(true);
                $pdf = $daevento->render();
                return response($pdf)->header('Content-Type', 'application/pdf');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
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
