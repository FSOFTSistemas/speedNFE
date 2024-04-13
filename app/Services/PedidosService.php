<?php

namespace App\Services;

use App\Enums\EstadoEnum;
use App\Models\Ncm;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class PedidosService
{

    public function create($user_id, $cliente_id, $subtotal, $desconto, $empresa, $cfop, $info_complementares)
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
            'info_complementares' => $info_complementares,
        ]);
    }

    public function update($id, $cliente, $subtotal, $desconto, $cfop, $info_complementares)
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
            'info_complementares' => $info_complementares,
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

    public function buscarPedidos($empresaId)
    {
        if ($empresaId == 1) {
            $empresaId = '%';
        }
        return Pedido::select('pedidos.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'pedidos.empresa_id')
            ->where('pedidos.empresa_id', 'like', $empresaId)
            ->where('pedidos.chave', '!=', '')
            ->whereRaw('MONTH(pedidos.created_at) = MONTH(CURRENT_DATE)')
            ->whereRaw('YEAR(pedidos.created_at) = YEAR(CURRENT_DATE)')
            ->get();
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
        return DB::table('cfops')->get();
    }

    public function findCfop($id)
    {
        return DB::table('cfops')->where('id', $id)->first();
    }



    public function totalMes($empresa)
    {
        try {
            $resultados = Pedido::selectRaw('MONTH(data) as mes, SUM(total) as total_vendas')
                ->where('empresa_id', $empresa)
                ->where('estado', 'Autorizado')
                ->groupBy('mes')
                ->get();
    
            return $resultados;
        } catch (Exception $e) {
            // Trate o erro aqui se necessário
            return [];
        }
    }
    


}


