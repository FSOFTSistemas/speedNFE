<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeDashboardService
{
    private FluxoDeCaixaService $fluxoDeCaixaService;

    public function __construct(FluxoDeCaixaService $fluxoDeCaixaService)
    {
        $this->fluxoDeCaixaService = $fluxoDeCaixaService;
    }

    public function resumoMes($empresaId)
    {
        $inicioMesAtual = Carbon::now()->startOfMonth();
        $fimMesAtual = Carbon::now()->endOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $fimMesAnterior = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $atual = $this->resumoPeriodo($empresaId, $inicioMesAtual, $fimMesAtual);
        $anterior = $this->resumoPeriodo($empresaId, $inicioMesAnterior, $fimMesAnterior);

        return [
            'valorTotal' => $atual['valorTotal'],
            'qtdVendas' => $atual['qtdVendas'],
            'ticketMedio' => $atual['ticketMedio'],
            'lucroTotal' => $atual['lucroTotal'],
            'variacaoValorTotal' => $this->variacaoPercentual($atual['valorTotal'], $anterior['valorTotal']),
            'variacaoQtdVendas' => $this->variacaoPercentual($atual['qtdVendas'], $anterior['qtdVendas']),
            'variacaoTicketMedio' => $this->variacaoPercentual($atual['ticketMedio'], $anterior['ticketMedio']),
            'variacaoLucroTotal' => $this->variacaoPercentual($atual['lucroTotal'], $anterior['lucroTotal']),
        ];
    }

    private function resumoPeriodo($empresaId, Carbon $inicio, Carbon $fim)
    {
        $nfe = DB::table('item_pedidos')
            ->join('produtos', 'produtos.id', 'item_pedidos.produto_id')
            ->join('pedidos', 'pedidos.id', 'item_pedidos.pedido_id')
            ->where('pedidos.empresa_id', $empresaId)
            ->whereBetween('pedidos.data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('pedidos.chave', '!=', '')
            ->where('pedidos.estado', '!=', 'Cancelado')
            ->selectRaw('SUM((item_pedidos.unitario * item_pedidos.qtde) - item_pedidos.desconto) as receita,
                SUM(produtos.precocusto * item_pedidos.qtde) as custo')
            ->first();

        $nfce = DB::table('item_cupoms')
            ->join('produtos', 'produtos.id', 'item_cupoms.produto_id')
            ->join('cupoms', 'cupoms.id', 'item_cupoms.cupom_id')
            ->where('cupoms.empresa_id', $empresaId)
            ->whereBetween('cupoms.data', [$inicio, $fim])
            ->where('cupoms.situacao', 'ATIVO')
            ->selectRaw('SUM((item_cupoms.unitario * item_cupoms.qtde) - item_cupoms.desconto) as receita,
                SUM(produtos.precocusto * item_cupoms.qtde) as custo')
            ->first();

        $qtdVendasNfe = Pedido::where('empresa_id', $empresaId)
            ->whereBetween('data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('chave', '!=', '')
            ->where('estado', '!=', 'Cancelado')
            ->count();

        $qtdVendasNfce = DB::table('cupoms')
            ->where('empresa_id', $empresaId)
            ->whereBetween('data', [$inicio, $fim])
            ->where('situacao', 'ATIVO')
            ->count();

        $receita = (float) ($nfe->receita ?? 0) + (float) ($nfce->receita ?? 0);
        $custo = (float) ($nfe->custo ?? 0) + (float) ($nfce->custo ?? 0);
        $qtdVendas = $qtdVendasNfe + $qtdVendasNfce;

        return [
            'valorTotal' => $receita,
            'qtdVendas' => $qtdVendas,
            'ticketMedio' => $qtdVendas > 0 ? $receita / $qtdVendas : 0,
            'lucroTotal' => $receita - $custo,
        ];
    }

    private function variacaoPercentual($atual, $anterior)
    {
        if ($anterior == 0) {
            return $atual > 0 ? 100.0 : 0.0;
        }

        return round((($atual - $anterior) / $anterior) * 100, 1);
    }

    public function vendasUltimos30Dias($empresaId)
    {
        $inicio = Carbon::now()->subDays(29)->startOfDay();
        $fim = Carbon::now()->endOfDay();

        $nfe = Pedido::where('empresa_id', $empresaId)
            ->whereBetween('data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('chave', '!=', '')
            ->where('estado', '!=', 'Cancelado')
            ->selectRaw("DATE_FORMAT(data, '%Y-%m-%d') as dia, SUM(total) as valor")
            ->groupBy('dia')
            ->pluck('valor', 'dia');

        $nfce = DB::table('cupoms')
            ->where('empresa_id', $empresaId)
            ->whereBetween('data', [$inicio, $fim])
            ->where('situacao', 'ATIVO')
            ->selectRaw("DATE_FORMAT(data, '%Y-%m-%d') as dia, SUM(subtotal) as valor")
            ->groupBy('dia')
            ->pluck('valor', 'dia');

        $serie = [];
        for ($data = $inicio->copy(); $data->lte($fim); $data->addDay()) {
            $chave = $data->format('Y-m-d');
            $serie[] = [
                'dia' => $data->format('d/m'),
                'valor' => (float) ($nfe[$chave] ?? 0) + (float) ($nfce[$chave] ?? 0),
            ];
        }

        return $serie;
    }

    public function formaPagamento($empresaId)
    {
        $inicio = Carbon::now()->startOfMonth();
        $fim = Carbon::now()->endOfMonth();

        $nfe = DB::table('fatura_pedidos')
            ->join('pedidos', 'pedidos.id', 'fatura_pedidos.venda_id')
            ->join('forma_pags', 'forma_pags.id', 'fatura_pedidos.forma_pag_id')
            ->where('pedidos.empresa_id', $empresaId)
            ->whereBetween('pedidos.data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('pedidos.chave', '!=', '')
            ->where('pedidos.estado', '!=', 'Cancelado')
            ->selectRaw('forma_pags.descricao as forma, SUM(fatura_pedidos.valor) as total')
            ->groupBy('forma_pags.descricao')
            ->pluck('total', 'forma');

        $nfce = DB::table('cupom_formas')
            ->join('cupoms', 'cupoms.id', 'cupom_formas.cupom_id')
            ->where('cupoms.empresa_id', $empresaId)
            ->whereBetween('cupoms.data', [$inicio, $fim])
            ->where('cupoms.situacao', 'ATIVO')
            ->selectRaw('cupom_formas.forma as forma, SUM(cupom_formas.valor) as total')
            ->groupBy('cupom_formas.forma')
            ->pluck('total', 'forma');

        $combinado = [];
        foreach ($nfe as $forma => $total) {
            $combinado[$forma] = ($combinado[$forma] ?? 0) + (float) $total;
        }
        foreach ($nfce as $forma => $total) {
            $combinado[$forma] = ($combinado[$forma] ?? 0) + (float) $total;
        }

        arsort($combinado);

        return collect($combinado)->map(fn ($total, $forma) => [
            'forma' => $forma,
            'total' => $total,
        ])->values();
    }

    public function produtosMaisVendidos($empresaId)
    {
        $inicio = Carbon::now()->startOfMonth();
        $fim = Carbon::now()->endOfMonth();

        $nfe = DB::table('item_pedidos')
            ->join('produtos', 'produtos.id', 'item_pedidos.produto_id')
            ->join('pedidos', 'pedidos.id', 'item_pedidos.pedido_id')
            ->where('pedidos.empresa_id', $empresaId)
            ->whereBetween('pedidos.data', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
            ->where('pedidos.chave', '!=', '')
            ->where('pedidos.estado', '!=', 'Cancelado')
            ->selectRaw('produtos.id, produtos.produto as nome, SUM(item_pedidos.qtde) as quantidade')
            ->groupBy('produtos.id', 'produtos.produto')
            ->get();

        $nfce = DB::table('item_cupoms')
            ->join('produtos', 'produtos.id', 'item_cupoms.produto_id')
            ->join('cupoms', 'cupoms.id', 'item_cupoms.cupom_id')
            ->where('cupoms.empresa_id', $empresaId)
            ->whereBetween('cupoms.data', [$inicio, $fim])
            ->where('cupoms.situacao', 'ATIVO')
            ->selectRaw('produtos.id, produtos.produto as nome, SUM(item_cupoms.qtde) as quantidade')
            ->groupBy('produtos.id', 'produtos.produto')
            ->get();

        $combinado = [];
        foreach ($nfe->concat($nfce) as $linha) {
            if (!isset($combinado[$linha->id])) {
                $combinado[$linha->id] = ['nome' => $linha->nome, 'quantidade' => 0];
            }
            $combinado[$linha->id]['quantidade'] += (float) $linha->quantidade;
        }

        return collect($combinado)
            ->sortByDesc('quantidade')
            ->take(8)
            ->values();
    }

    public function alertas($empresaId)
    {
        return [
            'produtosSemEstoque' => Estoque::where('empresa_id', $empresaId)->where('estoque_atual', '<=', 0)->count(),
            'notasComPendencia' => Pedido::where('empresa_id', $empresaId)->where('estado', 'Rejeitado')->count(),
        ];
    }

    public function fluxoCaixaMes($empresaId)
    {
        $lancamentos = $this->fluxoDeCaixaService->listar($empresaId, [
            'data_inicio' => Carbon::now()->startOfMonth(),
            'data_fim' => Carbon::now()->endOfDay(),
        ]);

        $entradas = (float) $lancamentos->where('tipo', 'Entrada')->sum('valor');
        $saidas = (float) $lancamentos->where('tipo', 'Saída')->sum('valor');

        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $entradas - $saidas,
        ];
    }
}
