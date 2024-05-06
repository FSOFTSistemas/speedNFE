<?php

namespace App\Http\Controllers;

use App\Services\EmpresasService;
use App\Services\EntradaService;
use App\Services\ImportProductsService;
use App\Services\ProdutosService;
use App\Utils\FormatationUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EntradaController extends Controller
{
    private $empresaServices;
    private $produtoService;
    private $entradaService;

    public function __construct(EmpresasService $empresaService, ProdutosService $produtoService, EntradaService $entradaService)
    {
        $this->empresaServices = $empresaService;
        $this->produtoService = $produtoService;
        $this->entradaService = $entradaService;
    }

    public function index()
    {
        try {
            $entradas = [];
            return view('nfeEntrada.entradas', ['entradas' => $entradas]);
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
                'chNFe' => 'required',
                'vNF' => 'required',
                'fornecedor' => 'required',
                'CNPJ' => 'required',
                'IE' => 'required',
                'fone' => 'required',
                'rua' => 'required',
                'nro' => 'required',
                'bairro' => 'required',
                'mun' => 'required',
                'uf' => 'required',
                'CEP' => 'required',
                'prods' => 'required'
            ], [
                'required' => 'O campo :attribute é obrigatório!'
            ]);
            DB::beginTransaction();
            $this->entradaService->createEntrada($request, Auth::user()->empresa_id);
            $this->produtoService->insertProductsList($request->prods, Auth::user()->empresa_id);
            DB::commit();
            return redirect()->route('entradas.index')->with('success', 'Produtos importados com sucesso!');
        } catch (ValidationException $e) {
            dd($e);
            DB::rollBack();
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro interno, tente novamente em outro momento ou entre em contato com nosso suporte!');
        }
    }

    public function importProducts (Request $request)
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
            return view('nfeEntrada.create', ['data' => $response]);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }
}
