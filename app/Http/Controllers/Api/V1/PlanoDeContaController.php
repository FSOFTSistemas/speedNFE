<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\PlanoDeConta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PlanoDeContaController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = PlanoDeConta::query()->with('contaPai');
        $this->scopeEmpresa($query, $request);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return $this->success($query->orderBy('codigo')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId($request);

        $conta = PlanoDeConta::create($data);

        return $this->success($conta, 'Plano de conta criado com sucesso.', 201);
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->findAllowed($id)->load('contaPai', 'subContas'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $conta = $this->findAllowed($id);
        $conta->update($this->validated($request, $conta->id));

        return $this->success($conta->fresh('contaPai'), 'Plano de conta atualizado com sucesso.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findAllowed($id)->delete();

        return $this->message('Plano de conta removido com sucesso.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'codigo' => ['required', 'string', 'max:255', Rule::unique('plano_de_contas', 'codigo')->ignore($id)],
            'descricao' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:Receita,Despesa,Ativo,Passivo'],
            'conta_pai_id' => ['nullable', 'exists:plano_de_contas,id'],
        ]);
    }

    private function findAllowed(int $id): PlanoDeConta
    {
        $query = PlanoDeConta::query();
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
