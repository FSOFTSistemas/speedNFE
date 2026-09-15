<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json([
                'message' => 'Usuário não autenticado.',
            ], 401);
        }

        $empresaId = $this->resolveEmpresaId($request, $user);
        $inicioMes = now()->startOfMonth();
        $fimMes = now()->endOfMonth();

        $pedidosMesQuery = Pedido::query()
            ->where('empresa_id', $empresaId)
            ->whereBetween('data', [$inicioMes, $fimMes]);

        $totalPedidosMes = (clone $pedidosMesQuery)->count();
        $valorPedidosMes = (float) (clone $pedidosMesQuery)->sum($this->pedidoTotalColumn());

        $totalProdutos = Produto::query()
            ->where('empresa_id', $empresaId)
            ->count();

        $totalClientes = Cliente::query()
            ->where('empresa_id', $empresaId)
            ->count();

        $produtosEmEstoque = $this->contarProdutosEmEstoque($empresaId);

        return response()->json([
            'data' => [
                'empresa_id' => $empresaId,
                'periodo' => [
                    'mes' => $inicioMes->month,
                    'ano' => $inicioMes->year,
                    'inicio' => $inicioMes->toDateString(),
                    'fim' => $fimMes->toDateString(),
                ],
                'cards' => [
                    'pedidos_mes' => $totalPedidosMes,
                    'produtos' => $totalProdutos,
                    'clientes' => $totalClientes,
                    'valor_pedidos_mes' => $valorPedidosMes,
                    'produtos_em_estoque' => $produtosEmEstoque,
                ],
            ],
        ]);
    }
    private function resolveEmpresaId(Request $request, $user): int
    {
        if ((int) $user->empresa_id === 1 && $request->filled('empresa_id')) {
            return (int) $request->empresa_id;
        }

        return (int) $user->empresa_id;
    }

    private function pedidoTotalColumn(): string
    {
        foreach (['total', 'valor_total', 'valor', 'total_pedido'] as $column) {
            if (Schema::hasColumn('pedidos', $column)) {
                return $column;
            }
        }

        return 'total';
    }

    private function contarProdutosEmEstoque(int $empresaId): int
    {
        if (Schema::hasColumn('produtos', 'estoque')) {
            return Produto::query()
                ->where('empresa_id', $empresaId)
                ->where('estoque', '>', 0)
                ->count();
        }

        if (Schema::hasTable('estoques')) {
            $quantidadeColumn = null;

            foreach (['quantidade', 'estoque', 'saldo', 'qtd'] as $column) {
                if (Schema::hasColumn('estoques', $column)) {
                    $quantidadeColumn = $column;
                    break;
                }
            }

            if ($quantidadeColumn) {
                return DB::table('estoques')
                    ->where('empresa_id', $empresaId)
                    ->where($quantidadeColumn, '>', 0)
                    ->distinct('produto_id')
                    ->count('produto_id');
            }
        }

        return 0;
    }
}
