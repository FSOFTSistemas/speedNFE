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

    // 1. Busca os totais (como sua função já fazia)
    $receitas = FluxoDeCaixa::whereBetween('data', [$dataInicial, $dataFinal])
        ->where('tipo', 'Entrada')
        ->sum('valor');

    $despesas = FluxoDeCaixa::whereBetween('data', [$dataInicial, $dataFinal])
        ->where('tipo', 'Saída')
        ->sum('valor');

    $lucro = $receitas - $despesas;

    $receita_plano = DB::table('fluxo_de_caixas')
        ->join('plano_de_contas', 'fluxo_de_caixas.plano_de_contas_id', '=', 'plano_de_contas.id')
        ->select('plano_de_contas.descricao', 'fluxo_de_caixas.tipo', DB::raw('SUM(fluxo_de_caixas.valor) as total'))
        ->whereBetween('fluxo_de_caixas.data', [$dataInicial, $dataFinal])
        ->groupBy('plano_de_contas.id', 'plano_de_contas.descricao', 'fluxo_de_caixas.tipo')
        ->orderBy('fluxo_de_caixas.tipo')
        ->get();

    $pdf = Pdf::loadView('fluxodecaixa.drepdf', compact(
        'receitas', 
        'despesas', 
        'lucro', 
        'dataInicial', 
        'dataFinal', 
        'receita_plano' // A variável agora existe e contém os dados
    ));

    // 4. Usa stream() para abrir no navegador
    return $pdf->stream('dre.pdf');
}
}
