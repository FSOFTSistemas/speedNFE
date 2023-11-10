<?php

namespace App\Services;

use App\Enum\EstadoEnum;
use App\Models\Ncm;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class PedidosService
{

    public function create($user_id, $cliente_id, $subtotal, $desconto, $empresa, $cfop)
    {
        return Pedido::create([
            'user_id' => $user_id,
            'cliente_id' => $cliente_id,
            'data' => today(),
            'status' => 2,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'total' => $subtotal,
            'empresa_id' => $empresa,
            'numero_nfe' => 0,
            'sequencia_evento' => 0,
            'chave' => '',
            'estado' => EstadoEnum::PENDENTE,
            'cfop' => $cfop,
        ]);
    }

    public function update($id, $cliente, $subtotal, $desconto, $cfop)
    {
        $pedido = Pedido::find($id);
        return $pedido->update([
            'cliente_id' => $cliente,
            'data' => today(),
            'status' => 0,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'total' => $subtotal,
            'cfop' => $cfop,
        ]);
    }

    public function formatedVenda($idEmpresa)
    {
        if ($idEmpresa == 1) {
            $idEmpresa = '%';
        }
        return DB::table('pedidos')
            ->select('pedidos.*', 'clientes.nome', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'pedidos.empresa_id')
            ->join('clientes', 'clientes.id', '=', 'pedidos.cliente_id')
            ->where('pedidos.empresa_id', 'like', $idEmpresa)
            ->orderByDesc('pedidos.created_at')
            ->get();
    }

    public function limiteDeNotas($empresa)
    {
        return DB::table('pedidos')
            ->where('empresa_id', '=', $empresa)
            ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
            ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
            ->count();
    }

    public function delete($id)
    {
        $pedido = Pedido::find($id);
        return $pedido->delete();
    }

    public function ncmAll()
    {
        return Ncm::all();
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
