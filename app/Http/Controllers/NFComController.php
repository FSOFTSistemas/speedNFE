<?php

namespace App\Http\Controllers;

use App\Enums\EstadoEnum;
use App\Models\NFCom;
use App\Services\ClientesService;
use App\Services\EmpresasService;
use App\Services\FluxoDeCaixaService;
use App\Services\NFComService;
use App\Services\ServicosService;
use App\Utils\FormatationUtil;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NFComController extends Controller
{
    private EmpresasService $empresaService;

    private ClientesService $clientesService;

    private FluxoDeCaixaService $fluxoCaixaService;

    private ServicosService $servicosService;

    public function __construct(EmpresasService $empresaService, ClientesService $clientesService, FluxoDeCaixaService $fluxoCaixaService, ServicosService $servicosService)
    {
        $this->empresaService = $empresaService;
        $this->clientesService = $clientesService;
        $this->fluxoCaixaService = $fluxoCaixaService;
        $this->servicosService = $servicosService;
    }

    private function makeNFComService($empresa)
    {
        $config = [
            'atualizacao' => date('Y-m-d h:i:s'),
            'tpAmb' => (int) $empresa->ambiente,
            'razaosocial' => $empresa->razao,
            'siglaUF' => $empresa->endereco->uf,
            'cnpj' => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
            'schemes' => 'PL_NFCOM_1.00_NT2025.001 RTC_1.10',
            'versao' => '1.00',
            'tokenIBPT' => 'AAAAAAA',
        ];

        return new NFComService($config, $empresa);
    }

    private function somarItens(array $itens): array
    {
        $vProd = $vDesc = $vOutro = 0;
        foreach ($itens as $item) {
            $vProd += (float) ($item['vProd'] ?? 0);
            $vDesc += (float) ($item['vDesc'] ?? 0);
            $vOutro += (float) ($item['vOutro'] ?? 0);
        }

        return [$vProd, $vDesc, $vOutro];
    }

    public function index()
    {
        try {
            $nfcoms = NFCom::where('empresa_id', Auth::user()->empresa_id)
                ->with('cliente')
                ->latest('data')
                ->get();

            return view('nfcom.index', ['nfcoms' => $nfcoms]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function create()
    {
        try {
            $empresa = $this->empresaService->buscarEmpresa(Auth::user()->empresa_id);
            $clientes = $this->clientesService->todos(Auth::user()->empresa_id);
            $servicos = $this->servicosService->todos(Auth::user()->empresa_id);
            $isSimples = in_array((int) $empresa->crt, [1, 4]);

            return view('nfcom.create', ['clientes' => $clientes, 'servicos' => $servicos, 'isSimples' => $isSimples]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'cliente_id' => 'required|numeric',
                'iCodAssinante' => 'required',
                'tpAssinante' => 'required',
                'tpServUtil' => 'required',
                'competFat' => 'required',
                'dVencFat' => 'required|date',
                'itens' => 'required|array|min:1',
                'itens.*.cProd' => 'required',
                'itens.*.xProd' => 'required',
                'itens.*.cClass' => 'required',
                'itens.*.uMed' => 'required',
                'itens.*.qFaturada' => 'required|numeric',
                'itens.*.vItem' => 'required|numeric',
                'itens.*.vProd' => 'required|numeric',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
            ]);

            $empresa = $this->empresaService->buscarEmpresa(Auth::user()->empresa_id);
            [$vProd, $vDesc, $vOutro] = $this->somarItens($request->itens);

            DB::beginTransaction();
            $nfcom = NFCom::create([
                'nro' => ($empresa->ultimaNFCom ?? 0) + 1,
                'data' => now(),
                'serie' => $empresa->serie,
                'situacao' => EstadoEnum::PENDENTE->value,
                'cliente_id' => $request->cliente_id,
                'empresa_id' => $empresa->id,
                'iCodAssinante' => $request->iCodAssinante,
                'tpAssinante' => $request->tpAssinante,
                'tpServUtil' => $request->tpServUtil,
                'nContrato' => $request->nContrato,
                'competFat' => $request->competFat,
                'dVencFat' => $request->dVencFat,
                'dPerUsoIni' => $request->dPerUsoIni,
                'dPerUsoFim' => $request->dPerUsoFim,
                'codBarras' => $request->codBarras,
                'vProd' => $vProd,
                'vDesc' => $vDesc,
                'vNF' => $vProd - $vDesc + $vOutro,
            ]);

            foreach ($request->itens as $item) {
                $nfcom->itens()->create($item);
            }
            DB::commit();

            return redirect()->route('nfcom.index')->with('success', 'NFCom criada com sucesso!');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();

            return back()->with('warning', implode(PHP_EOL, $errors))->withInput();
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $nfcom = NFCom::with('itens', 'cliente')->findOrFail($id);
            if ($nfcom->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas notas Pendentes podem ser editadas!');
            }
            $empresa = $this->empresaService->buscarEmpresa(Auth::user()->empresa_id);
            $clientes = $this->clientesService->todos(Auth::user()->empresa_id);
            $servicos = $this->servicosService->todos(Auth::user()->empresa_id);
            $isSimples = in_array((int) $empresa->crt, [1, 4]);

            return view('nfcom.edit', ['nfcom' => $nfcom, 'clientes' => $clientes, 'servicos' => $servicos, 'isSimples' => $isSimples]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'cliente_id' => 'required|numeric',
                'iCodAssinante' => 'required',
                'tpAssinante' => 'required',
                'tpServUtil' => 'required',
                'competFat' => 'required',
                'dVencFat' => 'required|date',
                'itens' => 'required|array|min:1',
                'itens.*.cProd' => 'required',
                'itens.*.xProd' => 'required',
                'itens.*.cClass' => 'required',
                'itens.*.uMed' => 'required',
                'itens.*.qFaturada' => 'required|numeric',
                'itens.*.vItem' => 'required|numeric',
                'itens.*.vProd' => 'required|numeric',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
            ]);

            $nfcom = NFCom::findOrFail($id);
            if ($nfcom->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas notas Pendentes podem ser editadas!');
            }
            [$vProd, $vDesc, $vOutro] = $this->somarItens($request->itens);

            DB::beginTransaction();
            $nfcom->update([
                'cliente_id' => $request->cliente_id,
                'iCodAssinante' => $request->iCodAssinante,
                'tpAssinante' => $request->tpAssinante,
                'tpServUtil' => $request->tpServUtil,
                'nContrato' => $request->nContrato,
                'competFat' => $request->competFat,
                'dVencFat' => $request->dVencFat,
                'dPerUsoIni' => $request->dPerUsoIni,
                'dPerUsoFim' => $request->dPerUsoFim,
                'codBarras' => $request->codBarras,
                'vProd' => $vProd,
                'vDesc' => $vDesc,
                'vNF' => $vProd - $vDesc + $vOutro,
            ]);

            $nfcom->itens()->delete();
            foreach ($request->itens as $item) {
                $nfcom->itens()->create($item);
            }
            DB::commit();

            return redirect()->route('nfcom.index')->with('success', 'NFCom atualizada com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate(['nfcom_id' => 'required|numeric']);
            $nfcom = NFCom::findOrFail($request->nfcom_id);
            if ($nfcom->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas notas Pendentes podem ser excluídas!');
            }
            DB::beginTransaction();
            $nfcom->itens()->delete();
            $nfcom->delete();
            DB::commit();

            return redirect()->route('nfcom.index')->with('success', 'NFCom excluída com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function enviarNFCom($id)
    {
        try {
            $nfcom = NFCom::with('itens', 'cliente.endereco', 'empresa.endereco')->findOrFail($id);
            if ($nfcom->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas notas Pendentes podem ser enviadas!');
            }

            $nfComService = $this->makeNFComService($nfcom->empresa);
            DB::beginTransaction();
            $xml = $nfComService->gerarXml($nfcom, $nfcom->empresa);
            if (isset($xml['erros_xml'])) {
                $nfcom->situacao = EstadoEnum::REJEITADO->value;
                $nfcom->save();
                DB::commit();

                return redirect()->route('nfcom.index')->with('warning', implode(' | ', (array) $xml['erros_xml']));
            }

            $signedXml = $nfComService->sign($xml['xml']);
            $result = $nfComService->transmitir($signedXml);
            if (isset($result['sucesso'])) {
                $this->empresaService->incrementLastNFCom($nfcom->empresa_id);
                $nfcom->chave = $xml['chave'];
                $nfcom->nro = $xml['nNF'];
                $nfcom->nProtocolo = $result['nProt'];
                $nfcom->xml = $result['xml'];
                $nfcom->situacao = EstadoEnum::AUTORIZADO->value;
                $nfcom->save();
                $this->fluxoCaixaService->registrarEntradaAutomatica(
                    $nfcom->empresa_id,
                    $nfcom->vNF,
                    'NFCom #'.$nfcom->nro.' - '.$nfcom->cliente->nome,
                    $nfcom->data,
                    'NFCom',
                    $nfcom->id
                );
                DB::commit();

                return redirect()->route('nfcom.index')->with('success', 'NFCom enviada com sucesso!');
            }

            $nfcom->situacao = EstadoEnum::REJEITADO->value;
            $nfcom->save();
            DB::commit();

            return redirect()->route('nfcom.index')->with('warning', $result['erro']);
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function cancelarNFCom(Request $request)
    {
        try {
            $request->validate([
                'nfcom_id' => 'required|numeric',
                'justificativa' => 'required|min:15',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'min' => 'O campo :attribute deve ter no mínimo :min caracteres!',
            ]);

            $nfcom = NFCom::findOrFail($request->nfcom_id);
            if ($nfcom->situacao !== EstadoEnum::AUTORIZADO) {
                return back()->with('warning', 'Apenas notas Autorizadas podem ser canceladas!');
            }

            $nfComService = $this->makeNFComService($nfcom->empresa);
            DB::beginTransaction();
            $result = $nfComService->cancelar($nfcom, $request->justificativa);
            if (isset($result['erro'])) {
                DB::rollBack();

                return back()->with('warning', $result['erro']);
            }

            $nfcom->situacao = EstadoEnum::CANCELADO->value;
            $nfcom->nProtocolo = $result['nProt'] ?? $nfcom->nProtocolo;
            $nfcom->save();
            $this->fluxoCaixaService->estornarPorOrigem('NFCom', $nfcom->id);
            DB::commit();

            return redirect()->route('nfcom.index')->with('success', 'NFCom cancelada com sucesso!');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }

            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function visualizar($id)
    {
        try {
            $nfcom = NFCom::with('itens', 'cliente.endereco', 'empresa.endereco')->findOrFail($id);
            $pdf = Pdf::loadView('nfcom.dac', ['nfcom' => $nfcom]);

            return $pdf->stream('NFCom_'.$nfcom->nro.'.pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function downloadXml($id)
    {
        try {
            $nfcom = NFCom::findOrFail($id);
            if (empty($nfcom->xml)) {
                return back()->with('warning', 'Essa NFCom ainda não foi autorizada, não há XML disponível!');
            }
            header('Content-disposition: attachment; filename="'.$nfcom->chave.'.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
            echo $nfcom->xml;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }
}
