<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Estoque;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\HomeDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private HomeDashboardService $homeDashboardService;

    public function __construct(HomeDashboardService $homeDashboardService)
    {
        $this->homeDashboardService = $homeDashboardService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->tipo == 'admin' || Auth::user()->cargo == 'master'){
            $empresaId = Auth::user()->empresa_id;
            $quantidadePedidosPorMes = Pedido::where(DB::raw('MONTH(data)'), date('m'))->where('empresa_id', $empresaId)->count();
            $quantidadeProduto = Produto::where('empresa_id', $empresaId)->count();
            $quantidadeCliente = Cliente::where('empresa_id', $empresaId)->count();
            $quantidadeValorPedido = Pedido::where(DB::raw('MONTH(data)'), date('m'))->where('empresa_id', $empresaId)->sum('total');
            $quantidadeProdutosEmEstoque = Estoque::where('estoque_atual', '>', 0)->where('empresa_id', $empresaId)->count();

            return view('home', [
                'quantidadePedidosPorMes' => $quantidadePedidosPorMes,
                'quantidadeProduto' => $quantidadeProduto,
                'quantidadeCliente' => $quantidadeCliente,
                'quantidadeValorPedido' => $quantidadeValorPedido,
                'quantidadeProdutosEmEstoque' => $quantidadeProdutosEmEstoque,
                'resumoMes' => $this->homeDashboardService->resumoMes($empresaId),
                'vendasUltimos30Dias' => $this->homeDashboardService->vendasUltimos30Dias($empresaId),
                'produtosMaisVendidos' => $this->homeDashboardService->produtosMaisVendidos($empresaId),
                'alertas' => $this->homeDashboardService->alertas($empresaId),
                'fluxoCaixaMes' => $this->homeDashboardService->fluxoCaixaMes($empresaId),
            ]);
        } else {
            return view('home2');
        }
    }

    public function homePage(){

        return view('homePage');




    }

    public function homePage2(){

        return view('homePage2');

    }

}
