<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CategoriasController extends ApiController
{
    /** Categorias ativas da empresa do usuário (as mesmas do select do cadastro de produto web). */
    public function index(): JsonResponse
    {
        $categorias = Categoria::query()
            ->where('empresa_id', Auth::guard('api')->user()->empresa_id)
            ->where('status', 1)
            ->orderBy('descricao')
            ->get(['id', 'descricao']);

        return $this->success($categorias);
    }
}
