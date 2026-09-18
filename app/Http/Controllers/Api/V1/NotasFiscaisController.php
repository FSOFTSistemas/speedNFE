<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\LimitExceededException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Traits\EnviaNFe;
use App\Http\Requests\Api\V1\StorePedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Models\Pedido;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FaturaService;
use App\Services\FluxoDeCaixaService;
use App\Services\ItemService;
use App\Services\PedidosService;
use App\Services\ProdutosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotasFiscaisController extends ApiController
{
    use EnviaNFe;

    public function __construct(
        private PedidosService $pedidoService,
        private EmpresasService $empresaServices,
        private EstoquesService $estoqueService,
        private FluxoDeCaixaService $fluxoCaixaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Pedido::query()->with('cliente')
            ->where('chave', '!=', '');

        $this->scopeEmpresa($query, $request);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data', [$request->data_inicio, $request->data_fim]);
        }

        return $this->success($query->orderByDesc('data')->paginate(min(max((int) $request->get('per_page', 15), 1), 100)));
    }

    public function store(StorePedidoRequest $request, ProdutosService $produtoService, ItemService $itemService, FaturaService $faturaService): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $empresaId = $this->resolveEmpresaId($request, $user);

        try {
            $pedido = $this->pedidoService->criarApi(
                $request->validated(),
                $user->id,
                $empresaId,
                $produtoService,
                $itemService,
                $faturaService,
                $this->empresaServices
            );
        } catch (LimitExceededException $e) {
            return $this->message($e->getMessage(), 422);
        }

        return $this->success(new PedidoResource($pedido), 'Pedido criado com sucesso.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $query = Pedido::query()->with('cliente', 'itens.produto');
        $this->scopeEmpresa($query, request());

        return $this->success($query->findOrFail($id));
    }

    public function enviar(int $id): JsonResponse
    {
        $pedido = $this->findAllowed($id);

        $resultado = $this->_enviarNFePeloId(
            $pedido->id,
            $this->pedidoService,
            $this->empresaServices,
            $this->estoqueService,
            $this->fluxoCaixaService
        );

        $message = trim(($resultado->title ? $resultado->title.': ' : '').$resultado->message);

        return response()->json([
            'status' => $resultado->status,
            'message' => $message,
        ], $resultado->status === 'success' ? 200 : 422);
    }

    public function totalMes(PedidosService $pedidosService): JsonResponse
    {
        return $this->success($pedidosService->getTotalNFePerMonth(Auth::guard('api')->user()?->empresa_id));
    }

    private function resolveEmpresaId(Request $request, $user): int
    {
        if ((int) $user->empresa_id === 1) {
            if ($request->filled('empresa_id')) {
                return (int) $request->empresa_id;
            }

            if ($request->filled('empresa')) {
                return (int) $request->empresa;
            }
        }

        return (int) $user->empresa_id;
    }

    private function findAllowed(int $id): Pedido
    {
        $query = Pedido::query();
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
