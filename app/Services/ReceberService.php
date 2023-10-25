<?php

namespace App\Services;

use App\Models\ContasARecebers;
use Exception;
use Illuminate\Support\Facades\DB;

class ReceberService{
    public function __construct(){}

    public function update($id, $pgto, $venc){
        $conta = ContasARecebers::findOrFail($id);

        $conta->update([
            'valor_atual' => $conta->valor_atual - $pgto,
            'valor_pago' => $conta->valor_pago + $pgto,
            'vencimento' => $venc
        ]);

        return 1;

    }

    public function um($id){
        return ContasARecebers::findOrFail($id);
    }

    public function destroy($id){
        $conta = ContasARecebers::findOrFail($id);
        $conta->delete();

        return 1;
    }

    public function new($empresa, $cliente, $total, $status, $vencimento){
        return contasARecebers::create([
            'cliente_id' => $cliente,
            'empresa_id' => $empresa,
            'pedido_id' => null,
            'total' => $total,
            'status' => $status,
            'valor_original' => $total,
            'valor_pago' => 0,
            'valor_atual' => $total,
            'vencimento' => $vencimento
            // 'vencimento' => date('Y-m-d', strtotime("+30 days",strtotime(today())))
        ]);
    }

    public function todas($empresa){
        return DB::table('contas_a_recebers')
        ->select('contas_a_recebers.id', 'contas_a_recebers.total', 'contas_a_recebers.status', 'clientes.nome', 'empresas.fantasia', 'contas_a_recebers.pedido_id', 'contas_a_recebers.valor_atual', 'contas_a_recebers.valor_pago', 'contas_a_recebers.valor_original', 'contas_a_recebers.vencimento')
        ->join('clientes', 'clientes.id', '=', 'contas_a_recebers.cliente_id')
        ->join('empresas', 'empresas.id', '=', 'contas_a_recebers.empresa_id')
        ->where('contas_a_recebers.empresa_id', '=', $empresa)
        ->get();
    }
}