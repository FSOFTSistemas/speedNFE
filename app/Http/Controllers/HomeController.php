<?php

namespace App\Http\Controllers;

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
        $quantidadePedidosPorMes = Pedido::select(DB::raw('YEAR(data) as ano, MONTH(data) as mes'), DB::raw('COUNT(*) as total_pedidos'))
            ->groupBy(DB::raw('YEAR(data)'), DB::raw('MONTH(data)'))
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();
    
        return view('home', ['quantidadePedidosPorMes' => $quantidadePedidosPorMes]);
    }

    public function homePage(){

        return view('homePage');


        

    }

}
