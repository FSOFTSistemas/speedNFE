<?php

namespace App\Http\Livewire\Concerns;

use App\Models\Empresa;
use App\Models\Produto;
use App\Services\ItemFiscalService;

/**
 * Modal de dados fiscais por item (CFOP, CST/CSOSN, ICMS, ICMS-ST, PIS e COFINS) das telas de
 * novo pedido e de edição de pedido. Os dados ficam em $vendaItens[$i]['fiscal'] e são enviados
 * no formulário como vendaItens[i][fiscal][campo]; item sem 'fiscal' usa o cadastro do produto.
 *
 * Requer no componente: $vendaItens, $empresa (id da empresa emitente) e $bcfop (CFOP do pedido).
 */
trait EditaFiscalItensVenda
{
    public $fiscalIndex = null;
    public $fiscalForm = [];
    public $fiscalSimples = false;

    public function abrirFiscalItem($index)
    {
        if (!isset($this->vendaItens[$index])) {
            return;
        }

        $item = $this->vendaItens[$index];
        $this->resetErrorBag();
        $this->fiscalIndex = $index;
        $this->fiscalSimples = ItemFiscalService::simplesNacional(optional(Empresa::find($this->empresa))->crt);

        if (!empty($item['fiscal'])) {
            $this->fiscalForm = $item['fiscal'];
        } else {
            $produto = Produto::find($item['produto_id']);
            $this->fiscalForm = (new ItemFiscalService())->padrao(
                $produto,
                $item['quantidade'],
                $item['unitario'],
                $this->bcfop,
                $this->fiscalSimples ? 1 : 3
            );
        }

        $this->dispatchBrowserEvent('abrirModalFiscal');
    }

    /** Recalcula o valor do grupo (icms, icms_st, pis, cofins) quando a base ou a alíquota mudam. */
    public function updatedFiscalForm($valor, $campo)
    {
        if (preg_match('/^(icms_st|icms|pis|cofins)_(base|aliquota)$/', $campo, $m)) {
            $grupo = $m[1];
            $this->fiscalForm[$grupo.'_valor'] = (new ItemFiscalService())->calcular(
                $this->fiscalForm[$grupo.'_base'] ?? 0,
                $this->fiscalForm[$grupo.'_aliquota'] ?? 0
            );
        }
    }

    public function salvarFiscalItem()
    {
        if ($this->fiscalIndex === null || !isset($this->vendaItens[$this->fiscalIndex])) {
            return;
        }

        $this->validate(
            [
                'fiscalForm.cfop' => ['required', 'digits:4'],
                'fiscalForm.cst_csosn' => ['required', 'regex:/^\d{2,3}$/'],
                'fiscalForm.cst_pis' => ['required', 'digits:2'],
                'fiscalForm.cst_cofins' => ['required', 'digits:2'],
            ] + collect(ItemFiscalService::CAMPOS_NUMERICOS)
                ->mapWithKeys(fn ($campo) => ['fiscalForm.'.$campo => ['nullable', 'numeric', 'min:0']])
                ->all(),
            [
                'fiscalForm.cfop.*' => 'Informe um CFOP com 4 dígitos.',
                'fiscalForm.cst_csosn.*' => 'Selecione o CST/CSOSN.',
                'fiscalForm.cst_pis.*' => 'Selecione o CST de PIS.',
                'fiscalForm.cst_cofins.*' => 'Selecione o CST de COFINS.',
                'fiscalForm.*.numeric' => 'Informe um número válido.',
                'fiscalForm.*.min' => 'O valor não pode ser negativo.',
            ]
        );

        $fiscal = $this->fiscalForm;

        foreach (ItemFiscalService::CAMPOS_NUMERICOS as $campo) {
            $fiscal[$campo] = (float) str_replace(',', '.', (string) ($fiscal[$campo] ?? 0));
        }

        // Zera os grupos que o CST/CSOSN escolhido não destaca, para não confundir totais
        if (!$this->fiscalCampoHabilitado('icms')) {
            $fiscal['icms_base'] = $fiscal['icms_aliquota'] = $fiscal['icms_valor'] = 0;
        }
        if (!$this->fiscalCampoHabilitado('icms_st')) {
            $fiscal['icms_st_mva'] = $fiscal['icms_st_base'] = $fiscal['icms_st_aliquota'] = $fiscal['icms_st_valor'] = 0;
        }

        $this->vendaItens[$this->fiscalIndex]['fiscal'] = $fiscal;
        $this->fecharFiscalItem();
    }

    /** Volta o item a usar os tributos do cadastro do produto. */
    public function restaurarFiscalPadrao()
    {
        if ($this->fiscalIndex !== null && isset($this->vendaItens[$this->fiscalIndex])) {
            $this->vendaItens[$this->fiscalIndex]['fiscal'] = null;
        }

        $this->fecharFiscalItem();
    }

    public function fecharFiscalItem()
    {
        $this->resetErrorBag();
        $this->fiscalIndex = null;
        $this->fiscalForm = [];
        $this->dispatchBrowserEvent('fecharModalFiscal');
    }

    /** Indica se o grupo ('icms' próprio ou 'icms_st') se aplica ao CST/CSOSN selecionado no modal. */
    public function fiscalCampoHabilitado($grupo): bool
    {
        $cst = (string) ($this->fiscalForm['cst_csosn'] ?? '');

        if ($this->fiscalSimples) {
            return $grupo === 'icms'
                ? in_array($cst, ItemFiscalService::CSOSN_CREDITO, true)
                : in_array($cst, ItemFiscalService::CSOSN_ICMS_ST, true);
        }

        return $grupo === 'icms'
            ? in_array($cst, ItemFiscalService::CST_ICMS_PROPRIO, true)
            : in_array($cst, ItemFiscalService::CST_ICMS_ST, true);
    }
}
