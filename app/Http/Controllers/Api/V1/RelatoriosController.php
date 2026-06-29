<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelatoriosController extends ApiController
{
    public function vendas(Request $request): JsonResponse
    {
        $request->validate([
            'inicio' => ['required', 'date'],
            'fim' => ['required', 'date', 'after_or_equal:inicio'],
        ]);

        $query = Pedido::query()
            ->with('cliente')
            ->whereBetween('data', [$request->inicio, $request->fim]);

        $user = Auth::guard('api')->user();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        $vendas = $query->get();

        return $this->success([
            'vendas' => $vendas,
            'total' => $vendas->sum('total'),
            'subtotal' => $vendas->sum('subtotal'),
            'desconto' => $vendas->sum('desconto'),
        ]);
    }

    public function nfe(Request $request): JsonResponse
    {
        $request->validate([
            'inicio' => ['required', 'date'],
            'fim' => ['required', 'date', 'after_or_equal:inicio'],
        ]);

        $query = Pedido::query()
            ->with('cliente')
            ->where('chave', '!=', '')
            ->whereBetween('data', [$request->inicio, $request->fim]);

        $user = Auth::guard('api')->user();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        return $this->success($query->orderByDesc('data')->get());
    }
}
