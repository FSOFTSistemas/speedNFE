<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\FluxoDeCaixa;
use App\Models\PlanoDeConta;
use Carbon\Carbon;

class FluxoDeCaixaService
{
    public function listar($empresaId, array $filtros = [])
    {
        $query = FluxoDeCaixa::where('empresa_id', $empresaId);

        $dataInicio = $filtros['data_inicio'] ?? Carbon::today()->startOfDay();
        $dataFim = $filtros['data_fim'] ?? Carbon::today()->endOfDay();
        $query->whereBetween('data', [$dataInicio, $dataFim]);

        if (! empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }

        if (! empty($filtros['origem'])) {
            if ($filtros['origem'] === 'Manual') {
                $query->whereNull('origem');
            } else {
                $query->where('origem', $filtros['origem']);
            }
        }

        return $query->orderBy('data', 'asc')->get();
    }

    public function registrarEntradaAutomatica($empresaId, $valor, $descricao, $data, $origem, $origemId): ?FluxoDeCaixa
    {
        if (in_array($origem, ['NFe', 'NFCe']) && ! $this->deveLancarNFeNFCeNoFluxo($empresaId)) {
            return null;
        }

        $planoDeContas = PlanoDeConta::firstOrCreate(
            [
                'empresa_id' => $empresaId,
                'codigo' => 'AUTO-VENDAS-'.$empresaId,
            ],
            [
                'descricao' => 'Vendas (Automático)',
                'tipo' => 'Receita',
            ]
        );

        return FluxoDeCaixa::create([
            'data' => $data,
            'descricao' => $descricao,
            'valor' => $valor,
            'tipo' => 'Entrada',
            'plano_de_contas_id' => $planoDeContas->id,
            'origem' => $origem,
            'origem_id' => $origemId,
            'empresa_id' => $empresaId,
        ]);
    }

    private function deveLancarNFeNFCeNoFluxo($empresaId): bool
    {
        $configuracao = Empresa::whereKey($empresaId)->value('lancar_nfe_nfce_fluxo_caixa');

        return $configuracao === null ? true : (bool) $configuracao;
    }

    public function estornarPorOrigem($origem, $origemId): void
    {
        FluxoDeCaixa::where('origem', $origem)->where('origem_id', $origemId)->delete();
    }
}
