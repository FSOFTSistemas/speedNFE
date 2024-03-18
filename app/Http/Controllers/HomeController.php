<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
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
        $quantidadePedidosPorMes = Pedido::where(DB::raw('MONTH(data)'), date('m'))->count();
        $quantidadeProduto = Produto::count();
        $quantidadeCliente = Cliente::count();
        $quantidadeValorPedido = Pedido::where(DB::raw('MONTH(data)'), date('m'))->sum('total');
        return view('home', ['quantidadePedidosPorMes' => $quantidadePedidosPorMes, 'quantidadeProduto' => $quantidadeProduto, 'quantidadeCliente' => $quantidadeCliente, 'quantidadeValorPedido' => $quantidadeValorPedido]);
    }

    public function homePage(){

        return view('homePage');


        

    }

}
