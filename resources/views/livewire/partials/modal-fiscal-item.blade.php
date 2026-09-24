{{-- Modal de dados fiscais do item (usa o trait App\Http\Livewire\Concerns\EditaFiscalItensVenda) --}}
@php
    $cstsIcms = $fiscalSimples
        ? [
            '101' => '101 - Tributada com permissão de crédito',
            '102' => '102 - Tributada sem permissão de crédito',
            '103' => '103 - Isenção para faixa de receita bruta',
            '201' => '201 - Com crédito e cobrança de ICMS-ST',
            '202' => '202 - Sem crédito e com cobrança de ICMS-ST',
            '203' => '203 - Isenção e cobrança de ICMS-ST',
            '300' => '300 - Imune',
            '400' => '400 - Não tributada',
            '500' => '500 - ICMS cobrado anteriormente por ST',
            '900' => '900 - Outros',
        ]
        : [
            '00' => '00 - Tributada integralmente',
            '10' => '10 - Tributada e com cobrança de ICMS-ST',
            '20' => '20 - Com redução de base de cálculo',
            '30' => '30 - Isenta/não tributada e com cobrança de ICMS-ST',
            '40' => '40 - Isenta',
            '41' => '41 - Não tributada',
            '50' => '50 - Suspensão',
            '51' => '51 - Diferimento',
            '60' => '60 - ICMS cobrado anteriormente por ST',
            '70' => '70 - Redução de base e cobrança de ICMS-ST',
            '90' => '90 - Outras',
        ];

    $cstsPisCofins = [
        '01' => '01 - Tributável (alíquota básica)',
        '02' => '02 - Tributável (alíquota diferenciada)',
        '03' => '03 - Tributável (alíquota por unidade)',
        '04' => '04 - Monofásica (alíquota zero)',
        '05' => '05 - Substituição tributária',
        '06' => '06 - Alíquota zero',
        '07' => '07 - Isenta',
        '08' => '08 - Sem incidência',
        '09' => '09 - Com suspensão',
        '49' => '49 - Outras operações de saída',
        '50' => '50 - Direito a crédito (receita tributada)',
        '70' => '70 - Aquisição sem direito a crédito',
        '98' => '98 - Outras operações de entrada',
        '99' => '99 - Outras operações',
    ];

    $icmsHabilitado = $fiscalIndex !== null && $this->fiscalCampoHabilitado('icms');
    $stHabilitado = $fiscalIndex !== null && $this->fiscalCampoHabilitado('icms_st');
    $reducaoHabilitada = $fiscalIndex !== null && $this->fiscalCampoHabilitado('reducao');
    $itemFiscal = $fiscalIndex !== null ? ($vendaItens[$fiscalIndex] ?? null) : null;
@endphp

