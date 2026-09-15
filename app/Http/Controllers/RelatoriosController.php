<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Pedido;
use App\Services\RelatoriosService;
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
        try {
            $empresas = Empresa::all();
            $empresa = Auth::user()->empresa_id;

            if ($empresa == 1) {
                $empresa = "%";
            }

            $pedidos = DB::table('pedidos')
                ->select('pedidos.*', 'clientes.nome as cliente')
                ->join('clientes', 'clientes.id', 'pedidos.cliente_id')
                ->where("pedidos.empresa_id", "like", $empresa)
                ->get();

            return view('relatorios.todos', ['pedidos' => $pedidos, 'empresa' => $empresa, 'empresas' => $empresas]);

        } catch (Exception $e) {
            return back()->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }

    public function relatorio(Request $request)
    {

        try {
            $dataI = $request->inicio;
            $dataF = $request->fim;
            
            $userEmpresaId = Auth::user()->empresa_id;

            // Define a empresa para o filtro
            if ($userEmpresaId == 1) {
                $empresaFiltro = $request->empresa ?? "%";
                $empresa = Empresa::find($request->empresa);
            } else {
                $empresaFiltro = $userEmpresaId;
                $empresa = Empresa::find($userEmpresaId);
            }

            if ($request->tipo == "nfe") {
                $pedidos = Pedido::whereBetween("data", [$dataI, $dataF])
                    ->where("empresa_id", "like", $empresaFiltro)
                    ->get();

                $pdf = Pdf::loadView('relatorios.nfe', [
                    'pedidos' => $pedidos, 
                    'empresa' => $empresa
                ]);
                
                return $pdf->stream(date('d-m-Y') . ' Relatorio de NFe.pdf');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }

    public function gerarPdf(Request $request) 
    {
        try {
            $userEmpresaId = Auth::user()->empresa_id;
            
            // Se for admin, usa o que vem no request ou tudo (%), se não for, trava no ID dele
            $empresaFiltro = ($userEmpresaId == 1) ? ($request->empresa ?? "%") : $userEmpresaId;

            // 1. Busca os dados do banco filtrando por empresa
            $vendas = DB::table('pedidos')
                ->where('data', '>=', $request->inicio)
                ->where('data', '<=', $request->fim)
                ->where('empresa_id', 'like', $empresaFiltro)
                ->get();

            // 2. Mantendo os nomes exatos que sua View espera
            $pdf = Pdf::loadView('relatorios.vendasSinteticas', [
                'vendas'      => $vendas, 
                'data_inicio' => $request->inicio, 
                'data_fim'    => $request->fim
            ]);

            return $pdf->stream('Relatorio_Vendas_Sintetico.pdf');

        } catch (Exception $e) {
            return back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    public function dashboardData(Request $request, RelatoriosService $relatoriosService)
    {
        try {
            $userEmpresaId = Auth::user()->empresa_id;
            $empresaFiltro = ($userEmpresaId == 1) ? ($request->empresa ?? 1) : $userEmpresaId;

            $dataInicio = $request->filled('data_inicio')
                ? $request->input('data_inicio')
                : now()->subDays(30)->format('Y-m-d');
            $dataFim = $request->filled('data_fim')
                ? $request->input('data_fim')
                : now()->format('Y-m-d');

            $dados = $relatoriosService->dashboard($empresaFiltro, $dataInicio, $dataFim);

            return response()->json($dados);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function indexMDFe()
    {
        try {
            return view('relatorios.index-mdfe');
        } catch (Exception $e) {
            return back()->with('error', 'Erro inesperado: ' . $e->getMessage());
        }
    }
}