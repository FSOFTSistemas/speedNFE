<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\FluxoDeCaixa;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FluxoDeCaixaController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = FluxoDeCaixa::query()->with('planoDeContas');
        $this->scopeEmpresa($query, $request);
        $this->scopePeriodo($query, $request);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return $this->success($query->orderBy('data')->paginate(min(max((int) $request->get('per_page', 15), 1), 100)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId($request);

        $fluxo = FluxoDeCaixa::create($data);

        return $this->success($fluxo, 'Lancamento criado com sucesso.', 201);
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->findAllowed($id)->load('planoDeContas'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $fluxo = $this->findAllowed($id);
        $fluxo->update($this->validated($request, true));

        return $this->success($fluxo->fresh('planoDeContas'), 'Lancamento atualizado com sucesso.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findAllowed($id)->delete();

        return $this->message('Lancamento removido com sucesso.');
    }

    public function resumo(Request $request): JsonResponse
    {
        $query = FluxoDeCaixa::query();
        $this->scopeEmpresa($query, $request);
        $this->scopePeriodo($query, $request);

        $receitas = (clone $query)->where('tipo', 'Receita')->sum('valor');
        $despesas = (clone $query)->where('tipo', 'Despesa')->sum('valor');

        return $this->success([
            'total_receitas' => $receitas,
            'total_despesas' => $despesas,
            'saldo_final' => $receitas - $despesas,
        ]);
    }

    private function validated(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'descricao' => [$required, 'string', 'max:255'],
            'valor' => [$required, 'numeric'],
            'data' => [$required, 'date'],
            'tipo' => [$required, 'string'],
            'plano_de_contas_id' => [$required, 'exists:plano_de_contas,id'],
        ]);
    }

    private function findAllowed(int $id): FluxoDeCaixa
    {
        $query = FluxoDeCaixa::query();
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

    private function scopePeriodo($query, Request $request): void
    {
        $inicio = $request->filled('data_inicio') ? Carbon::parse($request->data_inicio)->startOfDay() : Carbon::today()->startOfDay();
        $fim = $request->filled('data_fim') ? Carbon::parse($request->data_fim)->endOfDay() : Carbon::today()->endOfDay();

        $query->whereBetween('data', [$inicio, $fim]);
    }

    private function empresaId(Request $request): ?int
    {
        $user = Auth::guard('api')->user();

        return $user && (int) $user->empresa_id !== 1
            ? $user->empresa_id
            : ($request->integer('empresa_id') ?: $user?->empresa_id);
    }
}
