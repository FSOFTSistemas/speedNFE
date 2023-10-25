<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UsersService;
use App\Services\CategoriasService;
use App\Services\EmpresasService;
use Illuminate\Support\Facades\Auth;

class CategoriasController extends Controller
{
    public function destroy($id){
        $sCategorias = new CategoriasService();
        $resp = $sCategorias->status($id);

        if(is_int($resp)){
            return redirect('/categoria')->with('success', 'Categoria desativada com sucesso');
        } else {
            return redirect('/categoria')->with('error', $resp);
        }
    }

    public function show(){
        $sEmpresa = new UsersService();
        $empresa = $sEmpresa->getEmpresa(Auth::id());

        $sCategorias = new CategoriasService();
        $categorias = $sCategorias->todas($empresa->empresa_id);

        return view('categorias.todos', ['categorias' => $categorias, 'empresa' => $empresa->empresa_id]);
    }

    public function new(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sEmpresa = new EmpresasService();
        $empresas = $sEmpresa->todas();

        return view('categorias.new', ['empresa' => $empresa->empresa_id, 'empresas' => $empresas]);
    }

    public function store(Request $request){
        $sEmpresa = new UsersService();
        $empresa = $sEmpresa->getEmpresa(Auth::id());

        if($request->has('empresa')){
            $empresa = $request->empresa;
        } else {
            $empresa = $empresa->empresa_id;
        }

        $sCategorias = new CategoriasService();
        $resp = $sCategorias->store($request->descricao, $empresa, $request->status);

        if(is_int($resp)){
            return redirect('/categoria')->with('success', 'Categoria Cadastrada com sucesso');
        } else {
            return redirect('/categoria')->with('error', $resp);
        }
    }
}
