<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class PedidosService{
    public function __construct(){}

    public function formatedVenda($idEmpresa){
        if($idEmpresa == 1){
            $idEmpresa = '%';
        }
        return DB::table('pedidos')
        ->select('pedidos.id', 'clientes.nome', 'pedidos.sequencia_evento', 'pedidos.total', 'pedidos.chave', 'pedidos.status', 'pedidos.estado', 'empresas.fantasia', 'pedidos.numero_nfe')
        ->join('empresas', 'empresas.id', '=', 'pedidos.empresa_id')
        ->join('clientes', 'clientes.id', '=', 'pedidos.cliente_id')
        ->where('pedidos.empresa_id', 'like', $idEmpresa)
        ->orderByDesc('pedidos.created_at')
        ->get();
    }

    public function buscarPedido($id)
    {
        return Pedido::find($id);
    }

    public function cfopAll()
    {
        return DB::table('cfop')->get();
    }

    public function findCfop($id)
    {
        return DB::table('cfop')->where('id', $id)->first();
    }

}