<?php

namespace App\Services;

use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelatoriosService
{
    public function dashboard($empresaId, $dataInicio, $dataFim)
    {
        if ($empresaId == 1) {
            $empresaId = '%';
        }

        $inicio = Carbon::parse($dataInicio)->startOfDay();
        $fim = Carbon::parse($dataFim)->endOfDay();
        $formato = $inicio->diffInDays($fim) > 60 ? '%Y-%m' : '%Y-%m-%d';

        $pedidosQuery = Pedido::where('empresa_id', 'like', $empresaId)
            ->whereBetween('data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('chave', '!=', '')
            ->where('estado', '!=', 'Cancelado');

        $pedidoIds = (clone $pedidosQuery)->pluck('id');
        $lucroVsCusto = $this->lucroVsCusto($empresaId, $inicio, $fim, $formato);

        return [
            'kpis' => $this->kpis($pedidosQuery, $lucroVsCusto),
            'vendasPorPeriodo' => $this->vendasPorPeriodo($empresaId, $inicio, $fim, $formato),
            'formaPagamento' => $this->formaPagamento($pedidoIds),
            'produtosMaisVendidos' => $this->produtosMaisVendidos($pedidoIds),
            'lucroVsCusto' => $lucroVsCusto,
        ];
    }

    private function kpis($pedidosQuery, $lucroVsCusto)
    {
        $pedidos = (clone $pedidosQuery)->get(['id', 'total']);
        $qtdVendas = $pedidos->count();
        $valorTotal = (float) $pedidos->sum('total');
        $ticketMedio = $qtdVendas > 0 ? $valorTotal / $qtdVendas : 0;
        $lucroTotal = collect($lucroVsCusto)->sum('lucro');

        return [
            'valorTotal' => $valorTotal,
            'qtdVendas' => $qtdVendas,
            'ticketMedio' => $ticketMedio,
            'lucroTotal' => $lucroTotal,
        ];
    }

    private function vendasPorPeriodo($empresaId, Carbon $inicio, Carbon $fim, $formato)
    {
        return Pedido::where('empresa_id', 'like', $empresaId)
            ->whereBetween('data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('chave', '!=', '')
            ->where('estado', '!=', 'Cancelado')
            ->selectRaw("DATE_FORMAT(data, '{$formato}') as periodo, SUM(total) as valor")
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->map(fn ($linha) => [
                'periodo' => $linha->periodo,
                'valor' => (float) $linha->valor,
            ]);
    }

    private function formaPagamento($pedidoIds)
    {
        if ($pedidoIds->isEmpty()) {
            return [];
        }

        return DB::table('fatura_pedidos')
            ->join('forma_pags', 'forma_pags.id', 'fatura_pedidos.forma_pag_id')
            ->whereIn('fatura_pedidos.venda_id', $pedidoIds)
            ->selectRaw('forma_pags.descricao as forma, SUM(fatura_pedidos.valor) as total')
            ->groupBy('forma_pags.descricao')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($linha) => [
                'forma' => $linha->forma,
                'total' => (float) $linha->total,
            ]);
    }

    private function produtosMaisVendidos($pedidoIds)
    {
        if ($pedidoIds->isEmpty()) {
            return [];
        }

        return DB::table('item_pedidos')
            ->join('produtos', 'produtos.id', 'item_pedidos.produto_id')
            ->whereIn('item_pedidos.pedido_id', $pedidoIds)
            ->selectRaw('produtos.produto as nome, SUM(item_pedidos.qtde) as quantidade')
            ->groupBy('produtos.id', 'produtos.produto')
            ->orderByDesc('quantidade')
            ->limit(8)
            ->get()
            ->map(fn ($linha) => [
                'nome' => $linha->nome,
                'quantidade' => (float) $linha->quantidade,
            ]);
    }

    private function lucroVsCusto($empresaId, Carbon $inicio, Carbon $fim, $formato)
    {
        return DB::table('item_pedidos')
            ->join('produtos', 'produtos.id', 'item_pedidos.produto_id')
            ->join('pedidos', 'pedidos.id', 'item_pedidos.pedido_id')
            ->where('pedidos.empresa_id', 'like', $empresaId)
            ->whereBetween('pedidos.data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('pedidos.chave', '!=', '')
            ->where('pedidos.estado', '!=', 'Cancelado')
            ->selectRaw("DATE_FORMAT(pedidos.data, '{$formato}') as periodo,
                SUM((item_pedidos.unitario * item_pedidos.qtde) - item_pedidos.desconto) as receita,
                SUM(produtos.precocusto * item_pedidos.qtde) as custo")
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->map(fn ($linha) => [
                'periodo' => $linha->periodo,
                'receita' => (float) $linha->receita,
                'custo' => (float) $linha->custo,
                'lucro' => (float) $linha->receita - (float) $linha->custo,
            ]);
    }
}
