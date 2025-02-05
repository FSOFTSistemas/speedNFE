<?php

namespace App\Http\Controllers;

use App\Models\FluxoDeCaixa;
use App\Models\PlanoDeConta;
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
}
