<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CategoriasService;
use App\Services\EmpresasService;
use App\Services\ProdutosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutosController extends Controller
{
    private ProdutosService $produtoServices;
    private CategoriasService $categoriaServices;
    private EmpresasService $empresaServices;

    public function __construct(ProdutosService $produtoServices, CategoriasService $categoriaServices, EmpresasService $empresaServices)
    {
        $this->produtoServices = $produtoServices;
        $this->categoriaServices = $categoriaServices;
        $this->empresaServices = $empresaServices;
    }

    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'categoria' => 'required',
                'codigo' => '',
                'produto' => 'required|max:255',
                'ncm' => 'required',
                'precocusto' => 'required',
                'precovenda' => 'required',
                'un' => 'required',
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
            ]);
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
                $request->cst,
                $request->icms,
                $request->pis,
                $request->cofins,
                $request->ipi,
                $request->cfopexterno,
                $request->un,
            );
            return redirect()->route('editar_produto', [$produto->id])->with('success', 'Produto editado com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Não foi possível editar o produto');
        }
    }

    public function editar($id)
    {
        try {
            $produto = $this->produtoServices->um($id);
            $categorias = $this->categoriaServices->todas($produto->empresa_id);
            return view('produtos.editar', ['produto' => $produto, 'categorias' => $categorias]);
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
            return back()->with('error', 'Não foi possível excluir o produto');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'empresa' => '',
                'categoria' => 'required',
                'codigo' => '',
                'produto' => 'required|max:255',
                'ncm' => 'required',
                'precocusto' => 'required',
                'precovenda' => 'required',
                'un' => 'required',
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
            ]);
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
                    $request->un
                );
            }
            return redirect()->route('produto.index')->with('success', 'Produto cadastrado com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Não foi possível cadastrar o produto!');
        }
    }

    public function view($id)
    {
        try {
            $produto = $this->produtoServices->um($id);
            return view('produtos.view', ['produto' => $produto]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function show()
    {
        try {
            $user = Auth::user();
            $produtos = null;
            if ($user->empresa_id != 1) {
                $produtos = $this->produtoServices->todos($user->empresa_id);
            } else {
                $produtos = $this->produtoServices->todosProdutos();
            }
            return view('produtos.todos', ['produtos' => $produtos, 'empresa' => $user->empresa_id]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function new ()
    {
        try {
            $user = Auth::user();
            $empresas = $this->empresaServices->todas();
            $categorias = $this->categoriaServices->todas($user->empresa_id);
            return view('produtos.new', ['user' => $user, 'empresas' => $empresas, 'categorias' => $categorias]);
        } catch (Exception $e) {
            return back();
        }
    }
}
