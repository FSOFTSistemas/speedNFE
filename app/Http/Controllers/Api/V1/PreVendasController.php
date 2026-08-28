<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\ConverterPreVendaRequest;
use App\Http\Requests\Api\V1\StorePreVendaRequest;
use App\Http\Requests\Api\V1\UpdatePreVendaRequest;
use App\Http\Resources\PreVendaResource;
use App\Models\PreVenda;
use App\Services\PreVendaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class PreVendasController extends ApiController
{
    public function __construct(private PreVendaService $preVendaService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', PreVenda::class);

        return PreVendaResource::collection(
            $this->preVendaService->listarApi($request->all(), Auth::guard('api')->user())
        );
    }

    public function store(StorePreVendaRequest $request): JsonResponse
    {
        $this->authorize('create', PreVenda::class);

        $preVenda = $this->preVendaService->criarApi(
            $request->validated(),
            Auth::guard('api')->user()
        );

        return $this->success(new PreVendaResource($preVenda), 'Pré-venda criada com sucesso.', 201);
    }

    public function show(int $id): PreVendaResource
    {
        $preVenda = $this->preVendaService->buscarPermitidaApi($id, Auth::guard('api')->user());
        $this->authorize('view', $preVenda);

        return new PreVendaResource($preVenda);
    }

    public function update(UpdatePreVendaRequest $request, int $id): JsonResponse
    {
        $atual = $this->preVendaService->buscarPermitidaApi($id, Auth::guard('api')->user());
        $this->authorize('update', $atual);

        $preVenda = $this->preVendaService->atualizarApi(
            $id,
            $request->validated(),
            Auth::guard('api')->user()
        );

        return $this->success(new PreVendaResource($preVenda), 'Pré-venda atualizada com sucesso.');
    }

    public function cancelar(int $id): JsonResponse
    {
        $atual = $this->preVendaService->buscarPermitidaApi($id, Auth::guard('api')->user());
        $this->authorize('cancel', $atual);

        $preVenda = $this->preVendaService->cancelarApi($id, Auth::guard('api')->user());

        return $this->success(new PreVendaResource($preVenda), 'Pré-venda cancelada com sucesso.');
    }

    public function converter(ConverterPreVendaRequest $request, int $id): JsonResponse
    {
        $atual = $this->preVendaService->buscarPermitidaApi($id, Auth::guard('api')->user());
        $this->authorize('convert', $atual);

        $preVenda = $this->preVendaService->converterApi(
            $id,
            $request->validated(),
            Auth::guard('api')->user()
        );

        return $this->success(new PreVendaResource($preVenda), 'Pré-venda convertida com sucesso.');
    }

    public function pdf(int $id)
    {
        $preVenda = $this->preVendaService->buscarPermitidaApi($id, Auth::guard('api')->user());
        $this->authorize('view', $preVenda);
        $pdf = Pdf::loadView('pre-vendas.pdf', ['preVenda' => $preVenda]);

        return $pdf->stream("pre-venda-{$preVenda->numero}.pdf");
    }
}
