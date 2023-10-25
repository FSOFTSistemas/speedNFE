<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Services\UsersService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RelatoriosController extends Controller
{
    public function show(){
        $sUser = new UsersService();
        $empresa = $sUser->getEmpresa(Auth::id());
        $empresas = Empresa::all();

        return view('relatorios.todos', ['empresa' => $empresa->empresa_id, 'empresas' => $empresas]);
    }

    public function relatorio(Request $request){
        $dataI = date('Y-m-d h:m:s', strtotime($request->inicio));
        $dataF = date('Y-m-t h:m:s', strtotime($request->fim));

        if($request->empresa != '%'){
            $empresa = Empresa::findOrFail($request->empresa);
        } else {
            $empresa = '';
        }



        if ($request->tipoR == 'nfe'){
            if($request->status == '%'){
                $pedidos = DB::table('pedidos')
                ->select('*')
                ->whereRaw("(estado like 'Aprovado' or estado like 'Cancelado')")
                ->where("updated_at", '>=', $dataI)
                ->where("updated_at", "<=", $dataF)
                ->where("empresa_id", "like", $request->empresa)
                ->get();
           } else {
                $pedidos = DB::table('pedidos')
                ->select('*')
                ->where('estado', 'like', $request->status)
                ->where("updated_at", '>=', $dataI)
                ->where("updated_at", "<=", $dataF)
                ->where("empresa_id", "like", $request->empresa)
                ->get();
           }
           $pdf = Pdf::loadView('relatorios.nfe', ['pedidos' => $pedidos, 'empresa' => $empresa]);
           return $pdf->stream(date('d-m-Y').' Relatorio de NFe.pdf');
        }
    }
}
