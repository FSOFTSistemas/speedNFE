<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreProdutoRequest;
use App\Http\Requests\Api\V1\UpdateProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Services\ProdutosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ProdutosController extends ApiController
{
    public function __construct(private ProdutosService $produtosService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $produtos = $this->produtosService->listarApi(
            $request->all(),
            Auth::guard('api')->user()
        );

        return ProdutoResource::collection($produtos);
    }

    public function store(StoreProdutoRequest $request): JsonResponse
    {
        $produto = $this->produtosService->criarApi(
            $request->validated(),
            Auth::guard('api')->user()
        );

        return $this->success(
            new ProdutoResource($produto),
            'Produto criado com sucesso.',
            201
        );
    }

    public function show(int $id): ProdutoResource
    {
        $produto = $this->produtosService->buscarPermitidoApi(
            $id,
            Auth::guard('api')->user()
        );

        return new ProdutoResource($produto);
    }

    public function update(UpdateProdutoRequest $request, int $id): JsonResponse
    {
        $produto = $this->produtosService->buscarPermitidoApi(
            $id,
            Auth::guard('api')->user()
        );

        $produto = $this->produtosService->atualizarApi(
            $produto,
            $request->validated(),
            Auth::guard('api')->user()
        );

        return $this->success(
            new ProdutoResource($produto),
            'Produto atualizado com sucesso.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $produto = $this->produtosService->buscarPermitidoApi(
            $id,
            Auth::guard('api')->user()
        );

        $this->produtosService->removerApi($produto);

        return $this->message('Produto removido com sucesso.');
    }
}
