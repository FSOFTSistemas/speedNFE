<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EmpresasService;
use Illuminate\Http\Request;
use App\Services\FormaPagService;
use App\Services\UsersService;
use Illuminate\Support\Facades\Auth;

class FormaPagController extends Controller
{
    public function show(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sForma = new FormaPagService();
        $formas = $sForma->todos($empresa->empresa_id);

        return view('formas.todos', ['formas' => $formas, 'empresa' => $empresa->empresa_id]);
    }

    public function new(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sEmpresa = new EmpresasService();
        $empresas = $sEmpresa->todas();

        return view('formas.new', ['empresa' => $empresa->empresa_id, 'empresas' => $empresas]);
    }

    public function store(Request $request){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sForma = new FormaPagService();

        if($request->has('empresa')){
            $empresa = $request->empresa;
        } else {
            $empresa = $empresa->empresa_id;
        }

        $resp =  $sForma->store($request->descricao, $empresa);

        if ($resp == 1){
            return redirect('/forma')->with('success', 'Forma de Pagamento cadastrada com sucesso');
        }
        return redirect('/forma')->with('error', 'Não foi possível cadastrar a Forma de Pagamento');
    }

    public function excluir($id){
        $sForma = new FormaPagService();
        $resp = $sForma->excluir($id);

        if($resp == 1){
            return redirect('/forma')->with('success', 'Forma de Pagamento excluída com sucesso');
        }
        return redirect('/forma')->with('error', 'Não foi possível apagar a forma de pagamento');
    }
}
