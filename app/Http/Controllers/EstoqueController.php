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

    public function index()
    {
        try {
            $stocks = $this->estoqueService->getCompanyStocks(Auth::user()->empresa_id);
            return view("estoques.index", ["estoques" => $stocks]);
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

}
