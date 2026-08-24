<?php

namespace App\Http\Controllers;

use App\Enums\EstadoEnum;
use App\Models\Cidade;
use App\Models\NFSeIndOp;
use App\Models\NFSeNbs;
use App\Models\NFSeServicoNacional;
use App\Services\ClientesService;
use App\Services\EmpresasService;
use App\Services\NFSeService;
use App\Services\ServicosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class NFSeController extends Controller
{
    private NFSeService $nfseService;

    private EmpresasService $empresaService;

    private ClientesService $clientesService;

    private ServicosService $servicosService;

    public function __construct(NFSeService $nfseService, EmpresasService $empresaService, ClientesService $clientesService, ServicosService $servicosService)
    {
        $this->nfseService = $nfseService;
        $this->empresaService = $empresaService;
        $this->clientesService = $clientesService;
        $this->servicosService = $servicosService;
    }

    private function rules(): array
    {
        return [
            'cliente_id' => 'required|numeric',
            'servico_id' => 'required|numeric',
            'data_competencia' => 'required|date',
            'cLocPrestacao' => 'nullable|digits:7',
            'cLocIncid' => 'nullable|digits:7',
            'cTribNac' => 'nullable|digits:6',
            'cTribMun' => 'nullable',
            'cNBS' => 'nullable|digits:9',
            'cIndOp' => 'nullable|digits:6',
            'cClassTrib' => 'nullable|digits:6',
            'vServ' => 'required|numeric',
            'vDescIncond' => 'nullable|numeric',
            'vDescCond' => 'nullable|numeric',
            'vDeducaoReducao' => 'nullable|numeric',
            'pAliq' => 'nullable|numeric',
            'vISSQN' => 'nullable|numeric',
            'vTotalRet' => 'nullable|numeric',
            'vIBS' => 'nullable|numeric',
            'vCBS' => 'nullable|numeric',
            'vTotNF' => 'nullable|numeric',
            'discriminacao' => 'required',
            'informacoes_complementares' => 'nullable',
        ];
    }

    public function index()
    {
        try {
            $nfses = $this->nfseService->todas(Auth::user()->empresa_id);

            return view('nfse.index', ['nfses' => $nfses]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('nfse.create', $this->formData());
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate($this->rules(), $this->messages());
            $empresa = $this->empresaService->buscarEmpresa(Auth::user()->empresa_id);
            $nfse = $this->nfseService->criar($data, $empresa);

            if ($request->acao === 'emitir') {
                $response = $this->nfseService->emitir($nfse);

                return redirect()->route('nfse.view', [$nfse->id])->with($response['ok'] ? 'success' : 'warning', $response['message']);
            }

            return redirect()->route('nfse.index')->with('success', 'NFS-e criada como rascunho com sucesso!');
        } catch (ValidationException $e) {
            return back()->with('warning', $this->formatValidation($e))->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $nfse = $this->nfseService->buscar($id, Auth::user()->empresa_id);
            if (! $this->nfseService->podeEditar($nfse)) {
                return back()->with('warning', 'Apenas NFS-e pendentes ou rejeitadas podem ser editadas!');
            }

            return view('nfse.edit', array_merge($this->formData(), ['nfse' => $nfse]));
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate($this->rules(), $this->messages());
            $nfse = $this->nfseService->buscar($id, Auth::user()->empresa_id);
            if (! $this->nfseService->podeEditar($nfse)) {
                return back()->with('warning', 'Apenas NFS-e pendentes ou rejeitadas podem ser editadas!');
            }

            $empresa = $this->empresaService->buscarEmpresa(Auth::user()->empresa_id);
            $nfse = $this->nfseService->atualizar($nfse, $data, $empresa);

            if ($request->acao === 'emitir') {
                $response = $this->nfseService->emitir($nfse);

                return redirect()->route('nfse.view', [$nfse->id])->with($response['ok'] ? 'success' : 'warning', $response['message']);
            }

            return redirect()->route('nfse.index')->with('success', 'NFS-e atualizada com sucesso!');
        } catch (ValidationException $e) {
            return back()->with('warning', $this->formatValidation($e))->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage())->withInput();
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate(['nfse_id' => 'required|numeric']);
            $nfse = $this->nfseService->buscar($request->nfse_id, Auth::user()->empresa_id);
            if (! $this->nfseService->podeExcluir($nfse)) {
                return back()->with('warning', 'Apenas NFS-e pendentes ou rejeitadas podem ser excluídas!');
            }

            $this->nfseService->excluir($nfse);

            return redirect()->route('nfse.index')->with('success', 'NFS-e excluída com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function visualizar($id)
    {
        try {
            $nfse = $this->nfseService->buscar($id, Auth::user()->empresa_id);

            return view('nfse.show', ['nfse' => $nfse]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function enviar($id)
    {
        try {
            $nfse = $this->nfseService->buscar($id, Auth::user()->empresa_id);
            $response = $this->nfseService->emitir($nfse);

            return back()->with($response['ok'] ? 'success' : 'warning', $response['message']);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function downloadXml($id)
    {
        try {
            $nfse = $this->nfseService->buscar($id, Auth::user()->empresa_id);
            $xml = $nfse->xmlAutorizado ?: $nfse->xmlDps;
            if (! $xml) {
                return back()->with('warning', 'Essa NFS-e ainda não possui XML gerado.');
            }

            $filename = ($nfse->chave ?: 'nfse_'.$nfse->id).'_'.$xml->tipo.'.xml';

            return response($xml->xml)
                ->header('Content-Type', 'text/xml; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function cancelar(Request $request)
    {
        try {
            $request->validate([
                'nfse_id' => 'required|numeric',
                'justificativa' => 'required|min:15',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'min' => 'O campo :attribute deve ter no mínimo :min caracteres!',
            ]);

            $nfse = $this->nfseService->buscar($request->nfse_id, Auth::user()->empresa_id);
            if ($nfse->situacao !== EstadoEnum::AUTORIZADO->value) {
                return back()->with('warning', 'Apenas NFS-e autorizadas podem ser canceladas.');
            }

            $this->nfseService->registrarCancelamentoLocal($nfse, $request->justificativa);

            return redirect()->route('nfse.index')->with('success', 'Cancelamento registrado localmente.');
        } catch (ValidationException $e) {
            return back()->with('warning', $this->formatValidation($e));
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    private function formData(): array
    {
        return [
            'clientes' => $this->clientesService->todos(Auth::user()->empresa_id),
            'servicos' => $this->servicosService->todos(Auth::user()->empresa_id),
            'empresa' => $this->empresaService->buscarEmpresa(Auth::user()->empresa_id),
            'servicosNacionais' => NFSeServicoNacional::query()->orderBy('codigo')->get(),
            'nbsList' => NFSeNbs::query()->orderBy('codigo')->get(),
            'indOps' => NFSeIndOp::query()->orderBy('codigo')->get(),
            'cidades' => Cidade::query()->orderBy('cidade')->orderBy('uf')->get(),
        ];
    }

    private function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'numeric' => 'O campo :attribute deve ser um valor numérico!',
            'date' => 'O campo :attribute deve ser uma data válida!',
            'digits' => 'O campo :attribute deve ter :digits dígitos!',
        ];
    }

    private function formatValidation(ValidationException $e): string
    {
        $errors = [];
        foreach ($e->errors() as $error) {
            $errors[] = implode(PHP_EOL, $error);
        }

        return implode(PHP_EOL, $errors);
    }
}
