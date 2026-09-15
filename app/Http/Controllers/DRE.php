<?php

namespace App\Http\Controllers;

use App\Models\FluxoDeCaixa;
use App\Models\PlanoDeConta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DRE extends Controller
{
    public function index(Request $request)
    {
        $dataInicial = $request->input('data_inicial', now()->startOfMonth()->toDateString());
        $dataFinal = $request->input('data_final', now()->endOfMonth()->toDateString());


        $receitas = FluxoDeCaixa::whereBetween('data', [$dataInicial, $dataFinal])
            ->where('tipo', 'Entrada')
            ->where('empresa_id', Auth::user()->empresa_id)
            ->sum('valor');

        $despesas = FluxoDeCaixa::whereBetween('data', [$dataInicial, $dataFinal])
            ->where('tipo', 'Saida')
            ->where('empresa_id', Auth::user()->empresa_id)
            ->sum('valor');

        $receita_plano = DB::table('fluxo_de_caixas')
            ->join('plano_de_contas', 'fluxo_de_caixas.plano_de_contas_id', '=', 'plano_de_contas.id')
            ->selectRaw('plano_de_contas.descricao, fluxo_de_caixas.tipo, SUM(fluxo_de_caixas.valor) as total')
            ->whereBetween('fluxo_de_caixas.data', [$dataInicial, $dataFinal])
            ->where('fluxo_de_caixas.empresa_id', Auth::user()->empresa_id)
            ->groupBy('plano_de_contas.descricao', 'fluxo_de_caixas.tipo')
            ->get();

       

        $lucro = $receitas - $despesas;


        return view('fluxodecaixa.dre', compact('receitas', 'despesas', 'lucro', 'receita_plano'));
    }

    public function gerarPdf(Request $request)
    {
        $dataInicial = $request->input('data_inicial', now()->startOfMonth()->toDateString());
        $dataFinal = $request->input('data_final', now()->endOfMonth()->toDateString());

        $receitas = FluxoDeCaixa::whereBetween('data', [$dataInicial, $dataFinal])
            ->where('tipo', 'Entrada')
            ->where('empresa_id', Auth::user()->empresa_id)
            ->sum('valor');

        $despesas = FluxoDeCaixa::whereBetween('data', [$dataInicial, $dataFinal])
            ->where('tipo', 'Saída')
            ->where('empresa_id', Auth::user()->empresa_id)
            ->sum('valor');

        $lucro = $receitas - $despesas;

        $pdf = Pdf::loadView('fluxodecaixa.drepdf', compact('receitas', 'despesas', 'lucro', 'dataInicial', 'dataFinal'));
        return $pdf->download('fluxodecaixa.pdf');
    }
}
