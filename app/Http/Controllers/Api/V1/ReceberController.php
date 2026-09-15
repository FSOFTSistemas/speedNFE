<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\ContasARecebers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceberController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = ContasARecebers::query();
        $this->scopeEmpresa($query, $request);

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->success($query->orderBy('vencimento')->paginate(min(max((int) $request->get('per_page', 15), 1), 100)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cliente_id' => ['required_without:cliente', 'integer'],
            'cliente' => ['required_without:cliente_id', 'integer'],
            'total' => ['required', 'numeric'],
            'vencimento' => ['required', 'date'],
            'status' => ['nullable'],
        ]);

        $total = $data['total'];
        $conta = ContasARecebers::create([
            'cliente_id' => $data['cliente_id'] ?? $data['cliente'],
            'empresa_id' => $this->empresaId($request),
            'pedido_id' => null,
            'total' => $total,
            'status' => $data['status'] ?? 0,
            'valor_original' => $total,
            'valor_pago' => 0,
            'valor_atual' => $total,
            'vencimento' => $data['vencimento'],
        ]);

        return $this->success($conta, 'Conta a receber criada com sucesso.', 201);
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->findAllowed($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $conta = $this->findAllowed($id);
        $data = $request->validate([
            'valor_pago' => ['nullable', 'numeric'],
            'vencimento' => ['nullable', 'date'],
            'status' => ['nullable'],
        ]);

        if (array_key_exists('valor_pago', $data)) {
            $conta->valor_atual -= $data['valor_pago'];
            $conta->valor_pago += $data['valor_pago'];
        }

        if (array_key_exists('vencimento', $data)) {
            $conta->vencimento = $data['vencimento'];
        }

        if (array_key_exists('status', $data)) {
            $conta->status = $data['status'];
        }

        $conta->save();

        return $this->success($conta->fresh(), 'Conta a receber atualizada com sucesso.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findAllowed($id)->delete();

        return $this->message('Conta a receber removida com sucesso.');
    }

    private function findAllowed(int $id): ContasARecebers
    {
        $query = ContasARecebers::query();
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

    private function empresaId(Request $request): ?int
    {
        $user = Auth::guard('api')->user();

        return $user && (int) $user->empresa_id !== 1
            ? $user->empresa_id
            : ($request->integer('empresa_id') ?: $user?->empresa_id);
    }
}
