<?php

namespace App\Services;

use App\Enums\PreVendaDestinoEnum;
use App\Enums\PreVendaStatusEnum;
use App\Models\Cliente;
use App\Models\CupomForma;
use App\Models\Empresa;
use App\Models\Estoque;
use App\Models\FormaPag;
use App\Models\PreVenda;
use App\Models\Produto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PreVendaService
{
    public function __construct(
        private PreVendaCalculator $calculator,
        private PedidosService $pedidosService,
        private ItemService $itemService,
        private FaturaService $faturaService,
        private CupomService $cupomService,
        private ItemCupomService $itemCupomService,
        private EstoquesService $estoquesService,
        private EmpresasService $empresasService
    ) {}

    public function listarApi(array $filtros, $user): LengthAwarePaginator
    {
        $query = PreVenda::query()->with(['cliente', 'empresa', 'itens', 'pagamentos']);
        $this->aplicarEscopoEmpresa($query, $filtros, $user);

        if (! empty($filtros['search'])) {
            $busca = trim($filtros['search']);
            $query->where(function (Builder $query) use ($busca) {
                $query->where('numero', 'like', "%{$busca}%")
                    ->orWhereHas('cliente', function (Builder $query) use ($busca) {
                        $query->where('nome', 'like', "%{$busca}%")
                            ->orWhere('cpf_cnpj', 'like', "%{$busca}%");
                    })
                    ->orWhereHas('itens', function (Builder $query) use ($busca) {
                        $query->where('descricao', 'like', "%{$busca}%")
                            ->orWhere('codigo', 'like', "%{$busca}%");
                    });
            });
        }

        if (! empty($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }

        if (! empty($filtros['status'])) {
            $status = strtoupper($filtros['status']);

            if ($status === PreVendaStatusEnum::EXPIRADA->value) {
                $query->where('status', PreVendaStatusEnum::ABERTA->value)
                    ->whereDate('validade_at', '<', today());
            } elseif ($status === PreVendaStatusEnum::ABERTA->value) {
                $query->where('status', $status)
                    ->where(function (Builder $query) {
                        $query->whereNull('validade_at')->orWhereDate('validade_at', '>=', today());
                    });
            } else {
                $query->where('status', $status);
            }
        }

        $dataInicio = $filtros['data_inicio'] ?? ($filtros['startDate'] ?? null);
        $dataFim = $filtros['data_fim'] ?? ($filtros['endDate'] ?? null);

        if ($dataInicio) {
            $query->whereDate('data', '>=', $dataInicio);
        }

        if ($dataFim) {
            $query->whereDate('data', '<=', $dataFim);
        }

        $perPage = max(1, min((int) ($filtros['per_page'] ?? 15), 100));

        return $query->orderByDesc('data')->orderByDesc('id')->paginate($perPage);
    }

    public function buscarPermitidaApi(int $id, $user, bool $bloquear = false): PreVenda
    {
        $query = PreVenda::query()->with(['cliente', 'empresa', 'itens.produto', 'pagamentos.formaPagamento']);
        $this->aplicarEscopoEmpresa($query, [], $user);

        if ($bloquear) {
            $query->lockForUpdate();
        }

        return $query->findOrFail($id);
    }

    public function criarApi(array $dados, $user): PreVenda
    {
        return DB::transaction(function () use ($dados, $user) {
            $empresaId = $this->resolverEmpresaId($dados, $user);
            $clienteId = $dados['cliente_id'] ?? null;
            $cliente = $this->buscarCliente($clienteId, $empresaId);

            $itens = $this->normalizarItens($dados['itens'], $empresaId);
            $calculo = $this->calculator->calcular(
                $itens,
                (float) ($dados['desconto'] ?? 0),
                (float) ($dados['acrescimo'] ?? 0)
            );
            $pagamentos = $this->normalizarPagamentos($dados['pagamentos'] ?? [], $empresaId);

            $preVenda = PreVenda::create([
                'empresa_id' => $empresaId,
                'cliente_id' => $clienteId,
                'cliente_nome' => $cliente?->nome,
                'cliente_documento' => $cliente?->cpf_cnpj,
                'user_id' => $user->id,
                'data' => $dados['data'] ?? today(),
                'validade_at' => $dados['validade_at'] ?? null,
                'status' => PreVendaStatusEnum::ABERTA,
                'subtotal' => $calculo['subtotal'],
                'desconto' => $calculo['desconto'],
                'acrescimo' => $calculo['acrescimo'],
                'total' => $calculo['total'],
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            $preVenda->update(['numero' => $preVenda->id]);
            $this->sincronizarItensEPagamentos($preVenda, $calculo['itens'], $pagamentos);

            return $preVenda->fresh(['cliente', 'empresa', 'itens.produto', 'pagamentos.formaPagamento']);
        });
    }

    public function atualizarApi(int $id, array $dados, $user): PreVenda
    {
        return DB::transaction(function () use ($id, $dados, $user) {
            $preVenda = $this->buscarPermitidaApi($id, $user, true);
            $this->validarAberta($preVenda);

            $clienteId = $dados['cliente_id'] ?? null;
            $cliente = $this->buscarCliente($clienteId, $preVenda->empresa_id);
            $itens = $this->normalizarItens($dados['itens'], $preVenda->empresa_id);
            $calculo = $this->calculator->calcular(
                $itens,
                (float) ($dados['desconto'] ?? 0),
                (float) ($dados['acrescimo'] ?? 0)
            );
            $pagamentos = $this->normalizarPagamentos($dados['pagamentos'] ?? [], $preVenda->empresa_id);

            $preVenda->update([
                'cliente_id' => $clienteId,
                'cliente_nome' => $cliente?->nome,
                'cliente_documento' => $cliente?->cpf_cnpj,
                'data' => $dados['data'] ?? $preVenda->data,
                'validade_at' => $dados['validade_at'] ?? null,
                'subtotal' => $calculo['subtotal'],
                'desconto' => $calculo['desconto'],
                'acrescimo' => $calculo['acrescimo'],
                'total' => $calculo['total'],
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            $this->sincronizarItensEPagamentos($preVenda, $calculo['itens'], $pagamentos);

            return $preVenda->fresh(['cliente', 'empresa', 'itens.produto', 'pagamentos.formaPagamento']);
        });
    }

    public function cancelarApi(int $id, $user): PreVenda
    {
        return DB::transaction(function () use ($id, $user) {
            $preVenda = $this->buscarPermitidaApi($id, $user, true);
            $this->validarAberta($preVenda);
            $preVenda->update(['status' => PreVendaStatusEnum::CANCELADA]);

            return $preVenda->fresh(['cliente', 'empresa', 'itens', 'pagamentos']);
        });
    }

    public function converterApi(int $id, array $dados, $user): PreVenda
    {
        return DB::transaction(function () use ($id, $dados, $user) {
            $preVenda = $this->buscarPermitidaApi($id, $user, true);
            $this->validarAberta($preVenda);
            $destino = PreVendaDestinoEnum::from($dados['destino']);

            if ($destino === PreVendaDestinoEnum::NFE) {
                $pedido = $this->converterParaNFe($preVenda, $dados, $user);
                $preVenda->pedido_id = $pedido->id;
            } else {
                $cupom = $this->converterParaNFCe($preVenda);
                $preVenda->cupom_id = $cupom->id;
            }

            $preVenda->status = PreVendaStatusEnum::CONVERTIDA;
            $preVenda->destino = $destino;
            $preVenda->convertida_por = $user->id;
            $preVenda->convertida_at = now();
            $preVenda->save();

            return $preVenda->fresh(['cliente', 'empresa', 'itens', 'pagamentos', 'pedido', 'cupom']);
        });
    }

    private function converterParaNFe(PreVenda $preVenda, array $dados, $user)
    {
        if (! $preVenda->cliente_id) {
            throw ValidationException::withMessages([
                'cliente_id' => 'Informe um cliente antes de converter a pré-venda em NF-e.',
            ]);
        }

        $empresa = Empresa::query()->lockForUpdate()->findOrFail($preVenda->empresa_id);

        if (
            $empresa->id !== 1
            && (int) $empresa->limNFes > 0
            && $this->pedidosService->limiteDeNotas($empresa->id) >= (int) $empresa->limNFes
        ) {
            throw ValidationException::withMessages([
                'destino' => 'O limite mensal de NF-e da empresa foi atingido.',
            ]);
        }

        $itensFiscais = $this->prepararItensFiscais($preVenda);
        $pedido = $this->pedidosService->create(
            $user->id,
            $preVenda->cliente_id,
            $itensFiscais['subtotal'],
            $itensFiscais['desconto'],
            $preVenda->empresa_id,
            $dados['cfop'],
            (int) ($dados['finalidade'] ?? 1),
            $dados['ref_nfe'] ?? null,
            (int) ($dados['tipo'] ?? 1),
            mb_substr($dados['info_complementares'] ?? $preVenda->observacoes ?? '', 0, 1500),
            $dados['aut_xml'] ?? null
        );

        foreach ($itensFiscais['itens'] as $item) {
            $produto = Produto::where('empresa_id', $preVenda->empresa_id)->find($item['produto_id']);

            if (! $produto) {
                throw ValidationException::withMessages([
                    'itens' => "O produto {$item['descricao']} não está mais disponível para conversão.",
                ]);
            }

            $this->itemService->create(
                $pedido->id,
                $produto,
                $item['quantidade'],
                $preVenda->empresa_id,
                $item['desconto'],
                $item['valor_unitario']
            );
        }

        $this->faturaService->create(
            (float) $preVenda->total,
            $pedido->id,
            $pedido->finNF,
            $preVenda->empresa_id
        );

        return $pedido;
    }

    private function converterParaNFCe(PreVenda $preVenda)
    {
        if ($preVenda->pagamentos->isEmpty()) {
            throw ValidationException::withMessages([
                'pagamentos' => 'Informe ao menos uma forma de pagamento para converter em NFC-e.',
            ]);
        }

        $totalPagamentos = round((float) $preVenda->pagamentos->sum('valor'), 2);

        if ($totalPagamentos < (float) $preVenda->total) {
            throw ValidationException::withMessages([
                'pagamentos' => 'A soma dos pagamentos é menor que o total da pré-venda.',
            ]);
        }

        $produtoIds = $preVenda->itens->pluck('produto_id')->filter()->values();
        $estoques = Estoque::query()
            ->where('empresa_id', $preVenda->empresa_id)
            ->whereIn('produto_id', $produtoIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('produto_id');

        foreach ($preVenda->itens as $item) {
            $estoque = $estoques->get($item->produto_id);

            if (! $estoque || (float) $estoque->estoque_atual < (float) $item->quantidade) {
                throw ValidationException::withMessages([
                    'itens' => "Estoque insuficiente para o produto {$item->descricao}.",
                ]);
            }
        }

        Empresa::query()->lockForUpdate()->findOrFail($preVenda->empresa_id);
        $numeroCupom = $this->empresasService->incrementCupomSequence($preVenda->empresa_id);
        $itensFiscais = $this->prepararItensFiscais($preVenda);
        $cupomId = $this->cupomService->createCupom(
            $numeroCupom,
            (float) $preVenda->total,
            $itensFiscais['desconto'],
            0,
            $itensFiscais['subtotal'],
            round($totalPagamentos - (float) $preVenda->total, 2),
            $preVenda->cliente_id,
            $preVenda->empresa_id
        );

        $this->itemCupomService->createItemsCupom(
            collect($itensFiscais['itens'])->map(fn ($item) => [
                'qtde' => $item['quantidade'],
                'unitario' => $item['valor_unitario'],
                'desconto' => $item['desconto'],
                'acrescimo' => 0,
                'total' => round($item['subtotal'] - $item['desconto'], 2),
                'subtotal' => $item['subtotal'],
                'prodId' => $item['produto_id'],
            ])->all(),
            $cupomId
        );

        foreach ($preVenda->pagamentos as $pagamento) {
            CupomForma::create([
                'forma' => $this->mapearFormaNFCe($pagamento->descricao),
                'valor' => $pagamento->valor,
                'cupom_id' => $cupomId,
            ]);
        }

        foreach ($preVenda->itens as $item) {
            $this->estoquesService->out($item->produto_id, $item->quantidade);
        }

        return $this->cupomService->getCupom($cupomId);
    }

    private function mapearFormaNFCe(string $descricao): string
    {
        $normalizada = mb_strtoupper(trim($descricao));
        $normalizadaSemAcento = strtr($normalizada, [
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A',
            'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ó' => 'O',
            'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U', 'Ç' => 'C',
        ]);

        if (str_contains($normalizadaSemAcento, 'DINHEIRO')) {
            return 'DINHEIRO';
        }

        if (str_contains($normalizadaSemAcento, 'CREDITO')) {
            return 'CARTÃO/CRÉDITO';
        }

        if (str_contains($normalizadaSemAcento, 'DEBITO')) {
            return 'CARTÃO/DÉBITO';
        }

        if (str_contains($normalizadaSemAcento, 'PIX')) {
            return 'PIX';
        }

        return 'OUTROS';
    }

    private function prepararItensFiscais(PreVenda $preVenda): array
    {
        $itens = $preVenda->itens->values();
        $rateioDescontos = $this->ratear((float) $preVenda->desconto, $itens->pluck('subtotal')->map(fn ($valor) => (float) $valor)->all());
        $rateioAcrescimos = $this->ratear((float) $preVenda->acrescimo, $itens->pluck('subtotal')->map(fn ($valor) => (float) $valor)->all());
        $subtotal = 0.0;
        $desconto = 0.0;
        $itensFiscais = [];

        foreach ($itens as $indice => $item) {
            $quantidade = (float) $item->quantidade;
            $subtotalItem = round((float) $item->subtotal + (float) $item->acrescimo + $rateioAcrescimos[$indice], 2);
            $descontoItem = round((float) $item->desconto + $rateioDescontos[$indice], 2);
            $subtotal += $subtotalItem;
            $desconto += $descontoItem;
            $itensFiscais[] = [
                'produto_id' => $item->produto_id,
                'descricao' => $item->descricao,
                'quantidade' => $quantidade,
                'valor_unitario' => round($subtotalItem / $quantidade, 4),
                'subtotal' => $subtotalItem,
                'desconto' => $descontoItem,
            ];
        }

        return [
            'itens' => $itensFiscais,
            'subtotal' => round($subtotal, 2),
            'desconto' => round($desconto, 2),
        ];
    }

    private function ratear(float $valor, array $bases): array
    {
        $valor = round($valor, 2);
        $totalBase = array_sum($bases);
        $restante = $valor;
        $rateio = [];
        $ultimoIndice = count($bases) - 1;

        foreach ($bases as $indice => $base) {
            $parcela = $indice === $ultimoIndice
                ? $restante
                : ($valor > 0 && $totalBase > 0 ? round($valor * ($base / $totalBase), 2) : 0.0);
            $rateio[] = $parcela;
            $restante = round($restante - $parcela, 2);
        }

        return $rateio;
    }

    private function normalizarItens(array $itens, int $empresaId): array
    {
        $produtoIds = collect($itens)->pluck('produto_id')->unique()->values();
        $produtos = Produto::query()
            ->where('empresa_id', $empresaId)
            ->whereIn('id', $produtoIds)
            ->get()
            ->keyBy('id');

        if ($produtos->count() !== $produtoIds->count()) {
            throw ValidationException::withMessages([
                'itens' => 'Um ou mais produtos não pertencem à empresa selecionada.',
            ]);
        }

        return collect($itens)->map(function (array $item) use ($produtos) {
            $produto = $produtos->get($item['produto_id']);

            return [
                'produto_id' => $produto->id,
                'codigo' => $produto->codigo,
                'descricao' => $produto->produto,
                'unidade' => $produto->un,
                'quantidade' => (float) $item['quantidade'],
                'valor_unitario' => array_key_exists('valor_unitario', $item) && $item['valor_unitario'] !== null
                    ? (float) $item['valor_unitario']
                    : (float) $produto->precovenda,
                'desconto' => (float) ($item['desconto'] ?? 0),
                'acrescimo' => (float) ($item['acrescimo'] ?? 0),
            ];
        })->all();
    }

    private function normalizarPagamentos(array $pagamentos, int $empresaId): array
    {
        $formaIds = collect($pagamentos)->pluck('forma_pag_id')->filter()->unique()->values();
        $formas = FormaPag::query()
            ->whereIn('id', $formaIds)
            ->where(function (Builder $query) use ($empresaId) {
                $query->where('empresa_id', $empresaId)->orWhere('empresa_id', 1);
            })
            ->get()
            ->keyBy('id');

        if ($formas->count() !== $formaIds->count()) {
            throw ValidationException::withMessages([
                'pagamentos' => 'Uma ou mais formas de pagamento não pertencem à empresa selecionada.',
            ]);
        }

        return collect($pagamentos)->map(function (array $pagamento) use ($formas) {
            $forma = ! empty($pagamento['forma_pag_id']) ? $formas->get($pagamento['forma_pag_id']) : null;

            return [
                'forma_pag_id' => $forma?->id,
                'descricao' => trim($pagamento['descricao'] ?? $forma?->descricao ?? ''),
                'valor' => round((float) $pagamento['valor'], 2),
                'vencimento' => $pagamento['vencimento'] ?? null,
            ];
        })->all();
    }

    private function sincronizarItensEPagamentos(PreVenda $preVenda, array $itens, array $pagamentos): void
    {
        $preVenda->itens()->delete();
        $preVenda->pagamentos()->delete();
        $preVenda->itens()->createMany($itens);

        if ($pagamentos !== []) {
            $preVenda->pagamentos()->createMany($pagamentos);
        }
    }

    private function buscarCliente(?int $clienteId, int $empresaId): ?Cliente
    {
        if (! $clienteId) {
            return null;
        }

        $cliente = Cliente::where('empresa_id', $empresaId)->whereKey($clienteId)->first();

        if (! $cliente) {
            throw ValidationException::withMessages([
                'cliente_id' => 'O cliente não pertence à empresa selecionada.',
            ]);
        }

        return $cliente;
    }

    private function validarAberta(PreVenda $preVenda): void
    {
        if ($preVenda->statusEfetivo() !== PreVendaStatusEnum::ABERTA) {
            throw ValidationException::withMessages([
                'status' => 'Somente pré-vendas abertas podem ser alteradas.',
            ]);
        }
    }

    private function resolverEmpresaId(array $dados, $user): int
    {
        $empresaId = (int) $user->empresa_id !== 1
            ? (int) $user->empresa_id
            : (int) ($dados['empresa_id'] ?? $user->empresa_id);

        if (! Empresa::whereKey($empresaId)->exists()) {
            throw ValidationException::withMessages([
                'empresa_id' => 'Empresa inválida.',
            ]);
        }

        return $empresaId;
    }

    private function aplicarEscopoEmpresa(Builder $query, array $filtros, $user): void
    {
        if ((int) $user->empresa_id !== 1) {
            $query->where('empresa_id', $user->empresa_id);

            return;
        }

        if (! empty($filtros['empresa_id'])) {
            $query->where('empresa_id', $filtros['empresa_id']);
        }
    }
}
