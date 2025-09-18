<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Estoque;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->tipo == 'admin'){
            $quantidadePedidosPorMes = Pedido::where(DB::raw('MONTH(data)'), date('m'))->where('empresa_id', Auth::user()->empresa_id)->count();
            $quantidadeProduto = Produto::where('empresa_id', Auth::user()->empresa_id)->count();
            $quantidadeCliente = Cliente::where('empresa_id', Auth::user()->empresa_id)->count();
            $quantidadeValorPedido = Pedido::where(DB::raw('MONTH(data)'), date('m'))->where('empresa_id', Auth::user()->empresa_id)->sum('total');
            $quantidadeProdutosEmEstoque = Estoque::where('estoque_atual', '>', 0)->where('empresa_id', Auth::user()->empresa_id)->count();
            return view('home', ['quantidadePedidosPorMes' => $quantidadePedidosPorMes, 'quantidadeProduto' => $quantidadeProduto, 'quantidadeCliente' => $quantidadeCliente, 'quantidadeValorPedido' => $quantidadeValorPedido, 'quantidadeProdutosEmEstoque' => $quantidadeProdutosEmEstoque]);
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
