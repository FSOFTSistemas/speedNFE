<?php

namespace App\Http\Controllers;

use App\Enums\PreVendaStatusEnum;
use App\Http\Requests\Api\V1\ConverterPreVendaRequest;
use App\Http\Requests\Api\V1\StorePreVendaRequest;
use App\Http\Requests\Api\V1\UpdatePreVendaRequest;
use App\Models\cfop;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\FormaPag;
use App\Models\PreVenda;
use App\Models\Produto;
use App\Services\PreVendaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreVendasController extends Controller
{
    public function __construct(private PreVendaService $preVendaService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', PreVenda::class);

        $preVendas = $this->preVendaService->listarApi($request->all(), Auth::user());

        return view('pre-vendas.index', [
            'preVendas' => $preVendas,
            'statusOptions' => PreVendaStatusEnum::cases(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', PreVenda::class);

        return view('pre-vendas.create', $this->formData());
    }

    public function store(StorePreVendaRequest $request)
    {
        $this->authorize('create', PreVenda::class);

        $preVenda = $this->preVendaService->criarApi($request->validated(), Auth::user());

        return redirect()
            ->route('pre-vendas.show', $preVenda->id)
            ->with('success', 'Pré-venda criada com sucesso.');
    }

    public function show(int $id)
    {
        $preVenda = $this->preVendaService->buscarPermitidaApi($id, Auth::user());
        $this->authorize('view', $preVenda);

        return view('pre-vendas.show', array_merge($this->formData($preVenda->empresa_id), [
            'preVenda' => $preVenda,
        ]));
    }

    public function edit(int $id)
    {
        $preVenda = $this->preVendaService->buscarPermitidaApi($id, Auth::user());
        $this->authorize('update', $preVenda);

        if ($preVenda->statusEfetivo() !== PreVendaStatusEnum::ABERTA) {
            return redirect()
                ->route('pre-vendas.show', $preVenda->id)
                ->with('warning', 'Somente pré-vendas abertas podem ser editadas.');
        }

        return view('pre-vendas.edit', array_merge($this->formData($preVenda->empresa_id), [
            'preVenda' => $preVenda,
        ]));
    }

    public function update(UpdatePreVendaRequest $request, int $id)
    {
        $atual = $this->preVendaService->buscarPermitidaApi($id, Auth::user());
        $this->authorize('update', $atual);

        $preVenda = $this->preVendaService->atualizarApi($id, $request->validated(), Auth::user());

        return redirect()
            ->route('pre-vendas.show', $preVenda->id)
            ->with('success', 'Pré-venda atualizada com sucesso.');
    }

    public function cancelar(int $id)
    {
        $atual = $this->preVendaService->buscarPermitidaApi($id, Auth::user());
        $this->authorize('cancel', $atual);

        $preVenda = $this->preVendaService->cancelarApi($id, Auth::user());

        return redirect()
            ->route('pre-vendas.show', $preVenda->id)
            ->with('success', 'Pré-venda cancelada com sucesso.');
    }

    public function converter(ConverterPreVendaRequest $request, int $id)
    {
        $atual = $this->preVendaService->buscarPermitidaApi($id, Auth::user());
        $this->authorize('convert', $atual);

        $preVenda = $this->preVendaService->converterApi(
            $id,
            $request->validated(),
            Auth::user()
        );

        $destino = $preVenda->destino?->value === 'NFE' ? 'NF-e' : 'NFC-e';

        return redirect()
            ->route('pre-vendas.show', $preVenda->id)
            ->with('success', "Pré-venda convertida em {$destino} com sucesso.");
    }

    public function pdf(int $id)
    {
        $preVenda = $this->preVendaService->buscarPermitidaApi($id, Auth::user());
        $this->authorize('view', $preVenda);
        $pdf = Pdf::loadView('pre-vendas.pdf', ['preVenda' => $preVenda]);

        return $pdf->stream("pre-venda-{$preVenda->numero}.pdf");
    }

    private function formData(?int $empresaId = null): array
    {
        $user = Auth::user();
        $isMaster = (int) $user->empresa_id === 1;
        $empresaId ??= (int) $user->empresa_id;

        $clientes = Cliente::query()->orderBy('nome');
        $produtos = Produto::query()->orderBy('produto');
        $formasPagamento = FormaPag::query()->orderBy('descricao');

        if (! $isMaster) {
            $clientes->where('empresa_id', $empresaId);
            $produtos->where('empresa_id', $empresaId);
            $formasPagamento->where(function ($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId)->orWhere('empresa_id', 1);
            });
        }

        return [
            'isMaster' => $isMaster,
            'empresas' => $isMaster ? Empresa::orderBy('fantasia')->get() : collect(),
            'clientes' => $clientes->get(),
            'produtos' => $produtos->get(),
            'formasPagamento' => $formasPagamento->get(),
            'cfops' => cfop::orderBy('cfop')->get(),
        ];
    }
}
