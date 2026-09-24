<?php

namespace App\Services;

use App\Models\ItemPedido;
use App\Models\Produto;

/**
 * Dados fiscais editáveis por item do pedido (NFe): CFOP, CST/CSOSN, ICMS, ICMS-ST, PIS e COFINS.
 *
 * No formulário cada item carrega um array `fiscal` com as chaves de CAMPOS; no banco elas vão
 * para as colunas de item_pedidos (cfop -> cfop_item). Item sem `fiscal` continua sendo emitido
 * com os tributos do cadastro do produto, como antes.
 */
class ItemFiscalService
{
    public const CAMPOS_TEXTO = ['cfop', 'cst_csosn', 'cst_pis', 'cst_cofins'];

    public const CAMPOS_NUMERICOS = [
        'icms_base', 'icms_aliquota', 'icms_valor',
        'icms_st_mva', 'icms_st_base', 'icms_st_aliquota', 'icms_st_valor',
        'pis_base', 'pis_aliquota', 'pis_valor',
        'cofins_base', 'cofins_aliquota', 'cofins_valor',
    ];

    /** CSTs (regime normal) que destacam ICMS próprio. */
    public const CST_ICMS_PROPRIO = ['00', '10', '20', '51', '70', '90'];

    /** CSTs (regime normal) que destacam ICMS-ST. */
    public const CST_ICMS_ST = ['10', '30', '70', '90'];

    /** CSOSNs (Simples Nacional) que destacam ICMS-ST. */
    public const CSOSN_ICMS_ST = ['201', '202', '203', '900'];

    /** CSOSNs (Simples Nacional) com crédito de ICMS (pCredSN / vCredICMSSN). */
    public const CSOSN_CREDITO = ['101', '201', '900'];

    public static function simplesNacional($crt): bool
    {
        return in_array((int) $crt, [1, 4], true);
    }

    /**
     * Valores fiscais que a NFe usaria hoje para o item (cadastro do produto + CFOP do pedido),
     * usados para pré-preencher o formulário de edição.
     */
    public function padrao(Produto $produto, $quantidade, $unitario, $cfopPedido, $crt): array
    {
        $vProd = round((float) $quantidade * (float) $unitario, 2);
        $simples = self::simplesNacional($crt);
        $cst = $simples ? (string) $produto->cst_csosn : str_pad((string) $produto->cst_csosn, 2, '0', STR_PAD_LEFT);

        $destacaIcms = $simples ? in_array($cst, self::CSOSN_CREDITO, true) : in_array($cst, self::CST_ICMS_PROPRIO, true);
        $aliqIcms = $destacaIcms ? (float) $produto->icms : 0;
        $aliqPis = (float) $produto->pis;
        $aliqCofins = (float) $produto->cofins;

        return [
            'cfop' => (string) $cfopPedido,
            'cst_csosn' => $cst,
            'icms_base' => $destacaIcms ? $vProd : 0,
            'icms_aliquota' => $aliqIcms,
            'icms_valor' => $this->calcular($destacaIcms ? $vProd : 0, $aliqIcms),
            'icms_st_mva' => 0,
            'icms_st_base' => 0,
            'icms_st_aliquota' => 0,
            'icms_st_valor' => 0,
            'cst_pis' => (string) $produto->cst_pis,
            'pis_base' => $aliqPis > 0 ? $vProd : 0,
            'pis_aliquota' => $aliqPis,
            'pis_valor' => $this->calcular($aliqPis > 0 ? $vProd : 0, $aliqPis),
            'cst_cofins' => (string) $produto->cst_cofins,
            'cofins_base' => $aliqCofins > 0 ? $vProd : 0,
            'cofins_aliquota' => $aliqCofins,
            'cofins_valor' => $this->calcular($aliqCofins > 0 ? $vProd : 0, $aliqCofins),
        ];
    }

    public function calcular($base, $aliquota): float
    {
        return round((float) $base * (float) $aliquota / 100, 2);
    }

