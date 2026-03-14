<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UsersService;
use App\Services\ReceberService;
use App\Services\ClientesService;
use Illuminate\Support\Facades\Auth;

class ReceberController extends Controller
{
    public function update($id, Request $request){
        $sReceber = new ReceberService();
        $resp = $sReceber->update($id, $request->valor_pago, $request->vencimento);

        if ($resp){
            return redirect('/receber')->with('success', 'Recebimento lançado com sucesso');
        }
        return redirect('/receber')->with('erro', 'Não foi possível realizar o lançamento');

    }

    public function editar($id){
        // puxar variaveis para cliente e empresa
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sClientes = new ClientesService();
        $clientes = $sClientes->todos($empresa->empresa_id);

        $sReceber = new ReceberService();
        $receber = $sReceber->um($id);

        return view('receber.edit', ['recebimento' => $receber]);
    }

    public function destroy($id){
        $sReceber = new ReceberService();
        $response = $sReceber->destroy($id);

        if ($response == 1){
            return redirect('/receber')->with('success', 'Conta a receber excluída com sucesso');
        }
        return redirect('/receber')->with('error', 'Não foi possível excluir a conta a receber');
    }

    public function store(Request $request){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sReceber = new ReceberService();
        $resp = $sReceber->new($empresa->empresa_id, $request->cliente, $request->total, 0, $request->vencimento);

        if ($resp){
            return redirect('/receber')->with('success', 'Conta a receber cadastrada com sucesso');
        } else {
            return redirect('/receber')->with('error', 'Não foi possível cadastrar a conta a receber');
        }

    }

    public function new(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sClientes = new ClientesService();
        $clientes = $sClientes->todos($empresa->empresa_id);

        return view('receber.new', ['clientes' => $clientes]);
    }

    public function show(){
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sReceber = new ReceberService();
        $todas = $sReceber->todas($empresa->empresa_id);

        return view('receber.todos', ['recebimentos' => $todas]);
    }
}
