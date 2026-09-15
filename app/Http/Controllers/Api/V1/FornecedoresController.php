<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Entrada;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FornecedoresController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Entrada::query()
            ->select('fornecedor')
            ->whereNotNull('fornecedor')
            ->distinct();

        $user = Auth::guard('api')->user();

        if ($user && (int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('search')) {
            $query->where('fornecedor', 'like', '%'.trim($request->search).'%');
        }

        return $this->success($query->orderBy('fornecedor')->get());
    }
}
