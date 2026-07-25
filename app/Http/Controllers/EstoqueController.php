<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EstoquesService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstoqueController extends Controller
{
    private $estoqueService;

    public function __construct(EstoquesService $estoqueService)
    {
        $this->estoqueService = $estoqueService;
    }

    public function index(Request $request)
    {
        try {
            $stocks = $this->estoqueService->getCompanyStocks(Auth::user()->empresa_id);

            if ($request->filled('chassi')) {
                $chassi = mb_strtolower($request->input('chassi'));
                $stocks = $stocks->filter(function ($estoque) use ($chassi) {
                    return str_contains(mb_strtolower(optional($estoque->produto)->chassiVeic ?? ''), $chassi);
                });
            }

            if ($request->filled('modelo')) {
                $modelo = mb_strtolower($request->input('modelo'));
                $stocks = $stocks->filter(function ($estoque) use ($modelo) {
                    return str_contains(mb_strtolower(optional($estoque->produto)->produto ?? ''), $modelo);
                });
            }

            return view("estoques.index", ["estoques" => $stocks]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function show($stockId)
    {
        try {
            $stock = $this->estoqueService->getStock($stockId);
            return view('estoques.show', ['estoque' => $stock]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function edit($stockId)
    {
        try {
            $stock = $this->estoqueService->getStock($stockId);
            return view('estoques.edit', ['estoque' => $stock]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $stockId)
    {
        try {
            $request->validate([
                'estoque' => 'required|numeric',
                'estoque_anterior' => 'required|numeric',
                'saidas' => 'required|numeric',
                'entradas' => 'required|numeric'
            ], [
                'required' => 'O campo :attribute é um campo obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!'
            ]);
            DB::beginTransaction();
            $this->estoqueService->update($request->estoque, $request->estoque_anterior, $request->entradas, $request->saidas, $stockId);
            DB::commit();
            return redirect()->route('estoque.edit', [$stockId])->with('success', 'Estoque foi atualizado com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

}
