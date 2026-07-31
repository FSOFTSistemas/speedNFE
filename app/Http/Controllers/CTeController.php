<?php

namespace App\Http\Controllers;

use App\Enums\EstadoEnum;
use App\Models\CTe;
use App\Services\ClientesService;
use App\Services\CTeService;
use App\Services\EmpresasService;
use App\Services\FluxoDeCaixaService;
use App\Services\MotoristaService;
use App\Services\VeiculosService;
use App\Utils\FormatationUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use NFePHP\DA\CTe\Dacte;

class CTeController extends Controller
{
    private EmpresasService $empresaService;

    private ClientesService $clientesService;

    private VeiculosService $veiculosService;

    private MotoristaService $motoristaService;

    private FluxoDeCaixaService $fluxoCaixaService;

    public function __construct(
        EmpresasService $empresaService,
        ClientesService $clientesService,
        VeiculosService $veiculosService,
        MotoristaService $motoristaService,
        FluxoDeCaixaService $fluxoCaixaService
    ) {
        $this->empresaService = $empresaService;
        $this->clientesService = $clientesService;
        $this->veiculosService = $veiculosService;
        $this->motoristaService = $motoristaService;
        $this->fluxoCaixaService = $fluxoCaixaService;
    }

    private function makeCTeService($empresa)
    {
        $config = [
            'atualizacao' => date('Y-m-d h:i:s'),
            'tpAmb' => (int) $empresa->ambiente,
            'razaosocial' => $empresa->razao,
            'siglaUF' => $empresa->endereco->uf,
            'cnpj' => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
            'schemes' => 'PL_CTe_400',
            'versao' => '4.00',
        ];

        return new CTeService($config, $empresa);
    }

    private const CAMPOS_VALIDACAO = [
        'remetente_id' => 'required|numeric',
        'destinatario_id' => 'required|numeric',
        'toma' => 'required|in:0,1',
        'veiculo_id' => 'required|numeric',
        'motorista_id' => 'nullable|numeric',
        'documentos' => 'required|array|min:1',
        'documentos.*.tipo_documento' => 'required|in:NFe,NFCom',
        'documentos.*.chave' => 'required|size:44',
        'cfop' => 'required',
        'uf_inicio' => 'required',
        'mun_ini_codigo' => 'required',
        'mun_ini_nome' => 'required|max:255',
        'uf_fim' => 'required',
        'mun_fim_codigo' => 'required',
        'mun_fim_nome' => 'required|max:255',
        'xProd' => 'required|max:255',
        'qCarga' => 'required|numeric',
        'vCarga' => 'required|numeric',
        'vTPrest' => 'required|numeric',
        'vRec' => 'required|numeric',
        'picms' => 'required|numeric',
        'info_fisco' => 'nullable|max:255',
        'info_contribuinte' => 'nullable|max:255',
    ];

    private const MENSAGENS_VALIDACAO = [
        'required' => 'O campo :attribute é obrigatório!',
        'numeric' => 'O campo :attribute deve ser um valor numérico!',
        'max' => 'O campo :attribute pode ter no máximo :max dígitos!',
        'size' => 'O campo :attribute deve ter :size caracteres!',
    ];

