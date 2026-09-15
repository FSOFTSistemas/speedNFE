<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyExistException;
use App\Models\Entrada;
use App\Models\Estoque;
use App\Services\EmpresasService;
use App\Services\EntradaService;
use App\Services\EstoquesService;
use App\Services\ImportProductsService;
use App\Services\ItemEntradaService;
use App\Services\ProdutosService;
use App\Utils\FormatationUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use NFePHP\DA\NFe\Danfe;


class EntradaController extends Controller
{
    private $empresaServices;
    private $produtoService;
    private $entradaService;
    private $itemEntradaService;
    private $estoqueService;

    public function __construct(EmpresasService $empresaService, ProdutosService $produtoService, EntradaService $entradaService, ItemEntradaService $itemEntradaService, EstoquesService $estoqueService)
    {
        $this->empresaServices = $empresaService;
        $this->produtoService = $produtoService;
        $this->entradaService = $entradaService;
        $this->itemEntradaService = $itemEntradaService;
        $this->estoqueService = $estoqueService;
    }

    public function index(Request $request)
    {
        try {
            $empresaId = Auth::user()->empresa_id;
            $entradas = $this->entradaService->getEntradas($empresaId);

            $dataInicio = $request->filled('data_inicio')
                ? Carbon::createFromFormat('Y-m-d', $request->input('data_inicio'))->startOfDay()
                : now()->startOfMonth();
            $dataFim = $request->filled('data_fim')
                ? Carbon::createFromFormat('Y-m-d', $request->input('data_fim'))->endOfDay()
                : now()->endOfDay();

            // dataEntrada é armazenada como string, em formatos diferentes
            // conforme a origem do registro ("d/m/Y H:i:s" ou "Y-m-d") - por
            // isso usa um parser tolerante em vez de comparar as strings direto.
            $entradas = $entradas->filter(function ($entrada) use ($dataInicio, $dataFim) {
                $data = FormatationUtil::parseData($entrada->dataEntrada);
                return $data && $data->between($dataInicio, $dataFim);
            });

            if ($request->filled('fornecedor')) {
                $fornecedor = mb_strtolower($request->input('fornecedor'));
                $entradas = $entradas->filter(function ($entrada) use ($fornecedor) {
                    return str_contains(mb_strtolower($entrada->fornecedor ?? ''), $fornecedor);
                });
            }

            if ($request->filled('busca')) {
                $busca = mb_strtolower($request->input('busca'));
                $entradas = $entradas->filter(function ($entrada) use ($busca) {
                    return str_contains(mb_strtolower($entrada->numeroNota ?? ''), $busca)
                        || str_contains(mb_strtolower($entrada->chave ?? ''), $busca);
                });
            }

            if ($request->filled('chassi')) {
                $chassi = mb_strtolower($request->input('chassi'));
                $entradas = $entradas->filter(function ($entrada) use ($chassi) {
                    return $entrada->itens->contains(function ($item) use ($chassi) {
                        return str_contains(mb_strtolower(optional($item->produto)->chassiVeic ?? ''), $chassi);
                    });
                });
            }

            $entradas = $entradas->sortByDesc(function ($entrada) {
                return FormatationUtil::parseData($entrada->dataEntrada) ?? Carbon::createFromTimestamp(0);
            })->values();

            $filtros = [
                'data_inicio' => $dataInicio->format('Y-m-d'),
                'data_fim' => $dataFim->format('Y-m-d'),
                'fornecedor' => $request->input('fornecedor'),
                'busca' => $request->input('busca'),
                'chassi' => $request->input('chassi'),
            ];

            return view('nfeEntrada.entradas', ['entradas' => $entradas, 'filtros' => $filtros]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro interno, tente novamente em outro momento ou entre em contato com nosso suporte!');
        }
    }

    public function create()
    {
        try {
            return view('nfeEntrada.create');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro interno, tente novamente em outro momento ou entre em contato com nosso suporte!');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'natOp' => 'required',
                'dhEmi' => 'required',
                'dhSaiEnt' => 'required',
                'chNFe' => 'required|unique:entradas,chave',
                'vNF' => 'required|numeric',
                'fornecedor' => 'required',
                'CNPJ' => 'required',
                'IE' => 'required',
                // 'fone' => 'required',
                'rua' => 'required',
                'nro' => 'required',
                'bairro' => 'required',
                'mun' => 'required',
                'uf' => 'required',
                'CEP' => 'required',
                'prods' => 'required|array'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'unique' => 'Nota (' . $request->chNFe . ') já foi importada anteriormente!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
                'array' => 'O campo :attribute deve ser uma lista de produtos!'
            ]);


            DB::beginTransaction();
            $entradaId = $this->entradaService->createEntrada($request, Auth::user()->empresa_id);
            $productsList = $this->produtoService->insertProductsList($request->prods, Auth::user()->empresa_id);
            foreach ($productsList as $prod) {
                $this->estoqueService->create($prod['qtde'], Auth::user()->empresa_id, $prod['produtoId']);
            }
            $this->itemEntradaService->createInputItems($entradaId, $productsList, Auth::user()->empresa_id);
            DB::commit();
            return redirect()->route('entradas.index')->with('success', 'Produtos importados com sucesso!');
        } catch (ValidationException $e) {
            DB::rollBack();
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            return redirect()->route('entradas.index')->with('warning', implode(PHP_EOL, $errors));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('entradas.index')->with('error', 'Erro interno, tente novamente em outro momento ou entre em contato com nosso suporte!');
        }
    }

    public function importProducts(Request $request)
    {
        try {
            $request->validate([
                'type' => 'nullable',
                'nota' => 'required',
            ]);
            $companyId = Auth::user()->empresa_id;
            if (isset($request->type)) {
                $response = ImportProductsService::readXML($request->nota);
            } else {
                $emitente = $this->empresaServices->buscarEmpresa($companyId);
                $importProductsServices = new ImportProductsService([
                    "atualizacao" => date('Y-m-d h:i:s'),
                    "tpAmb" => (int) $emitente->ambiente,
                    "razaosocial" => $emitente->razao,
                    "siglaUF" => $emitente->endereco->uf,
                    "cnpj" => FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj),
                    "schemes" => "PL_009_V4",
                    "versao" => "4.00",
                    "tokenIBPT" => "AAAAAAA",
                    "CSC" => $emitente->csc,
                    "CSCid" => '00000' . $emitente->idCsc,
                ], $emitente);
                $response = $importProductsServices->importProducts($request->nota);
            }
            $this->entradaService->entradaExist((string) $response['nota']['chNFe']);
            return view('nfeEntrada.create', ['data' => $response]);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (AlreadyExistException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function entradaManual()
    {
        return view('nfeEntrada.nfeEntradamanual');
    }

    public function destroy($id)
    {
        try {
            $entrada = Entrada::findOrFail($id);

            // Atualizar o estoque revertendo as quantidades (opcional)
            foreach ($entrada->itensEntradas as $item) {
                $estoque = Estoque::where('produto_id', $item->produto_id)
                    ->where('empresa_id', $entrada->empresa_id)
                    ->first();

                if ($estoque) {
                    $qtdeRemover = $item->qtde;
                    $estoqueAnterior = $estoque->estoque_atual ?? 0;
                    $estoque->estoque_atual = max(0, $estoqueAnterior - $qtdeRemover);
                    $estoque->saidas = ($estoque->saidas ?? 0) + min($estoqueAnterior, $qtdeRemover);
                    $estoque->save();
                }
            }

            // Deletar a entrada
            $entrada->delete();

            return redirect()->route('entradas.index')->with('success', 'Entrada deletada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('entradas.index')->with('error', 'Erro ao deletar entrada: ' . $e->getMessage());
        }
    }

    public function pdf($id)
    {
        try {
            $entrada = Entrada::findOrFail($id);

            if (Auth::user()->empresa_id != 1 && $entrada->empresa_id != Auth::user()->empresa_id) {
                abort(403);
            }

            if (empty($entrada->xml)) {
                return back()->with('warning', 'O XML dessa entrada não está disponível. Isso acontece com entradas importadas antes dessa funcionalidade, ou pela busca automática por chave na SEFAZ.');
            }

            $danfe = new Danfe($entrada->xml);
            $danfe->creditsIntegratorFooter('SpeedNFE - www.f-softsistemas.com.br', false);
            $pdf = $danfe->render();

            return response($pdf)->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Não foi possível gerar o PDF dessa entrada, tente novamente em outro momento!');
        }
    }
}
