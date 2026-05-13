<?php

namespace App\Http\Controllers;

use App\Models\FluxoDeCaixa;
use App\Models\PlanoDeConta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FluxoDeCaixaController extends Controller
{
    public function index(Request $request)
    {
        $query = FluxoDeCaixa::where('empresa_id', Auth::user()->empresa_id);

        // Define as datas de início e fim com a data de hoje
        $dataInicio = \Carbon\Carbon::today()->startOfDay(); // Início do dia atual
        $dataFim = \Carbon\Carbon::today()->endOfDay(); // Fim do dia atual

        // Verifica se as datas de filtro estão presentes e aplica o filtro de data
        if ($request->has('data_inicio') && $request->has('data_fim')) {
            $dataInicio = \Carbon\Carbon::parse($request->data_inicio)->startOfDay();
            $dataFim = \Carbon\Carbon::parse($request->data_fim)->endOfDay();
        }

        // Aplica o filtro de data
        $query->whereBetween('data', [$dataInicio, $dataFim]);

        // Obtém os lançamentos filtrados ou todos, caso o filtro não seja aplicado
        $lancamentos = $query->orderBy('data', 'asc')->get();

        // Obtém os planos de contas
        $planosDeContas = PlanoDeConta::where('empresa_id', Auth::user()->empresa_id)->get();

        // Retorna a view com os dados e as datas
        return view('fluxodecaixa.index', compact('lancamentos', 'planosDeContas', 'dataInicio', 'dataFim'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data' => 'required|date',
            'plano_de_contas_id' => 'required|exists:plano_de_contas,id',
        ]);

        try {

            FluxoDeCaixa::create([
                'descricao' => $request->descricao,
                'valor' => $request->valor,
                'data' => $request->data,
                'tipo' => $request->tipo,
                'plano_de_contas_id' => $request->plano_de_contas_id,
                'empresa_id' => Auth::user()->empresa_id,
            ]);

            return redirect()->route('fluxo-caixa.index')->with('success', 'Lançamento cadastrado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cadastrar: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data' => 'required|date',
            'tipo' => 'required',
        ]);

        try {
            $fluxoDeCaixa = FluxoDeCaixa::find($id);
            $fluxoDeCaixa->update([
                'descricao' => $request->descricao,
                'valor' => $request->valor,
                'data' => $request->data,
                'tipo' => $request->tipo,
            ]);

            return redirect()->route('fluxo-caixa.index')->with('success', 'Lançamento atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $lancamento = FluxoDeCaixa::findOrFail($id);
            $lancamento->delete();

            return redirect()->route('fluxo-caixa.index')->with('success', 'Lançamento removido com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao remover: ' . $e->getMessage());
        }
    }

    public function telaRrelatorio()
    {
        return view('fluxodecaixa.telaRelatorio');
    }

    public function gerarRelatorio(Request $request)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'tipo_relatorio' => 'required|in:geral,receitas_despesas,categoria,empresa,resumo',
        ]);

        $dataInicio = \Carbon\Carbon::parse($request->data_inicio)->startOfDay();
        $dataFim = \Carbon\Carbon::parse($request->data_fim)->endOfDay();

        $query = FluxoDeCaixa::where('empresa_id', Auth::user()->empresa_id)
            ->whereBetween('data', [$dataInicio, $dataFim]);

        switch ($request->tipo_relatorio) {
            case 'geral':
                $dados = $query->get();
                break;
            case 'receitas_despesas':
                $dados = $query->selectRaw("tipo, SUM(valor) as total")->groupBy('tipo')->get();

                break;
            case 'categoria':
                $dados = $query->selectRaw("fluxo_de_caixas.plano_de_contas_id, plano_de_contas.descricao as plano_de_contas_nome, SUM(fluxo_de_caixas.valor) as total")
                    ->join('plano_de_contas', 'fluxo_de_caixas.plano_de_contas_id', '=', 'plano_de_contas.id')
                    ->where('plano_de_contas.empresa_id', Auth::user()->empresa_id)
                    ->groupBy('fluxo_de_caixas.plano_de_contas_id', 'plano_de_contas.descricao')
                    ->get();
                break;
            case 'empresa':
                $dados = $query->selectRaw("empresa_id, SUM(valor) as total")
                    ->groupBy('empresa_id')
                    ->with('empresa')
                    ->get();
                break;
            case 'resumo':
                $totalReceitas = (clone $query)->where('tipo', 'Receita')->sum('valor');
                $totalDespesas = (clone $query)->where('tipo', 'Despesa')->sum('valor');

                $dados = [
                    'total_receitas' => $totalReceitas,
                    'total_despesas' => $totalDespesas,
                    'saldo_final' => $totalReceitas - $totalDespesas,
                ];
                break;
            default:
                return redirect()->back()->withErrors(['erro' => 'Tipo de relatório inválido']);
        }

        $pdf = Pdf::loadView('fluxodecaixa.rel.1', compact('dados', 'request'));
        return $pdf->stream('relatorio.pdf');
    }
}
