<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\ProdutosService;
use Illuminate\Http\Request;
use App\Services\UsersService;
use Illuminate\Support\Facades\Auth;

class EstoqueController extends Controller
{
    public function update($id, Request $request){
        $sEstoques = new EstoquesService();
        $resp = $sEstoques->update($id, $request->estoque);

        if ($resp == 1){
            return redirect('/estoque')->with('success', 'Estoque alterado com sucesso');
        }
        return redirect('/estoque')->with('error', 'Não foi possível alterar o estoque');
    }

    public function editar($id){
        $sEstoques = new EstoquesService();
        $estoque = $sEstoques->um($id);

        return view('estoques.editar', ['estoque' => $estoque]);
    }

    public function destroy($id){
        $sEstoques = new EstoquesService();
        $resp = $sEstoques->destroy($id);

        if ($resp == 1){
            return redirect('/estoque')->with('success', 'Estoque destruído com sucesso');
        }
        return $resp;
    }

    public function store(Request $request){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sEstoques = new EstoquesService();

        if($request->has('empresa')){
            $empresa = $request->empresa;
        } else {
            $empresa = $empresa->empresa_id;
        }

        $resp = $sEstoques->store($empresa, $request->produto, $request->estoque);

        if($resp){
            return redirect('/estoque')->with('success', 'Estoque cadastrado com sucesso');
        }
    }

    public function new(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sProdutos = new ProdutosService();
        $produtos = $sProdutos->todos($empresa->empresa_id);

        $sEmpresas = new EmpresasService();

        return view('estoques.new', ['produtos' => $produtos, 'empresa' => $empresa->empresa_id, 'empresas' => $sEmpresas->todas()]);
    }

    public function show(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sEstoques = new EstoquesService();
        $estoques = $sEstoques->show($empresa->empresa_id);

        return view('estoques.todos', ['estoques' => $estoques]);
    }
}
