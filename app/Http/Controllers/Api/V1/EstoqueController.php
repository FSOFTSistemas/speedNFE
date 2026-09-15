<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Estoque;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstoqueController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Estoque::query()->with('produto');
        $this->scopeEmpresa($query, $request);

        if ($request->filled('produto_id')) {
            $query->where('produto_id', $request->produto_id);
        }

        return $this->success($query->paginate(min(max((int) $request->get('per_page', 15), 1), 100)));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->findAllowed($id)->load('produto'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $estoque = $this->findAllowed($id);
        $data = $request->validate([
            'estoque_atual' => ['required_without:estoque', 'numeric'],
            'estoque' => ['required_without:estoque_atual', 'numeric'],
            'estoque_anterior' => ['nullable', 'numeric'],
            'entradas' => ['nullable', 'numeric'],
            'saidas' => ['nullable', 'numeric'],
        ]);

        $estoque->update([
            'estoque_atual' => $data['estoque_atual'] ?? $data['estoque'],
            'estoque_anterior' => $data['estoque_anterior'] ?? $estoque->estoque_anterior,
            'entradas' => $data['entradas'] ?? $estoque->entradas,
            'saidas' => $data['saidas'] ?? $estoque->saidas,
        ]);

        return $this->success($estoque->fresh('produto'), 'Estoque atualizado com sucesso.');
    }

    private function findAllowed(int $id): Estoque
    {
        $query = Estoque::query();
        $this->scopeEmpresa($query, request());

        return $query->findOrFail($id);
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
