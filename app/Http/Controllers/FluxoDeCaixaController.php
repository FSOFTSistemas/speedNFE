<?php

namespace App\Http\Controllers;

use App\Models\FluxoDeCaixa;
use App\Models\PlanoDeConta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FluxoDeCaixaController extends Controller
{
    public function index()
    {
        $lancamentos = FluxoDeCaixa::where('empresa_id', Auth::user()->empresa_id)->orderBy('data', 'asc')->get();
        $planosDeContas = PlanoDeConta::where('empresa_id', Auth::user()->empresa_id)->get();
        return view('fluxodecaixa.index', compact('lancamentos', 'planosDeContas'));
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

        $query = FluxoDeCaixa::whereBetween('data', [$request->data_inicio, $request->data_fim]);

        switch ($request->tipo_relatorio) {
            case 'geral':
                $dados = $query->get();
                break;
            case 'receitas_despesas':
                $dados = $query->selectRaw("tipo, SUM(valor) as total")->groupBy('tipo')->get();
                break;
            case 'categoria':
                $dados = $query->selectRaw("plano_de_contas_id, SUM(valor) as total")
                    ->join('plano_de_contas', 'fluxo_de_caixas.plano_de_contas_id', '=', 'plano_de_contas.id') // Fazendo o JOIN manualmente
                    ->groupBy('plano_de_contas_id', 'plano_de_contas.descricao') // Agrupando pelo nome também
                    ->addSelect('plano_de_contas.descricao as plano_de_contas_nome') // Selecionando o nome diretamente
                    ->get();
                break;
            case 'empresa':
                $dados = $query->selectRaw("empresa_id, SUM(valor) as total")
                    ->groupBy('empresa_id')
                    ->with('empresa')
                    ->get();
                break;
            case 'resumo':
                $dados = [
                    'total_receitas' => $query->where('tipo', 'Receita')->sum('valor'),
                    'total_despesas' => $query->where('tipo', 'Despesa')->sum('valor'),
                    'saldo_final' => $query->sum('valor')
                ];
                break;
            default:
                return redirect()->back()->withErrors(['erro' => 'Tipo de relatório inválido']);
        }

        $pdf = Pdf::loadView('fluxodecaixa.rel.1', compact('dados', 'request'));
        return $pdf->stream('relatorio.pdf');
    }
}
