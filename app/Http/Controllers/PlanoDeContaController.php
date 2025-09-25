<?php

namespace App\Http\Controllers;

use App\Models\PlanoDeConta;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanoDeContaController extends Controller
{

    public function index()
    {

        $contas = PlanoDeConta::where('empresa_id', Auth::user()->empresa_id)->get();
        return view('fluxodecaixa.contas.index', compact('contas'));
    }

    /**
     * Armazena uma nova conta
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'codigo' => 'required',
                'descricao' => 'required',
                'tipo' => 'required|in:Receita,Despesa,Ativo,Passivo',
                'conta_pai_id' => 'nullable|exists:plano_de_contas,id',
            ]);

            $request['empresa_id'] = Auth::user()->empresa_id;
            PlanoDeConta::create($request->all());
            return redirect()->route('contas.index')->with('success', 'Plano de contas cadastrado com sucesso');
        } catch (QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Erro no banco de dados' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro no banco de dados' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {

        try {
            $planoConta = PlanoDeConta::findOrFail($id);



            $dadosValidados = $request->validate([
                'codigo' => "required|unique:plano_de_contas,codigo,{$id}",
                'descricao' => 'required',
                'tipo' => 'required|in:Receita,Despesa,Ativo,Passivo',
                'conta_pai_id' => 'nullable|exists:plano_contas,id',
            ]);

            $planoConta->update($dadosValidados);
            return redirect()->route('contas.index');
        } catch (QueryException $e) {
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove uma conta
     */
    public function destroy($id)
    {
        try {
            $planoConta = PlanoDeConta::find($id);
            $planoConta->delete();
            return redirect()->route('contas.index')->with('success', 'Deletado com sucesso !');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