<div wire:ignore.self class="modal fade" id="modalFiscalItem" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title font-weight-bold">Dados fiscais do item</h5>
                    @if ($itemFiscal)
                        <small class="text-muted">
                            {{ $itemFiscal['descricao'] }} — {{ $itemFiscal['quantidade'] }} × R$ {{ number_format($itemFiscal['unitario'], 2, ',', '.') }}
                            = <strong>R$ {{ number_format($itemFiscal['quantidade'] * $itemFiscal['unitario'], 2, ',', '.') }}</strong>
                        </small>
                    @endif
                </div>
                <button type="button" class="close" wire:click="fecharFiscalItem"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                @if ($fiscalIndex !== null)
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Os campos são preenchidos automaticamente pelo cadastro do produto e recalculados conforme o
                        {{ $fiscalSimples ? 'CSOSN' : 'CST' }}, a base, a alíquota e a MVA. Todos podem ser ajustados manualmente.
                        Regime do emitente: <strong>{{ $fiscalSimples ? 'Simples Nacional (CSOSN)' : 'Regime Normal (CST)' }}</strong>.
                    </p>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CFOP</label>
                            <input type="text" inputmode="numeric" maxlength="4" class="form-control @error('fiscalForm.cfop') is-invalid @enderror" wire:model.lazy="fiscalForm.cfop">
                            @error('fiscalForm.cfop') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-9 mb-3">
                            <label class="form-label">{{ $fiscalSimples ? 'CSOSN' : 'CST ICMS' }}</label>
                            <select class="form-control @error('fiscalForm.cst_csosn') is-invalid @enderror" wire:model="fiscalForm.cst_csosn">
                                <option value="">Selecione...</option>
                                @foreach ($cstsIcms as $codigo => $descricao)
                                    <option value="{{ $codigo }}">{{ $descricao }}</option>
                                @endforeach
                            </select>
                            @error('fiscalForm.cst_csosn') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <h6 class="font-weight-bold border-bottom pb-2 mt-2">
                        {{ $fiscalSimples && in_array($fiscalForm['cst_csosn'] ?? '', ['101', '201']) ? 'Crédito de ICMS (Simples Nacional)' : 'ICMS' }}
                        @unless ($icmsHabilitado)
                            <small class="text-muted font-weight-normal">— não é enviado na NF-e com este {{ $fiscalSimples ? 'CSOSN' : 'CST' }}</small>
                        @endunless
                    </h6>
                    <div class="row">
                        @if ($reducaoHabilitada)
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label">Redução BC (%)</label>
                                <input type="number" step="0.0001" min="0" max="100" class="form-control" wire:model.lazy="fiscalForm.icms_reducao">
                            </div>
                        @endif
                        <div class="{{ $reducaoHabilitada ? 'col-6 col-md-3' : 'col-4' }} mb-3">
                            <label class="form-label">Base de cálculo</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.icms_base">
                        </div>
                        <div class="{{ $reducaoHabilitada ? 'col-6 col-md-3' : 'col-4' }} mb-3">
                            <label class="form-label">Alíquota (%)</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.icms_aliquota">
                        </div>
                        <div class="{{ $reducaoHabilitada ? 'col-6 col-md-3' : 'col-4' }} mb-3">
                            <label class="form-label">Valor</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.icms_valor">
                        </div>
                    </div>

                    <h6 class="font-weight-bold border-bottom pb-2 mt-2">
                        ICMS-ST
                        @unless ($stHabilitado)
                            <small class="text-muted font-weight-normal">— não é enviado na NF-e com este {{ $fiscalSimples ? 'CSOSN' : 'CST' }}</small>
                        @endunless
                    </h6>
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">MVA (%)</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.icms_st_mva">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">Base de cálculo ST</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.icms_st_base">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">Alíquota ST (%)</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.icms_st_aliquota">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">Valor ST</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.icms_st_valor">
                        </div>
                    </div>
                    @if ($stHabilitado)
                        <p class="text-muted small mt-n2">
                            Base ST = (valor do item − desconto) × (1 + MVA). Valor ST = base ST × alíquota ST −
                            {{ $fiscalSimples ? 'ICMS da operação própria pela alíquota interna' : (($fiscalForm['cst_csosn'] ?? '') === '30' ? 'sem dedução (CST 30)' : 'ICMS próprio') }}.
                            O ICMS-ST é somado ao total da NF-e.
                        </p>
                    @endif

                    @foreach (['pis' => 'PIS', 'cofins' => 'COFINS'] as $grupo => $titulo)
                        <h6 class="font-weight-bold border-bottom pb-2 mt-2">{{ $titulo }}</h6>
                        <div class="row">
                            <div class="col-md-12 col-lg-6 mb-3">
                                <label class="form-label">CST {{ $titulo }}</label>
                                <select class="form-control @error('fiscalForm.cst_'.$grupo) is-invalid @enderror" wire:model="fiscalForm.cst_{{ $grupo }}">
                                    <option value="">Selecione...</option>
                                    @foreach ($cstsPisCofins as $codigo => $descricao)
                                        <option value="{{ $codigo }}">{{ $descricao }}</option>
                                    @endforeach
                                </select>
                                @error('fiscalForm.cst_'.$grupo) <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-4 col-lg-2 mb-3">
                                <label class="form-label">Base</label>
                                <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.{{ $grupo }}_base">
                            </div>
                            <div class="col-4 col-lg-2 mb-3">
                                <label class="form-label">Alíq. (%)</label>
                                <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.{{ $grupo }}_aliquota">
                            </div>
                            <div class="col-4 col-lg-2 mb-3">
                                <label class="form-label">Valor</label>
                                <input type="number" step="0.01" min="0" class="form-control" wire:model.lazy="fiscalForm.{{ $grupo }}_valor">
                            </div>
                        </div>
                    @endforeach

                    @if ($errors->has('fiscalForm.*'))
                        <div class="alert alert-danger py-2 mb-0">Confira os campos destacados.</div>
                    @endif
                @endif
            </div>

            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" wire:click="restaurarFiscalPadrao" title="Descarta a personalização e volta a usar os tributos do cadastro do produto">
                    <i class="fas fa-undo mr-1"></i> Usar padrão do produto
                </button>
                <div>
                    <button type="button" class="btn btn-light" wire:click="fecharFiscalItem">Cancelar</button>
                    <button type="button" class="btn custom-btn custom-btn-primary" wire:click="salvarFiscalItem">
                        <i class="fas fa-check mr-1"></i> Aplicar ao item
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Campos enviados no formulário para cada item com dados fiscais personalizados --}}
@foreach ($vendaItens as $index => $vendaItem)
    @if (!empty($vendaItem['fiscal']))
        @foreach ($vendaItem['fiscal'] as $campo => $valor)
            <input type="hidden" name="vendaItens[{{ $index }}][fiscal][{{ $campo }}]" value="{{ $valor }}">
        @endforeach
    @endif
@endforeach
