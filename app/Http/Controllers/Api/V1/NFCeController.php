<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Traits\EnviaNFCe;
use App\Models\NFCe;
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FluxoDeCaixaService;
use App\Services\NFCeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NFePHP\DA\NFe\Danfce;

class NFCeController extends ApiController
{
    use EnviaNFCe;

    public function __construct(
        private CupomService $cupomService,
        private EmpresasService $empresaServices,
        private EstoquesService $estoqueService,
        private FluxoDeCaixaService $fluxoCaixaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = NFCe::query()->with('cupom');
        $this->scopeEmpresa($query, $request);

        if ($request->filled('situacao')) {
            $query->where('situacao', $request->situacao);
        }

        if ($request->filled('month')) {
            $query->where('data', 'like', $request->month.'%');
        }

        return $this->success($query->orderByDesc('data')->paginate(min(max((int) $request->get('per_page', 15), 1), 100)));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->findAllowed($id)->load('cupom'));
    }

    public function pdf(int $id)
    {
        $nfce = $this->findAllowed($id);
        $dancfe = new Danfce($nfce->xml);

        return response($dancfe->render())->header('Content-Type', 'application/pdf');
    }

    public function xml(int $id)
    {
        $nfce = $this->findAllowed($id);

        return response($nfce->xml)
            ->header('Content-Type', 'text/xml; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="'.$nfce->chave.'.xml"');
    }

    public function enviar(int $cupomId): JsonResponse
    {
        $resultado = $this->_enviarNFCePeloId(
            $cupomId,
            $this->cupomService,
            $this->empresaServices,
            $this->estoqueService,
            $this->fluxoCaixaService
        );

        return response()->json([
            'status' => $resultado->status,
            'message' => $resultado->message,
        ], $resultado->status === 'success' ? 200 : 422);
    }

    public function totalMes(): JsonResponse
    {
        return $this->success(NFCeService::getTotalNFCePerMonth(Auth::guard('api')->user()?->empresa_id));
    }

    private function findAllowed(int $id): NFCe
    {
        $query = NFCe::query();
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
