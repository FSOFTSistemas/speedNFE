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
        'icms_reducao', 'icms_base', 'icms_aliquota', 'icms_valor',
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

    /** CSOSNs (Simples Nacional) com crédito de ICMS (pCredSN / vCredICMSSN) ou ICMS próprio (900). */
    public const CSOSN_CREDITO = ['101', '201', '900'];

    /** CSTs (regime normal) com redução da base de cálculo do ICMS (pRedBC). */
    public const CST_REDUCAO_BC = ['20', '70'];

    /** CSTs de PIS/COFINS sem base, alíquota e valor (monofásico, alíquota zero, isento, sem incidência, suspensão). */
    public const CST_PIS_COFINS_SEM_VALOR = ['04', '05', '06', '07', '08', '09'];

    public static function destacaIcms($cst, bool $simples): bool
    {
        return in_array((string) $cst, $simples ? self::CSOSN_CREDITO : self::CST_ICMS_PROPRIO, true);
    }

    public static function destacaSt($cst, bool $simples): bool
    {
        return in_array((string) $cst, $simples ? self::CSOSN_ICMS_ST : self::CST_ICMS_ST, true);
    }

    public static function simplesNacional($crt): bool
    {
        return in_array((int) $crt, [1, 4], true);
    }

    /**
     * Valores fiscais que a NFe usaria hoje para o item (cadastro do produto + CFOP do pedido),
     * usados para pré-preencher o formulário de edição.
     */
    public function padrao(Produto $produto, $quantidade, $unitario, $cfopPedido, $crt, $desconto = 0): array
    {
        $simples = self::simplesNacional($crt);
        $cst = $simples ? (string) $produto->cst_csosn : str_pad((string) $produto->cst_csosn, 2, '0', STR_PAD_LEFT);
        $vProd = round((float) $quantidade * (float) $unitario, 2);

        $fiscal = array_fill_keys(self::CAMPOS_NUMERICOS, 0);
        $fiscal['cfop'] = (string) $cfopPedido;
        $fiscal['cst_csosn'] = $cst;
        $fiscal['cst_pis'] = (string) $produto->cst_pis;
        $fiscal['cst_cofins'] = (string) $produto->cst_cofins;

        // Aplica as mesmas regras de quando o usuário escolhe cada CST no formulário
        foreach (['cst_csosn', 'cst_pis', 'cst_cofins'] as $campo) {
            $fiscal = $this->aplicarRegras($fiscal, $campo, $vProd, (float) $desconto, $simples, $produto);
        }

        return $fiscal;
    }

    /**
     * Preenche/recalcula os campos dependentes quando $campoAlterado muda no formulário.
     *
     * - Base da operação = valor do item (qtde x unitário) - desconto.
     * - Troca de CST/CSOSN: sugere base e alíquota do produto para os grupos que o código destaca
     *   e zera os que ele não destaca.
     * - CST 20/70: base = base da operação x (1 - redução%); digitar a base recalcula a redução.
     * - ICMS-ST: base = base da operação x (1 + MVA%); valor = base ST x alíquota ST - ICMS próprio
     *   (no Simples o ICMS próprio é calculado com a alíquota interna, a mesma da ST; no CST 30 não há dedução).
     * - PIS/COFINS com CST 04 a 09 não têm base, alíquota nem valor.
     *
     * Os campos de valor podem ser ajustados manualmente e não são sobrescritos quando alterados.
     */
    public function aplicarRegras(array $f, string $campoAlterado, float $vProd, float $desconto, bool $simples, ?Produto $produto = null): array
    {
        foreach (self::CAMPOS_NUMERICOS as $campo) {
            $f[$campo] = $this->numero($f[$campo] ?? 0);
        }

        $baseOperacao = max(0, round($vProd - $desconto, 2));
        $cst = (string) ($f['cst_csosn'] ?? '');
        $temIcms = self::destacaIcms($cst, $simples);
        $temSt = self::destacaSt($cst, $simples);
        $temReducao = !$simples && in_array($cst, self::CST_REDUCAO_BC, true);

        // --- ICMS próprio ---
        if ($campoAlterado === 'cst_csosn') {
            if ($temIcms) {
                if ($f['icms_aliquota'] <= 0 && $produto) {
                    $f['icms_aliquota'] = (float) $produto->icms;
                }
                $f['icms_reducao'] = $temReducao ? $f['icms_reducao'] : 0;
                $f['icms_base'] = $this->baseReduzida($baseOperacao, $f['icms_reducao']);
            } else {
                $f['icms_reducao'] = $f['icms_base'] = $f['icms_aliquota'] = $f['icms_valor'] = 0;
            }
        } elseif ($campoAlterado === 'icms_reducao') {
            $f['icms_base'] = $this->baseReduzida($baseOperacao, $f['icms_reducao']);
        } elseif ($campoAlterado === 'icms_base' && $temReducao) {
            $f['icms_reducao'] = $baseOperacao > 0
                ? round(max(0, 1 - $f['icms_base'] / $baseOperacao) * 100, 4)
                : 0;
        }

        if ($temIcms && in_array($campoAlterado, ['cst_csosn', 'icms_reducao', 'icms_base', 'icms_aliquota'], true)) {
            $f['icms_valor'] = $this->calcular($f['icms_base'], $f['icms_aliquota']);
        }

        // --- ICMS-ST ---
        if ($campoAlterado === 'cst_csosn' && !$temSt) {
            $f['icms_st_mva'] = $f['icms_st_base'] = $f['icms_st_aliquota'] = $f['icms_st_valor'] = 0;
        }

        if ($temSt) {
            // No Simples o ICMS do produto é o % de crédito, não a alíquota interna: fica para o usuário
            if ($campoAlterado === 'cst_csosn' && $f['icms_st_aliquota'] <= 0 && $produto && !$simples) {
                $f['icms_st_aliquota'] = (float) $produto->icms;
            }

            if (in_array($campoAlterado, ['cst_csosn', 'icms_st_mva'], true)) {
                $f['icms_st_base'] = round($baseOperacao * (1 + $f['icms_st_mva'] / 100), 2);
            }

            $gatilhosSt = ['cst_csosn', 'icms_reducao', 'icms_base', 'icms_aliquota', 'icms_valor', 'icms_st_mva', 'icms_st_base', 'icms_st_aliquota'];
            if (in_array($campoAlterado, $gatilhosSt, true)) {
                if ($simples) {
                    $deducao = $this->calcular($baseOperacao, $f['icms_st_aliquota']);
                } else {
                    $deducao = $cst === '30' ? 0 : $f['icms_valor'];
                }
                $f['icms_st_valor'] = max(0, round($this->calcular($f['icms_st_base'], $f['icms_st_aliquota']) - $deducao, 2));
            }
        }

        // --- PIS / COFINS ---
        foreach (['pis', 'cofins'] as $grupo) {
            $cstGrupo = (string) ($f['cst_'.$grupo] ?? '');
            $semValor = in_array($cstGrupo, self::CST_PIS_COFINS_SEM_VALOR, true);

            if ($campoAlterado === 'cst_'.$grupo) {
                if ($semValor) {
                    $f[$grupo.'_base'] = $f[$grupo.'_aliquota'] = $f[$grupo.'_valor'] = 0;
                    continue;
                }
                if ($f[$grupo.'_aliquota'] <= 0 && $produto) {
                    $f[$grupo.'_aliquota'] = (float) $produto->{$grupo};
                }
                $f[$grupo.'_base'] = $f[$grupo.'_aliquota'] > 0 ? $baseOperacao : 0;
            }

            if (!$semValor && in_array($campoAlterado, ['cst_'.$grupo, $grupo.'_base', $grupo.'_aliquota'], true)) {
                $f[$grupo.'_valor'] = $this->calcular($f[$grupo.'_base'], $f[$grupo.'_aliquota']);
            }
        }

        return $f;
    }

    private function baseReduzida(float $baseOperacao, $reducao): float
    {
        return round($baseOperacao * (1 - min(100, max(0, (float) $reducao)) / 100), 2);
    }

    public function calcular($base, $aliquota): float
    {
        return round((float) $base * (float) $aliquota / 100, 2);
    }

    /**
     * Reajusta as bases (mantendo a proporção de redução) e os valores quando a quantidade ou o
     * valor unitário do item mudam depois de os dados fiscais terem sido personalizados.
     */
    public function reajustarParaNovoValor(array $fiscal, float $vProdAntigo, float $vProdNovo, float $desconto = 0, bool $simples = false): array
    {
        $fator = $vProdAntigo > 0 ? $vProdNovo / $vProdAntigo : 1;

        foreach (['icms', 'icms_st', 'pis', 'cofins'] as $grupo) {
            $fiscal[$grupo.'_base'] = round((float) ($fiscal[$grupo.'_base'] ?? 0) * $fator, 2);
        }

        foreach (['icms_base', 'icms_st_base', 'pis_base', 'cofins_base'] as $campo) {
            $fiscal = $this->aplicarRegras($fiscal, $campo, $vProdNovo, $desconto, $simples);
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
