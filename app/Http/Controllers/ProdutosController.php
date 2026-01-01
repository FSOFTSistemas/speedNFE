<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\CstIbsCbs; // Importante para a Reforma Tributária
use App\Services\CategoriasService;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\PedidosService;
use App\Services\ProdutosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ProdutosController extends Controller
{
    private ProdutosService $produtoServices;
    private CategoriasService $categoriaServices;
    private EmpresasService $empresaServices;
    private PedidosService $pedidoServices;
    private EstoquesService $estoqueService;

    public function __construct(ProdutosService $produtoServices, CategoriasService $categoriaServices, EmpresasService $empresaServices, PedidosService $pedidoServices, EstoquesService $estoqueService)
    {
        $this->produtoServices = $produtoServices;
        $this->categoriaServices = $categoriaServices;
        $this->empresaServices = $empresaServices;
        $this->pedidoServices = $pedidoServices;
        $this->estoqueService = $estoqueService;
    }

    // MÉTODO DE ATUALIZAÇÃO (PUT)
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
                // --- VEÍCULOS ---
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
                'operVeic' => 'nullable|numeric',
                
                // --- REFORMA TRIBUTÁRIA ---
                'cClassTrib' => 'nullable',
                'pIBS' => 'nullable',
                'pCBS' => 'nullable',
                'pIS_imposto' => 'nullable',
                'cst_ibs_cbs' => 'nullable',
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
                doubleval($request->precocusto),
                doubleval($request->precovenda),
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
                $request->tpProd ? doubleval($request->pesoLVeic) : null,
                $request->tpProd ? doubleval($request->pesoBVeic) : null,
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
                doubleval($request->icms),
                $request->pis,
                $request->cofins,
                $request->ipi,
                $request->cfopexterno,
                $request->un,
                // --- NOVOS ARGUMENTOS ---
                $request->cClassTrib,
                doubleval($request->pIBS),
                doubleval($request->pCBS),
                doubleval($request->pIS_imposto),
                $request->cst_ibs_cbs
            );
            
            DB::commit();
            return redirect()->route('editar_produto', [$produto->id])->with('success', 'Produto editado com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode("<br>", $error);
            }
            DB::rollBack();
            return back()->with('warning', implode("<br>", $errors));
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e->getMessage());
        }
    }

    // MÉTODO QUE ABRE A TELA DE EDIÇÃO (Renomeado de view para editar)
    public function editar($id)
    {
        try {
            $user = Auth::user();
            $produto = $this->produtoServices->um($id);

            // Se o produto não for encontrado ou não pertencer à empresa (caso tenha lógica de segurança no service)
            if(!$produto){
                 return redirect()->route('produto.index')->with('warning', 'Produto não encontrado');
            }

            $empresas = $this->empresaServices->todos($user->empresa_id);
            $categorias = Categoria::all();
            $cfops = $this->pedidoServices->cfopAll();
            $ncms = $this->pedidoServices->ncmAll();
            $csts = CstIbsCbs::orderBy('codigo')->get();

            return view('produtos.editar', [
                'user' => $user,
                'produto' => $produto,
                'empresas' => $empresas,
                'categorias' => $categorias,
                'cfops' => $cfops,
                'ncms' => $ncms,
                'csts' => $csts
            ]);

        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    // LISTAGEM
    public function show()
    {
        try {
            $user = Auth::user();
            $produtos = $this->produtoServices->todos($user->empresa_id);
            return view('produtos.todos', ['produtos' => $produtos, 'empresa' => $user->empresa_id]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    // TELA DE NOVO CADASTRO
    public function new()
    {
        try {
            $user = Auth::user();
            $empresas = $this->empresaServices->todos($user->empresa_id);
            $categorias = Categoria::all();
            $cfops = $this->pedidoServices->cfopAll();
            $ncms = $this->pedidoServices->ncmAll();
            $csts = CstIbsCbs::orderBy('codigo')->get(); 

            return view('produtos.new', [
                'user' => $user, 
                'empresas' => $empresas, 
                'categorias' => $categorias, 
                'cfops' => $cfops, 
                'ncms' => $ncms,
                'csts' => $csts
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    // EXCLUIR
    public function destroy($id)
    {
        try {
            $this->produtoServices->destroy($id);
            return redirect()->route('produto.index')->with('success', 'Produto excluído com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    // SALVAR NOVO (POST)
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
                'estoque' => 'nullable|numeric',
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
                'operVeic' => 'nullable|numeric',
                // --- RTC ---
                'cClassTrib' => 'nullable',
                'pIBS' => 'nullable',
                'pCBS' => 'nullable',
                'pIS_imposto' => 'nullable',
                'cst_ibs_cbs' => 'nullable',
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
                'max' => 'O campo :attribute deve ter no máximo :max caracteres!'
            ]);

            DB::beginTransaction();
            !$request->empresa ? $empresa = Auth::user()->empresa_id : $empresa = $request->empresa;
            
            if ($this->produtoServices->contagemProdutos($empresa) < $this->empresaServices->buscarEmpresa($empresa)->limProdutos || $empresa == 1) {
                
                $produto = $this->produtoServices->store(
                    $request->categoria,
                    $empresa,
                    $request->codigo,
                    $request->produto,
                    doubleval($request->precocusto),
                    doubleval($request->precovenda),
                    $request->ncm,
                    $request->cfopinterno,
                    $request->cst_csosn,
                    $request->cst_pis,
                    $request->cst_cofins,
                    $request->cst,
                    doubleval($request->icms),
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
                    $request->tpProd ? doubleval($request->pesoLVeic) : null,
                    $request->tpProd ? doubleval($request->pesoBVeic) : null,
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
                    // --- RTC ---
                    $request->cClassTrib,
                    doubleval($request->pIBS),
                    doubleval($request->pCBS),
                    doubleval($request->pIS_imposto),
                    $request->cst_ibs_cbs
                );
                
                $this->estoqueService->create($request->estoque, $empresa, $produto->id);
            } else {
                return redirect()->route('produto.index')->with('warning', 'O limite de cadastro de produtos foi atingido, faça assinatura de um novo plano para conseguir mais cadastros!');
            }
            DB::commit();
            return redirect()->route('produto.index')->with('success', 'Produto cadastrado com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode("<br>", $error);
            }
            DB::rollBack();
            return back()->with('warning', implode("<br>", $errors))->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        // Busca o produto pelo ID
        $produto = \App\Models\Produto::find($id);

        if (!$produto) {
            return back()->with('error', 'Produto não encontrado!');
        }

        return view('produtos.view', compact('produto')); 
    }
}