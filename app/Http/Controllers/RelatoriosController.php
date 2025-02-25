<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Pedido;
use App\Services\UsersService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RelatoriosController extends Controller
{
    public function show()
    {
        try{
            $empresas = Empresa::all();
            $empresa = Auth::user()->empresa_id;

            if ($empresa == 1) {
                $empresa = "%";
            }

            $pedidos = DB::table('pedidos')
            ->select('pedidos.*','clientes.nome as cliente') 
            ->join('clientes', 'clientes.id', 'pedidos.cliente_id')
            ->where("pedidos.empresa_id", "like", $empresa)
            ->get();

            return view('relatorios.todos', ['pedidos' => $pedidos, 'empresa' => $empresa, 'empresas' => $empresas]);


        } catch (Exception $e) {
            dd($e);
            return back();

        }
    }

    public function relatorio(Request $request)
    {

        try {
            $dataI = date('Y-m-d h:m:s', strtotime($request->inicio));
            $dataF = date('Y-m-t h:m:s', strtotime($request->fim));
            if ($request->empresa != '%') {
                $empresa = Empresa::findOrFail($request->empresa);
            } else {
                $empresa = '';
            }
            if ($request->tipoR == 'nfe') {
                if ($request->status == '%') {
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
                return $pdf->stream(date('d-m-Y') . ' Relatorio de NFe.pdf');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function indexMDFe()
    {
        try {
            return view('relatorios.index-mdfe');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }
    


    public function gerarPdf(Request $request) { 
        // dd($request -> inicio);
        $vendas = DB::table('pedidos')
        ->where('data', '<=', $request -> fim)
        ->where('data','>=', $request -> inicio)
        ->get();
        $dompdf = new Dompdf();
    
        $html = view('relatorios.vendasSinteticas', [
            'vendas' => $request -> vendas,
            'dataInicio' => $request -> inicio,
            'dataFim' => $request -> fim,
            'estado' => $request -> estado
        ])->render();
    
        $dompdf->loadHtml($html);
    
        $dompdf->setPaper('A4');
    
        $dompdf->render();
    
        return $dompdf->stream('vendas_' . date('Y-m-d') . '.pdf');
    }
    
    

}


