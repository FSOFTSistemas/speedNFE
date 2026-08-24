<?php

namespace App\Http\Controllers;

use App\Models\NFSeIndOp;
use App\Models\NFSeNbs;
use App\Models\NFSeServicoNacional;
use App\Services\ServicosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ServicosController extends Controller
{
    private ServicosService $servicosService;

    public function __construct(ServicosService $servicosService)
    {
        $this->servicosService = $servicosService;
    }

    private function rules(): array
    {
        return [
            'codigo' => 'required',
            'descricao' => 'required',
            'cClass' => 'required',
            'cTribNac' => 'nullable|digits:6',
            'cTribMun' => 'nullable',
            'cNBS' => 'nullable|digits:9',
            'cIndOp' => 'nullable|digits:6',
            'cClassTrib' => 'nullable|digits:6',
            'tribISSQN' => 'nullable',
            'tpRetISSQN' => 'nullable',
            'pAliqISSQN' => 'nullable|numeric',
            'cst_ibs_cbs' => 'nullable|digits:3',
            'pRedutorIBSCBS' => 'nullable|numeric',
            'finNFSe' => 'nullable',
            'indFinal' => 'nullable',
            'indDest' => 'nullable',
            'uMed' => 'required',
            'valor' => 'required|numeric',
            'cfop' => 'nullable',
            'icms_cst' => 'nullable',
            'icms_pICMS' => 'nullable|numeric',
            'icms_pFCP' => 'nullable|numeric',
            'icms_orig' => 'nullable',
            'icms_csosn' => 'nullable',
            'pis_cst' => 'nullable',
            'pis_pPIS' => 'nullable|numeric',
            'cofins_cst' => 'nullable',
            'cofins_pCOFINS' => 'nullable|numeric',
            'fust_pFUST' => 'nullable|numeric',
            'funttel_pFUNTTEL' => 'nullable|numeric',
        ];
    }

    public function index()
    {
        try {
            $servicos = $this->servicosService->todos(Auth::user()->empresa_id);

            return view('servicos.index', [
                'servicos' => $servicos,
                'servicosNacionais' => NFSeServicoNacional::query()->orderBy('codigo')->get(),
                'nbsList' => NFSeNbs::query()->orderBy('codigo')->get(),
                'indOps' => NFSeIndOp::query()->orderBy('codigo')->get(),
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function buscarDominioNFSe(Request $request, string $tipo)
    {
        $termo = trim((string) $request->get('q', ''));
        $pagina = max((int) $request->get('page', 1), 1);
        $limite = 20;
        $offset = ($pagina - 1) * $limite;

        $query = match ($tipo) {
            'servicos-nacionais' => NFSeServicoNacional::query()->orderBy('codigo'),
            'nbs' => NFSeNbs::query()->orderBy('codigo'),
            'indicadores-operacao' => NFSeIndOp::query()->orderBy('codigo'),
            default => null,
        };

        if (! $query) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $termoNumericoNbs = $tipo === 'nbs' && $termo !== '' && $this->digitsOnly($termo) === $termo;

        if ($termo !== '' && ! $termoNumericoNbs) {
            $query->where(function ($query) use ($termo, $tipo) {
                $query->where('codigo', 'like', "%{$termo}%");

                if ($tipo === 'indicadores-operacao') {
                    $query->orWhere('tipo_operacao', 'like', "%{$termo}%")
                        ->orWhere('local_operacao', 'like', "%{$termo}%")
                        ->orWhere('caracteristica_fornecimento', 'like', "%{$termo}%");
                    return;
                }

                $query->orWhere('descricao', 'like', "%{$termo}%");
            });
        }

        $itens = $termoNumericoNbs
            ? $query->get()
            : $query->offset($offset)->limit($tipo === 'nbs' ? 200 : $limite + 1)->get();

        if ($tipo === 'nbs') {
            $itens = $itens->filter(function ($item) use ($termoNumericoNbs, $termo) {
                $codigo = $this->digitsOnly($item->codigo);
                if (strlen($codigo) !== 9) {
                    return false;
                }

                return ! $termoNumericoNbs || str_contains($codigo, $termo);
            })->values();
        }

        if ($termoNumericoNbs) {
            $itens = $itens->slice($offset)->values();
        }

        $temMais = $itens->count() > $limite;

        return response()->json([
            'results' => $itens->take($limite)->map(fn ($item) => [
                'id' => $this->codigoDominioNFSe($tipo, $item),
                'text' => $this->textoDominioNFSe($tipo, $item),
            ])->values(),
            'pagination' => ['more' => $temMais],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate($this->rules(), $this->messages());
            $data = $request->only(array_keys($this->rules()));
            $data['empresa_id'] = Auth::user()->empresa_id;
            $this->servicosService->store($data);

            return redirect()->route('servicos.index')->with('success', 'Serviço cadastrado com sucesso!');
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }

            return back()->with('warning', implode(PHP_EOL, $errors))->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate($this->rules(), $this->messages());
            $data = $request->only(array_keys($this->rules()));
            $this->servicosService->update($id, $data);

            return redirect()->route('servicos.index')->with('success', 'Serviço atualizado com sucesso!');
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }

            return back()->with('warning', implode(PHP_EOL, $errors))->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->validate(['servico_id' => 'required|numeric']);
            $this->servicosService->destroy($request->servico_id);

            return redirect()->route('servicos.index')->with('success', 'Serviço excluído com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    private function textoDominioNFSe(string $tipo, $item): string
    {
        if ($tipo === 'indicadores-operacao') {
            return trim($item->codigo.' - '.$item->tipo_operacao.' '.$item->local_operacao.' '.$item->caracteristica_fornecimento);
        }

        return $item->codigo.' - '.$item->descricao;
    }

    private function codigoDominioNFSe(string $tipo, $item): string
    {
        if ($tipo === 'servicos-nacionais') {
            return str_pad($this->digitsOnly($item->codigo), 6, '0', STR_PAD_LEFT);
        }

        if ($tipo === 'nbs') {
            return $this->digitsOnly($item->codigo);
        }

        return $item->codigo;
    }

    private function digitsOnly($value): string
    {
        return preg_replace('/\D/', '', (string) $value);
    }

    private function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'numeric' => 'O campo :attribute deve ser um valor numérico!',
            'digits' => 'O campo :attribute deve ter :digits dígitos!',
        ];
    }
}