    public function index()
    {
        try {
            $ctes = CTe::where('empresa_id', Auth::user()->empresa_id)
                ->with('remetente', 'destinatario')
                ->latest('data')
                ->get();

            return view('ctes.index', ['ctes' => $ctes]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function create()
    {
        try {
            $clientes = $this->clientesService->todos(Auth::user()->empresa_id);
            $veiculos = $this->veiculosService->buscarVeiculos(Auth::user()->empresa_id);
            $motoristas = $this->motoristaService->buscarMotoristas(Auth::user()->empresa_id);

            return view('ctes.create', ['clientes' => $clientes, 'veiculos' => $veiculos, 'motoristas' => $motoristas]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate(self::CAMPOS_VALIDACAO, self::MENSAGENS_VALIDACAO);

            $empresa = $this->empresaService->buscarEmpresa(Auth::user()->empresa_id);

            DB::beginTransaction();
            $cte = CTe::create([
                'numero' => ($empresa->ultimaCTe ?? 0) + 1,
                'data' => now(),
                'serie' => $empresa->serie,
                'situacao' => EstadoEnum::PENDENTE->value,
                'empresa_id' => $empresa->id,
                'remetente_id' => $request->remetente_id,
                'destinatario_id' => $request->destinatario_id,
                'toma' => $request->toma,
                'veiculo_id' => $request->veiculo_id,
                'motorista_id' => $request->motorista_id,
                'cfop' => $request->cfop,
                'uf_inicio' => $request->uf_inicio,
                'mun_ini_codigo' => $request->mun_ini_codigo,
                'mun_ini_nome' => $request->mun_ini_nome,
                'uf_fim' => $request->uf_fim,
                'mun_fim_codigo' => $request->mun_fim_codigo,
                'mun_fim_nome' => $request->mun_fim_nome,
                'xProd' => $request->xProd,
                'qCarga' => $request->qCarga,
                'vCarga' => $request->vCarga,
                'vTPrest' => $request->vTPrest,
                'vRec' => $request->vRec,
                'picms' => $request->picms,
                'info_fisco' => $request->info_fisco,
                'info_contribuinte' => $request->info_contribuinte,
            ]);

            foreach ($request->documentos as $documento) {
                $cte->documentos()->create($documento);
            }
            DB::commit();

            return redirect()->route('cte.index')->with('success', 'CTe foi criado com sucesso!');
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
            $cte = CTe::with('documentos', 'remetente', 'destinatario')->findOrFail($id);
            if ($cte->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas CTes Pendentes podem ser editados!');
            }
            $clientes = $this->clientesService->todos(Auth::user()->empresa_id);
            $veiculos = $this->veiculosService->buscarVeiculos(Auth::user()->empresa_id);
            $motoristas = $this->motoristaService->buscarMotoristas(Auth::user()->empresa_id);

            return view('ctes.edit', ['cte' => $cte, 'clientes' => $clientes, 'veiculos' => $veiculos, 'motoristas' => $motoristas]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate(self::CAMPOS_VALIDACAO, self::MENSAGENS_VALIDACAO);

            $cte = CTe::findOrFail($id);
            if ($cte->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas CTes Pendentes podem ser editados!');
            }

            DB::beginTransaction();
            $cte->update([
                'remetente_id' => $request->remetente_id,
                'destinatario_id' => $request->destinatario_id,
                'toma' => $request->toma,
                'veiculo_id' => $request->veiculo_id,
                'motorista_id' => $request->motorista_id,
                'cfop' => $request->cfop,
                'uf_inicio' => $request->uf_inicio,
                'mun_ini_codigo' => $request->mun_ini_codigo,
                'mun_ini_nome' => $request->mun_ini_nome,
                'uf_fim' => $request->uf_fim,
                'mun_fim_codigo' => $request->mun_fim_codigo,
                'mun_fim_nome' => $request->mun_fim_nome,
                'xProd' => $request->xProd,
                'qCarga' => $request->qCarga,
                'vCarga' => $request->vCarga,
                'vTPrest' => $request->vTPrest,
                'vRec' => $request->vRec,
                'picms' => $request->picms,
                'info_fisco' => $request->info_fisco,
                'info_contribuinte' => $request->info_contribuinte,
            ]);

            $cte->documentos()->delete();
            foreach ($request->documentos as $documento) {
                $cte->documentos()->create($documento);
            }
            DB::commit();

            return redirect()->route('cte.index')->with('success', 'CTe atualizado com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate(['cte_id' => 'required|numeric']);
            $cte = CTe::findOrFail($request->cte_id);
            if ($cte->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas CTes Pendentes podem ser excluídos!');
            }
            DB::beginTransaction();
            $cte->documentos()->delete();
            $cte->delete();
            DB::commit();

            return redirect()->route('cte.index')->with('success', 'CTe excluído com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function enviarCTe($id)
    {
        try {
            $cte = CTe::with('documentos', 'remetente.endereco', 'destinatario.endereco', 'veiculo.proprietario', 'empresa.endereco')->findOrFail($id);
            if ($cte->situacao !== EstadoEnum::PENDENTE) {
                return back()->with('warning', 'Apenas CTes Pendentes podem ser enviados!');
            }

            $cteService = $this->makeCTeService($cte->empresa);
            DB::beginTransaction();
            $xml = $cteService->gerarXml($cte, $cte->empresa);
            if (isset($xml['erros_xml'])) {
                $cte->situacao = EstadoEnum::REJEITADO->value;
                $cte->save();
                DB::commit();

                return redirect()->route('cte.index')->with('warning', implode(' | ', (array) $xml['erros_xml']));
            }

            $signedXml = $cteService->sign($xml['xml']);
            $result = $cteService->transmitir($signedXml);
            if (isset($result['sucesso'])) {
                $this->empresaService->incrementLastCTe($cte->empresa_id);
                $cte->chave = $xml['chave'];
                $cte->numero = $xml['nCT'];
                $cte->nProtocolo = $result['nProt'];
                $cte->xml = $result['xml'];
                $cte->situacao = EstadoEnum::AUTORIZADO->value;
                $cte->save();
                $this->fluxoCaixaService->registrarEntradaAutomatica(
                    $cte->empresa_id,
                    $cte->vRec,
                    'CTe #'.$cte->numero.' - '.$cte->destinatario->nome,
                    $cte->data,
                    'CTe',
                    $cte->id
                );
                DB::commit();

                return redirect()->route('cte.index')->with('success', 'CTe enviado com sucesso!');
            }

            $cte->situacao = EstadoEnum::REJEITADO->value;
            $cte->save();
            DB::commit();

            return redirect()->route('cte.index')->with('warning', $result['erro']);
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function cancelarCTe(Request $request)
    {
        try {
            $request->validate([
                'cte_id' => 'required|numeric',
                'justificativa' => 'required|min:15',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'min' => 'O campo :attribute deve ter no mínimo :min caracteres!',
            ]);

            $cte = CTe::findOrFail($request->cte_id);
            if ($cte->situacao !== EstadoEnum::AUTORIZADO) {
                return back()->with('warning', 'Apenas CTes Autorizados podem ser cancelados!');
            }

            $cteService = $this->makeCTeService($cte->empresa);
            DB::beginTransaction();
            $result = $cteService->cancelar($cte, $request->justificativa);
            if (isset($result['erro'])) {
                DB::rollBack();

                return back()->with('warning', $result['erro']);
            }

            $cte->situacao = EstadoEnum::CANCELADO->value;
            $cte->nProtocolo = $result['nProt'] ?? $cte->nProtocolo;
            $cte->save();
            $this->fluxoCaixaService->estornarPorOrigem('CTe', $cte->id);
            DB::commit();

            return redirect()->route('cte.index')->with('success', 'CTe cancelado com sucesso!');
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
            $cte = CTe::findOrFail($id);
            if (empty($cte->xml)) {
                return back()->with('warning', 'Esse CTe ainda não foi autorizado, não há DACTE disponível!');
            }
            $dacte = new Dacte($cte->xml);
            $pdf = $dacte->render();

            return response($pdf)->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes! Erro: '.$e->getMessage());
        }
    }

    public function downloadXml($id)
    {
        try {
            $cte = CTe::findOrFail($id);
            if (empty($cte->xml)) {
                return back()->with('warning', 'Esse CTe ainda não foi autorizado, não há XML disponível!');
            }
            header('Content-disposition: attachment; filename="'.$cte->chave.'.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
            echo $cte->xml;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }
}
