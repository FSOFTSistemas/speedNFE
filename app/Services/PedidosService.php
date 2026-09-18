<?php

namespace App\Services;

use App\Enums\EstadoEnum;
use App\Exceptions\LimitExceededException;
use App\Models\Ncm;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class PedidosService
{
    public static function referenciaItemDevolucaoHabilitada(): bool
    {
        $dataAtual = new \DateTime(date('Y-m-d'));
        $dataVirada = new \DateTime('2026-10-05');

        return $dataAtual >= $dataVirada;
    }

    /**
     * Cria um pedido (rascunho de NFe) pela API, com os itens e a fatura
     * associados. Espelha a orquestração de PedidosController::store(),
     * reaproveitando os mesmos services (ProdutosService, ItemService,
     * FaturaService) para não duplicar a lógica de negócio.
     *
     * @param  array  $data  Payload já validado por StorePedidoRequest (chaves: finalidade,
     *                       tipo, ref_nfe, cliente, cfop, vendaItens, info_complementares, aut_xml).
     */
    public function criarApi(
        array $data,
        int $userId,
        int $empresaId,
        ProdutosService $produtoService,
        ItemService $itemService,
        FaturaService $faturaService,
        EmpresasService $empresaService
    ): Pedido {
        $empresa = $empresaService->buscarEmpresa($empresaId);

        if ($this->limiteDeNotas($empresaId) >= $empresa->limNFes && $empresaId != 1) {
            throw new LimitExceededException('Limite de notas atingido para esta empresa.');
        }

        $isDevolucao = (int) $data['finalidade'] === 4;
        $usarReferenciaPorItem = $isDevolucao && self::referenciaItemDevolucaoHabilitada();

        $subtotal = 0;
        $desconto = 0;

        foreach ($data['vendaItens'] as $item) {
            $desconto += $item['desconto'] ?? 0;
            $subtotal += $item['quantidade'] * $item['unitario'];
        }

        return DB::transaction(function () use ($data, $userId, $empresaId, $subtotal, $desconto, $isDevolucao, $usarReferenciaPorItem, $produtoService, $itemService, $faturaService) {
            $pedido = $this->create(
                $userId,
                $data['cliente'],
                $subtotal,
                $desconto,
                $empresaId,
                $data['cfop'],
                $isDevolucao ? 4 : 1,
                $usarReferenciaPorItem ? null : ($data['ref_nfe'] ?? null),
                $data['tipo'],
                $data['info_complementares'] ?? null,
                $data['aut_xml'] ?? null
            );

            foreach ($data['vendaItens'] as $item) {
                $prod = $produtoService->um($item['produto_id']);

                if ((int) $data['finalidade'] === 1) {
                    $itemService->verificaVendaPorProduto($empresaId, $prod);
                }

                $itemService->create(
                    $pedido->id,
                    $prod,
                    $item['quantidade'],
                    $empresaId,
                    $item['desconto'] ?? 0,
                    $item['unitario'],
                    $usarReferenciaPorItem ? $item['dfe_referenciado_chave'] : null,
                    $usarReferenciaPorItem ? $item['dfe_referenciado_n_item'] : null
                );
            }

            $faturaService->create($subtotal - $desconto, $pedido->id, $pedido->finNF, $empresaId);

            return $pedido->load('itens.produto', 'cliente');
        });
    }

    public function create($user_id, $cliente_id, $subtotal, $desconto, $empresa, $cfop, $finalidade, $ref_nfe, $tipo, $info_complementares, $aut_xml = null)
    {

        return Pedido::create([
            'user_id' => $user_id,
            'cliente_id' => $cliente_id,
            'data' => today(),
            'status' => 2,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'total' => $subtotal,
            'finNF' => $finalidade,
            'tpNF' => $tipo,
            'empresa_id' => $empresa,
            'numero_nfe' => 0,
            'sequencia_evento' => 0,
            'chave' => '',
            'estado' => EstadoEnum::PENDENTE,
            'cfop' => $cfop,
            'ref_nfe' => $ref_nfe,
            'info_complementares' => $info_complementares,
            'aut_xml' => $aut_xml,
        ]);
    }

    public function update($id, $cliente, $subtotal, $desconto, $cfop, $info_complementares)
    {
        $pedido = Pedido::find($id);

        return $pedido->update([
            'cliente_id' => $cliente,
            'data' => today(),
            'status' => 0,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'total' => $subtotal,
            'cfop' => $cfop,
            'info_complementares' => $info_complementares,
        ]);
    }

    public function formatedVenda($idEmpresa, array $filtros = [])
    {
        if ($idEmpresa == 1) {
            $idEmpresa = '%';
        }

        $dataInicio = $filtros['data_inicio'] ?? now()->subMonths(2)->startOfDay()->format('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? now()->endOfDay()->format('Y-m-d');

        $query = DB::table('pedidos')
            ->select('pedidos.*', 'clientes.nome', 'clientes.cpf_cnpj', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'pedidos.empresa_id')
            ->leftJoin('clientes', 'clientes.id', '=', 'pedidos.cliente_id')
            ->where('pedidos.empresa_id', 'like', $idEmpresa)
            ->whereBetween('pedidos.data', [$dataInicio, $dataFim]);

        if (! empty($filtros['cliente'])) {
            $cliente = $filtros['cliente'];
            $query->where(function ($q) use ($cliente) {
                $q->where('clientes.nome', 'like', "%{$cliente}%")
                    ->orWhere('clientes.cpf_cnpj', 'like', "%{$cliente}%");
            });
        }

        if (! empty($filtros['chassi'])) {
            $chassi = $filtros['chassi'];
            $query->whereExists(function ($q) use ($chassi) {
                $q->select(DB::raw(1))
                    ->from('item_pedidos')
                    ->join('produtos', 'produtos.id', '=', 'item_pedidos.produto_id')
                    ->whereColumn('item_pedidos.pedido_id', 'pedidos.id')
                    ->where('produtos.chassiVeic', 'like', "%{$chassi}%");
            });
        }

        if (! empty($filtros['estado'])) {
            $query->where('pedidos.estado', $filtros['estado']);
        }

        return $query
            ->orderByDesc('pedidos.status')
            ->orderByDesc('pedidos.updated_at')
            ->get();
    }

    public function limiteDeNotas($empresa)
    {
        return DB::table('pedidos')
            ->where('empresa_id', '=', $empresa)
            ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
            ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
            ->count();
    }

    public function buscarPedidos($empresaId, array $filtros = [])
    {
        if ($empresaId == 1) {
            $empresaId = '%';
        }

        $dataInicio = $filtros['data_inicio'] ?? now()->subMonths(2)->startOfDay()->format('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? now()->endOfDay()->format('Y-m-d');

        $query = Pedido::select(
            'pedidos.*',
            'empresas.fantasia',
            'clientes.nome as cliente_nome',
            'clientes.cpf_cnpj as cliente_cpf_cnpj'
        )
            ->join('empresas', 'empresas.id', 'pedidos.empresa_id')
            ->leftJoin('clientes', 'clientes.id', 'pedidos.cliente_id')
            ->where('pedidos.empresa_id', 'like', $empresaId)
            ->where('pedidos.chave', '!=', '')
            ->whereBetween('pedidos.data', [$dataInicio, $dataFim]);

        if (! empty($filtros['cliente'])) {
            $cliente = $filtros['cliente'];
            $query->where(function ($q) use ($cliente) {
                $q->where('clientes.nome', 'like', "%{$cliente}%")
                    ->orWhere('clientes.cpf_cnpj', 'like', "%{$cliente}%");
            });
        }

        if (! empty($filtros['chassi'])) {
            $chassi = $filtros['chassi'];
            $query->whereHas('produtos', function ($q) use ($chassi) {
                $q->where('produtos.chassiVeic', 'like', "%{$chassi}%");
            });
        }

        if (! empty($filtros['estado'])) {
            $query->where('pedidos.estado', $filtros['estado']);
        }

        return $query->orderByDesc('pedidos.data')->get();
    }

    public function delete($id)
    {
        $pedido = Pedido::find($id);

        return $pedido->delete();
    }

    public function ncmAll()
    {
        return Ncm::all();
    }

    public function buscarPedido($id)
    {
        return Pedido::find($id);
    }

    public function cfopAll()
    {
        return DB::table('cfops')->get();
    }

    public function findCfop($id)
    {
        return DB::table('cfops')->where('id', $id)->first();
    }

    public function getTotalNFePerMonth($companyId)
    {
        $query = Pedido::selectRaw('MONTH(data) as mes, SUM(total) as total_vendas')
            ->where('estado', 'Autorizado')
            ->whereYear('data', date('Y')) // Filtra apenas o ano atual
            ->groupBy('mes')
            ->orderBy('mes', 'asc'); // Ordena corretamente os meses

        // Se não for a empresa "1", aplica o filtro por empresa
        if ($companyId != 1) {
            $query->where('empresa_id', $companyId);
        }

        return $query->get();
    }
}
