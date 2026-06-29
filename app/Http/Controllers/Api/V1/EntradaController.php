<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Entrada;
use App\Models\Estoque;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntradaController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Entrada::query()->with('itens.produto');
        $this->scopeEmpresa($query, $request);

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('dataEntrada', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('chave')) {
            $query->where('chave', $request->chave);
        }

        return $this->success($query->orderByDesc('dataEntrada')->paginate(min(max((int) $request->get('per_page', 15), 1), 100)));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->findAllowed($id)->load('itens.produto'));
    }

    public function destroy(int $id): JsonResponse
    {
        $entrada = $this->findAllowed($id)->load('itensEntradas');

        DB::transaction(function () use ($entrada) {
            foreach ($entrada->itensEntradas as $item) {
                $estoque = Estoque::where('produto_id', $item->produto_id)
                    ->where('empresa_id', $entrada->empresa_id)
                    ->first();

                if ($estoque) {
                    $qtdeRemover = $item->qtde;
                    $estoqueAnterior = $estoque->estoque_atual ?? 0;
                    $estoque->estoque_atual = max(0, $estoqueAnterior - $qtdeRemover);
                    $estoque->saidas = ($estoque->saidas ?? 0) + min($estoqueAnterior, $qtdeRemover);
                    $estoque->save();
                }
            }

            $entrada->delete();
        });

        return $this->message('Entrada removida com sucesso.');
    }

    private function findAllowed(int $id): Entrada
    {
        $query = Entrada::query();
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
