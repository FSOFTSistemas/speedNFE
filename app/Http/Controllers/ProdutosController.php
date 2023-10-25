<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Produto;
use App\Services\CategoriasService;
use App\Services\EmpresasService;
use Illuminate\Http\Request;
use App\Services\ProdutosService;
use App\Services\UsersService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdutosController extends Controller
{
    public function updat($id, Request $request){
        $sProdutos = new ProdutosService();
        $resp = $sProdutos->salvar($id, $request->codigo, $request->produto, $request->precocusto, $request->precovenda, $request->ncm, $request->cfopinterno, $request->cst_csosn, $request->cst_pis, $request->cst_cofins, $request->cst, $request->icms, $request->pis, $request->cofins, $request->ipi, $request->cfopexterno, $request->un);

        if(is_int($resp)){
            return redirect('/produto')->with('success', 'Produto editado com sucesso');
        }
        return redirect('/produto')->with('error', 'Não foi possível editar o produto');
    }

    public function editar($id){
        $sProdutos = new ProdutosService();
        $produto = $sProdutos->um($id);

        $sUsers = new UsersService();
        $sEmpresas = new EmpresasService();
        $sCategorias = new CategoriasService();
        $empresa = $sUsers->getEmpresa(Auth::id());
       
        $empresaAnterior = Empresa::find($produto->empresa_id);
        $categoriaAnterior = Categoria::find($produto->categoria_id);

        return view('produtos.editar', ['produto' => $produto,'categoriaAnterior'=>$categoriaAnterior,'empresaAnterior' =>$empresaAnterior, 'empresas' => $sEmpresas->todas(),'categorias' => $sCategorias->todas($empresa->empresa_id),'empresa'=>$empresa->empresa_id]);
    }

    public function destroy($id){
        $sProdutos = new ProdutosService();
        $resp = $sProdutos->destroy($id);

        // return $resp;

        if($resp == 1){
            return redirect('/produto')->with('success', 'Produto excluído com sucesso');
        }
        return redirect('/produto')->with('error', 'Não foi possível excluir o produto');
    }

    public function store(Request $request){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        if($request->has('empresa')){
            $empresa = $request->empresa;
        } else {
            $empresa = $empresa->empresa_id;
        }

        if(DB::table('produtos')
        ->where('empresa_id', '=', $empresa)
        ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
        ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
        ->count() < Empresa::findOrFail($empresa)->limProdutos or $empresa == 1){
            $sProdutos = new ProdutosService();
            $resp = $sProdutos->store($request->categoria, $empresa, $request->codigo, $request->produto, $request->precocusto, $request->precovenda, $request->ncm, $request->cfopinterno, $request->cst_csosn, $request->cst_pis, $request->cst_cofins, $request->cst, $request->icms, $request->pis, $request->cofins, $request->ipi, $request->cfopexterno, $request->un);
        } else {
            return redirect('/produto')->with('error', 'Limite de produtos atingido');
        }

        if ($resp == 1) {
            return redirect('/produto')->with('success', 'Produto cadastrado com sucesso');
        }
        return $resp;
    }

    public function show(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sProdutos = new ProdutosService();
        $produtos = $sProdutos->todos($empresa->empresa_id);

        return view('produtos.todos', ['produtos' => $produtos, 'empresa' => $empresa->empresa_id]);
    }

    public function new(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sEmpresas = new EmpresasService();

        $sCategorias = new CategoriasService();

        return view('produtos.new', ['empresa' => $empresa->empresa_id, 'empresas' => $sEmpresas->todas(), 'categorias' => $sCategorias->todas($empresa->empresa_id)]);
    }
}
