<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Categoria;
use App\Services\CategoriasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    /** Mesmo cadastro da tela web (CategoriasService::store); reaproveita a categoria ativa de mesmo nome. */
    public function store(Request $request, CategoriasService $categoriasService): JsonResponse
    {
        $request->validate(
            ['descricao' => ['required', 'string', 'max:512']],
            [
                'descricao.required' => 'Informe o nome da categoria.',
                'descricao.max' => 'O nome da categoria deve ter no máximo 512 caracteres.',
            ]
        );

        $empresaId = Auth::guard('api')->user()->empresa_id;
        $descricao = trim($request->input('descricao'));

        $existente = Categoria::query()
            ->where('empresa_id', $empresaId)
            ->where('status', 1)
            ->whereRaw('LOWER(descricao) = ?', [mb_strtolower($descricao)])
            ->first(['id', 'descricao']);

        if ($existente) {
            return $this->success($existente, 'Categoria já cadastrada.');
        }

        $categoria = $categoriasService->store($descricao, $empresaId);

        return $this->success($categoria->only(['id', 'descricao']), 'Categoria cadastrada com sucesso.', 201);
    }
}