    /**
     * Reajusta as bases (mantendo a proporção de redução) e os valores quando a quantidade ou o
     * valor unitário do item mudam depois de os dados fiscais terem sido personalizados.
     */
    public function reajustarParaNovoValor(array $fiscal, float $vProdAntigo, float $vProdNovo): array
    {
        $fator = $vProdAntigo > 0 ? $vProdNovo / $vProdAntigo : 1;

        foreach (['icms', 'icms_st', 'pis', 'cofins'] as $grupo) {
            $fiscal[$grupo.'_base'] = round((float) ($fiscal[$grupo.'_base'] ?? 0) * $fator, 2);
            $fiscal[$grupo.'_valor'] = $this->calcular($fiscal[$grupo.'_base'], $fiscal[$grupo.'_aliquota'] ?? 0);
        }

        return $fiscal;
    }

    /** Converte o array `fiscal` do formulário nas colunas de item_pedidos. */
    public function paraColunas(?array $fiscal): array
    {
        if (empty($fiscal)) {
            return ['fiscal_personalizado' => false];
        }

        $colunas = ['fiscal_personalizado' => true];

        foreach (self::CAMPOS_TEXTO as $campo) {
            $valor = isset($fiscal[$campo]) ? trim((string) $fiscal[$campo]) : '';
            $colunas[$campo === 'cfop' ? 'cfop_item' : $campo] = $valor !== '' ? $valor : null;
        }

        foreach (self::CAMPOS_NUMERICOS as $campo) {
            $colunas[$campo] = $this->numero($fiscal[$campo] ?? 0);
        }

        return $colunas;
    }

    /** Array `fiscal` (formato do formulário) de um item salvo, ou null se não foi personalizado. */
    public function doItem(ItemPedido $item): ?array
    {
        if (! $item->fiscal_personalizado) {
            return null;
        }

        $fiscal = ['cfop' => (string) $item->cfop_item];

        foreach (['cst_csosn', 'cst_pis', 'cst_cofins'] as $campo) {
            $fiscal[$campo] = (string) $item->{$campo};
        }

        foreach (self::CAMPOS_NUMERICOS as $campo) {
            $fiscal[$campo] = (float) $item->{$campo};
        }

        return $fiscal;
    }

    public function regrasValidacao(): array
    {
        $regras = [
            'vendaItens.*.fiscal' => ['nullable', 'array'],
            'vendaItens.*.fiscal.cfop' => ['nullable', 'digits:4'],
            'vendaItens.*.fiscal.cst_csosn' => ['nullable', 'regex:/^\d{2,3}$/'],
            'vendaItens.*.fiscal.cst_pis' => ['nullable', 'digits:2'],
            'vendaItens.*.fiscal.cst_cofins' => ['nullable', 'digits:2'],
        ];

        foreach (self::CAMPOS_NUMERICOS as $campo) {
            $regras['vendaItens.*.fiscal.'.$campo] = ['nullable', 'numeric', 'min:0'];
        }

        return $regras;
    }

    public function mensagensValidacao(): array
    {
        return [
            'vendaItens.*.fiscal.cfop.digits' => 'O CFOP do item deve ter 4 dígitos.',
            'vendaItens.*.fiscal.cst_csosn.regex' => 'O CST/CSOSN do item deve ter 2 ou 3 dígitos.',
            'vendaItens.*.fiscal.cst_pis.digits' => 'O CST de PIS do item deve ter 2 dígitos.',
            'vendaItens.*.fiscal.cst_cofins.digits' => 'O CST de COFINS do item deve ter 2 dígitos.',
            'vendaItens.*.fiscal.*.numeric' => 'Os valores fiscais do item devem ser numéricos.',
            'vendaItens.*.fiscal.*.min' => 'Os valores fiscais do item não podem ser negativos.',
        ];
    }

    private function numero($valor): float
    {
        return round((float) str_replace(',', '.', (string) $valor), 4);
    }
}
