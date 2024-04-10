<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Services\CategoriasService;
use App\Services\EmpresasService;
use App\Services\ImportProductsService;
use App\Services\PedidosService;
use App\Services\ProdutosService;
use App\Utils\FormatationUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProdutosController extends Controller
{
    private ProdutosService $produtoServices;
    private CategoriasService $categoriaServices;
    private EmpresasService $empresaServices;
    private PedidosService $pedidoServices;

    public function __construct(ProdutosService $produtoServices, CategoriasService $categoriaServices, EmpresasService $empresaServices, PedidosService $pedidoServices)
    {
        $this->produtoServices = $produtoServices;
        $this->categoriaServices = $categoriaServices;
        $this->empresaServices = $empresaServices;
        $this->pedidoServices = $pedidoServices;
    }

    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'categoria' => 'required',
                'codigo' => 'nullable',
                'produto' => 'required|max:255',
                'ncm' => 'required',
                'precocusto' => 'required',
                'precovenda' => 'required',
                'un' => 'required',
                'tpProd' => 'nullable',
                'cfopinterno' => 'required',
                'cfopexterno' => 'required',
                'cst' => 'required',
                'cst_pis' => 'required',
                'cst_cofins' => 'required',
                'cofins' => 'required',
                'icms' => 'required',
                'cst_csosn' => 'required',
                'pis' => 'required',
                'ipi' => 'required',
                'tpVeic' => 'nullable|numeric',
                'chassiVeic' => 'nullable',
                'renavanVeic' => 'nullable',
                'anoFabVeic' => 'nullable|numeric',
                'anoModVeic' => 'nullable|numeric',
                'pesoLVeic' => 'nullable|numeric',
                'pesoBVeic' => 'nullable|numeric',
                'distVeic' => 'nullable|numeric',
                'combVeic' => 'nullable|numeric',
                'nMotorVeic' => 'nullable',
                'cvVeic' => 'nullable',
                'cm3Veic' => 'nullable',
                'serieVeic' => 'nullable',
                'tpPVeic' => 'nullable',
                'corVeic' => 'nullable',
                'cCorVeic' => 'nullable|numeric',
                'cCorMontVeic' => 'nullable',
                'cMarcaVeic' => 'nullable',
                'condVeic' => 'nullable|numeric',
                'espVeic' => 'nullable|numeric',
                'vinVeic' => 'nullable',
                'lotVeic' => 'nullable',
                'restriVeic' => 'nullable|numeric',
                'cargaVeic' => 'nullable',
                'operVeic' => 'nullable|numeric'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
                'max' => 'O campo :attribute deve ter no máximo :max caracteres!'
            ]);
            DB::beginTransaction();
            $produto = $this->produtoServices->salvar(
                $id,
                $request->categoria,
                $request->codigo,
                $request->produto,
                $request->precocusto,
                $request->precovenda,
                $request->ncm,
                $request->cfopinterno,
                $request->cst_csosn,
                $request->cst_pis,
                $request->cst_cofins,
                $request->tpProd ? $request->tpVeic : null,
                $request->tpProd ? $request->chassiVeic : null,
                $request->tpProd ? $request->renavanVeic : null,
                $request->tpProd ? $request->anoFabVeic : null,
                $request->tpProd ? $request->anoModVeic : null,
                $request->tpProd ? $request->pesoLVeic : null,
                $request->tpProd ? $request->pesoBVeic : null,
                $request->tpProd ? $request->distVeic : null,
                $request->tpProd ? $request->combVeic : null,
                $request->tpProd ? $request->nMotorVeic : null,
                $request->tpProd ? $request->cvVeic : null,
                $request->tpProd ? $request->cm3Veic : null,
                $request->tpProd ? $request->serieVeic : null,
                $request->tpProd ? $request->tpPVeic : null,
                $request->tpProd ? $request->corVeic : null,
                $request->tpProd ? $request->cCorVeic : null,
                $request->tpProd ? $request->cCorMontVeic : null,
                $request->tpProd ? $request->cMarcaVeic : null,
                $request->tpProd ? $request->condVeic : null,
                $request->tpProd ? $request->espVeic : null,
                $request->tpProd ? $request->vinVeic : null,
                $request->tpProd ? $request->lotVeic : null,
                $request->tpProd ? $request->restriVeic : null,
                $request->tpProd ? $request->cargaVeic : null,
                $request->tpProd ? $request->operVeic : null,
                $request->cst,
                $request->icms,
                $request->pis,
                $request->cofins,
                $request->ipi,
                $request->cfopexterno,
                $request->un,
            );
            DB::commit();
            return redirect()->route('editar_produto', [$produto->id])->with('success', 'Produto editado com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function editar($id)
    {
        try {
            $produto = $this->produtoServices->um($id);
            $categorias = $this->categoriaServices->todas($produto->empresa_id);
            $cfops = $this->pedidoServices->cfopAll();
            $ncms = $this->pedidoServices->ncmAll();
            return view('produtos.editar', ['produto' => $produto, 'categorias' => $categorias, 'cfops' => $cfops, 'ncms' => $ncms]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function destroy(Request $request)
    {
        try {
            $this->produtoServices->destroy($request->idProduto);
            return redirect()->route('produto.index')->with('success', 'Produto excluído com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'empresa' => 'nullable',
                'categoria' => 'required',
                'codigo' => 'nullable',
                'produto' => 'required|max:255',
                'ncm' => 'required',
                'precocusto' => 'required',
                'precovenda' => 'required',
                'un' => 'required',
                'tpProd' => 'nullable',
                'cfopinterno' => 'required',
                'cfopexterno' => 'required',
                'cst' => 'required',
                'cst_pis' => 'required',
                'cst_cofins' => 'required',
                'cofins' => 'required',
                'icms' => 'required',
                'cst_csosn' => 'required',
                'pis' => 'required',
                'ipi' => 'required',
                'tpVeic' => 'nullable|numeric',
                'chassiVeic' => 'nullable',
                'renavanVeic' => 'nullable',
                'anoFabVeic' => 'nullable|numeric',
                'anoModVeic' => 'nullable|numeric',
                'pesoLVeic' => 'nullable|numeric',
                'pesoBVeic' => 'nullable|numeric',
                'distVeic' => 'nullable|numeric',
                'combVeic' => 'nullable|numeric',
                'nMotorVeic' => 'nullable',
                'cvVeic' => 'nullable',
                'cm3Veic' => 'nullable',
                'serieVeic' => 'nullable',
                'tpPVeic' => 'nullable',
                'corVeic' => 'nullable',
                'cCorVeic' => 'nullable|numeric',
                'cCorMontVeic' => 'nullable',
                'cMarcaVeic' => 'nullable',
                'condVeic' => 'nullable|numeric',
                'espVeic' => 'nullable|numeric',
                'vinVeic' => 'nullable',
                'lotVeic' => 'nullable',
                'restriVeic' => 'nullable|numeric',
                'cargaVeic' => 'nullable',
                'operVeic' => 'nullable|numeric'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
                'max' => 'O campo :attribute deve ter no máximo :max caracteres!'
            ]);
            DB::beginTransaction();
            !$request->empresa ? $empresa = Auth::user()->empresa_id : $empresa = $request->empresa;
            if ($this->produtoServices->contagemProdutos($empresa) < $this->empresaServices->buscarEmpresa($empresa)->limProdutos || $empresa == 1) {
                $this->produtoServices->store(
                    $request->categoria,
                    $empresa,
                    $request->codigo,
                    $request->produto,
                    $request->precocusto,
                    $request->precovenda,
                    $request->ncm,
                    $request->cfopinterno,
                    $request->cst_csosn,
                    $request->cst_pis,
                    $request->cst_cofins,
                    $request->cst,
                    $request->icms,
                    $request->pis,
                    $request->cofins,
                    $request->ipi,
                    $request->cfopexterno,
                    $request->un,
                    $request->tpProd ? 1 : 0,
                    $request->tpProd ? $request->tpVeic : null,
                    $request->tpProd ? $request->chassiVeic : null,
                    $request->tpProd ? $request->renavanVeic : null,
                    $request->tpProd ? $request->anoFabVeic : null,
                    $request->tpProd ? $request->anoModVeic : null,
                    $request->tpProd ? $request->pesoLVeic : null,
                    $request->tpProd ? $request->pesoBVeic : null,
                    $request->tpProd ? $request->distVeic : null,
                    $request->tpProd ? $request->combVeic : null,
                    $request->tpProd ? $request->nMotorVeic : null,
                    $request->tpProd ? $request->cvVeic : null,
                    $request->tpProd ? $request->cm3Veic : null,
                    $request->tpProd ? $request->serieVeic : null,
                    $request->tpProd ? $request->tpPVeic : null,
                    $request->tpProd ? $request->corVeic : null,
                    $request->tpProd ? $request->cCorVeic : null,
                    $request->tpProd ? $request->cCorMontVeic : null,
                    $request->tpProd ? $request->cMarcaVeic : null,
                    $request->tpProd ? $request->condVeic : null,
                    $request->tpProd ? $request->espVeic : null,
                    $request->tpProd ? $request->vinVeic : null,
                    $request->tpProd ? $request->lotVeic : null,
                    $request->tpProd ? $request->restriVeic : null,
                    $request->tpProd ? $request->cargaVeic : null,
                    $request->tpProd ? $request->operVeic : null
                );
            }
            DB::commit();
            return redirect()->route('produto.index')->with('success', 'Produto cadastrado com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors))->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function view($id)
    {
        try {
            $produto = $this->produtoServices->um($id);
            return view('produtos.view', ['produto' => $produto]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function show()
    {
        try {
            $user = Auth::user();
            $produtos = $this->produtoServices->todos($user->empresa_id);
            return view('produtos.todos', ['produtos' => $produtos, 'empresa' => $user->empresa_id]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }

    public function new ()
    {
        try {
            $user = Auth::user();
            $empresas = $this->empresaServices->todas();
            $categorias = Categoria::all();
            $cfops = $this->pedidoServices->cfopAll();
            $ncms = $this->pedidoServices->ncmAll();
            return view('produtos.new', ['user' => $user, 'empresas' => $empresas, 'categorias' => $categorias, 'cfops' => $cfops, 'ncms' => $ncms]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function importProducts (Request $request)
    {
        try {
            $emitente = $this->empresaServices->buscarEmpresa(Auth::user()->empresa_id);
            $importService = new ImportProductsService([
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
            $result = $importService->importProducts($request->chaveNota);
            dd($result);
            // return redirect()->route('');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }
}
