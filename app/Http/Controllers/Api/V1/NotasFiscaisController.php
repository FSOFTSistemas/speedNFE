<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Pedido;
use App\Services\PedidosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotasFiscaisController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Pedido::query()->with('cliente')
            ->where('chave', '!=', '');

        $this->scopeEmpresa($query, $request);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data', [$request->data_inicio, $request->data_fim]);
        }

        return $this->success($query->orderByDesc('data')->paginate(min(max((int) $request->get('per_page', 15), 1), 100)));
    }

    public function show(int $id): JsonResponse
    {
        $query = Pedido::query()->with('cliente', 'itens.produto');
        $this->scopeEmpresa($query, request());

        return $this->success($query->findOrFail($id));
    }

    public function totalMes(PedidosService $pedidosService): JsonResponse
    {
        return $this->success($pedidosService->getTotalNFePerMonth(Auth::guard('api')->user()?->empresa_id));
    }

    private function scopeEmpresa($query, Request $request): void
    {
        $user = Auth::guard('api')->user();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }
    }
}
