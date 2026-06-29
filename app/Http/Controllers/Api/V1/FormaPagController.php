<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\FormaPag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormaPagController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = FormaPag::query();
        $user = Auth::guard('api')->user();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        return $this->success($query->orderBy('descricao')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'descricao' => ['required', 'string', 'max:255'],
            'empresa_id' => ['nullable', 'integer'],
        ]);

        $user = Auth::guard('api')->user();
        $data['empresa_id'] = $user && (int) $user->empresa_id !== 1
            ? $user->empresa_id
            : ($data['empresa_id'] ?? $user?->empresa_id);

        $forma = FormaPag::create($data);

        return $this->success($forma, 'Forma de pagamento criada com sucesso.', 201);
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->findAllowed($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $forma = $this->findAllowed($id);
        $forma->update($request->validate([
            'descricao' => ['required', 'string', 'max:255'],
        ]));

        return $this->success($forma->fresh(), 'Forma de pagamento atualizada com sucesso.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findAllowed($id)->delete();

        return $this->message('Forma de pagamento removida com sucesso.');
    }

    private function findAllowed(int $id): FormaPag
    {
        $query = FormaPag::query();
        $user = Auth::guard('api')->user();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        }

        return $query->findOrFail($id);
    }
}
