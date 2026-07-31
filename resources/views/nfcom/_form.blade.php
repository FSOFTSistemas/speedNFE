@php
    $nfcom = $nfcom ?? null;
    $servicos = $servicos ?? collect();
    $isSimples = $isSimples ?? false;
    $old = fn ($field, $default = '') => old($field, data_get($nfcom, $field, $default));
@endphp

<style>
    .item-tabs .nav-link { border: none; border-bottom: 3px solid transparent; font-weight: 500; }
    .item-tabs .nav-link.active { border-bottom: 3px solid var(--primary-color, #00033a); }
</style>

<div class="row">
    <div class="col-md-6 form-group">
        <label for="cliente_id" class="form-label">Assinante (Cliente)</label>
        <select class="form-control" id="cliente_id" name="cliente_id" required>
            <option value="">Selecione</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}" @if ((string) $old('cliente_id', $nfcom->cliente_id ?? '') === (string) $cliente->id) selected @endif>
                    {{ $cliente->nome }} - {{ $cliente->cpf_cnpj }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="iCodAssinante" class="form-label">Código do Assinante</label>
        <input type="text" class="form-control" id="iCodAssinante" name="iCodAssinante" value="{{ $old('iCodAssinante') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label for="nContrato" class="form-label">Nº do Contrato</label>
        <input type="text" class="form-control" id="nContrato" name="nContrato" value="{{ $old('nContrato') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label for="tpAssinante" class="form-label">Tipo de Assinante</label>
        <input type="text" class="form-control" id="tpAssinante" name="tpAssinante" value="{{ $old('tpAssinante') }}" required>
        <small class="form-text text-muted">Código conforme tabela de domínios da NFCom (1-Comercial, 2-Industrial, 3-Residencial...).</small>
    </div>
    <div class="col-md-3 form-group">
        <label for="tpServUtil" class="form-label">Tipo de Serviço Utilizado</label>
        <input type="text" class="form-control" id="tpServUtil" name="tpServUtil" value="{{ $old('tpServUtil') }}" required>
        <small class="form-text text-muted">Código conforme tabela de domínios da NFCom.</small>
    </div>
    <div class="col-md-3 form-group">
        <label for="competFat" class="form-label">Competência do Faturamento</label>
        <input type="month" class="form-control" id="competFat" name="competFat" value="{{ $old('competFat') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label for="dVencFat" class="form-label">Vencimento da Fatura</label>
        <input type="date" class="form-control" id="dVencFat" name="dVencFat" value="{{ $old('dVencFat') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label for="dPerUsoIni" class="form-label">Período de Uso - Início</label>
        <input type="date" class="form-control" id="dPerUsoIni" name="dPerUsoIni" value="{{ $old('dPerUsoIni') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="dPerUsoFim" class="form-label">Período de Uso - Fim</label>
        <input type="date" class="form-control" id="dPerUsoFim" name="dPerUsoFim" value="{{ $old('dPerUsoFim') }}">
    </div>
    <div class="col-md-6 form-group">
        <label for="codBarras" class="form-label">Linha Digitável / Código de Barras</label>
        <input type="text" class="form-control" id="codBarras" name="codBarras" value="{{ $old('codBarras') }}">
    </div>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="m-0">Itens de Serviço</h5>
    <div>
        <a href="{{ route('servicos.index') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-concierge-bell mr-1"></i> Cadastrar Serviços
        </a>
        <button type="button" class="btn btn-sm custom-btn custom-btn-info" id="addItemBtn">
            <i class="fas fa-plus mr-1"></i> Adicionar item
        </button>
    </div>
</div>

<div id="itensContainer"></div>

<template id="itemTemplate">
    <div class="card item-card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Item __INDEX__</strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="form-group">
                <label>Selecionar serviço cadastrado (opcional)</label>
                <select class="form-control servico-select">
                    <option value="">Digitar manualmente</option>
                    @foreach ($servicos as $servico)
                        <option value="{{ $servico->id }}">{{ $servico->codigo }} - {{ $servico->descricao }}</option>
                    @endforeach
                </select>
            </div>

            <ul class="nav nav-tabs item-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="pill" href="#item-servico-__INDEX__" role="tab">Serviço</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="#item-tributos-__INDEX__" role="tab">Tributos</a>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="item-servico-__INDEX__" role="tabpanel">
                    <div class="row">
                        <div class="col-md-2 form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" name="itens[__INDEX__][cProd]" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Descrição do Serviço</label>
                            <input type="text" class="form-control" name="itens[__INDEX__][xProd]" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>cClass (ANATEL)</label>
                            <input type="text" class="form-control" name="itens[__INDEX__][cClass]" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>CFOP</label>
                            <input type="text" class="form-control" name="itens[__INDEX__][cfop]">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Unidade</label>
                            <input type="text" class="form-control" name="itens[__INDEX__][uMed]" value="UN" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 form-group">
                            <label>Quantidade</label>
                            <input type="number" step="0.0001" class="form-control item-calc" name="itens[__INDEX__][qFaturada]" value="1" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Valor Unitário</label>
                            <input type="number" step="0.0001" class="form-control item-calc" name="itens[__INDEX__][vItem]" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Desconto</label>
                            <input type="number" step="0.01" class="form-control item-calc" name="itens[__INDEX__][vDesc]" value="0">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Outras Despesas</label>
                            <input type="number" step="0.01" class="form-control item-calc" name="itens[__INDEX__][vOutro]" value="0">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Valor Total do Item</label>
                            <input type="number" step="0.01" class="form-control item-vprod" name="itens[__INDEX__][vProd]" required>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="item-tributos-__INDEX__" role="tabpanel">
                    @if ($isSimples)
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>ICMS - Origem</label>
                                <select class="form-control item-calc" name="itens[__INDEX__][icms_orig]">
                                    <option value="0" selected>0 - Nacional</option>
                                    <option value="1">1 - Estrangeira (importação direta)</option>
                                    <option value="2">2 - Estrangeira (mercado interno)</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>ICMS - CSOSN</label>
                                <select class="form-control item-calc" name="itens[__INDEX__][icms_csosn]">
                                    @foreach (['101', '102', '103', '201', '202', '203', '300', '400', '500', '900'] as $codigo)
                                        <option value="{{ $codigo }}" @if ($codigo === '102') selected @endif>{{ $codigo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label>ICMS - CST</label>
                                <input type="text" class="form-control" name="itens[__INDEX__][icms_cst]" value="00">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>ICMS - Base Cálc.</label>
                                <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][icms_vBC]" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>ICMS - Alíquota %</label>
                                <input type="number" step="0.01" class="form-control item-calc" name="itens[__INDEX__][icms_pICMS]" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>ICMS - Valor</label>
                                <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][icms_vICMS]" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>FCP - Alíquota %</label>
                                <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][icms_pFCP]" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>FCP - Valor</label>
                                <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][icms_vFCP]" value="0">
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-2 form-group">
                            <label>PIS - CST</label>
                            <input type="text" class="form-control" name="itens[__INDEX__][pis_cst]" value="01">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>PIS - Base Cálc.</label>
                            <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][pis_vBC]" value="0">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>PIS - Alíquota %</label>
                            <input type="number" step="0.0001" class="form-control item-calc" name="itens[__INDEX__][pis_pPIS]" value="0">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>PIS - Valor</label>
                            <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][pis_vPIS]" value="0">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>COFINS - CST</label>
                            <input type="text" class="form-control" name="itens[__INDEX__][cofins_cst]" value="01">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>COFINS - Base Cálc.</label>
                            <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][cofins_vBC]" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 form-group">
                            <label>COFINS - Alíquota %</label>
                            <input type="number" step="0.0001" class="form-control item-calc" name="itens[__INDEX__][cofins_pCOFINS]" value="0">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>COFINS - Valor</label>
                            <input type="number" step="0.01" class="form-control" name="itens[__INDEX__][cofins_vCOFINS]" value="0">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>FUST - Alíquota % / Base / Valor</label>
                            <div class="d-flex" style="gap: 4px;">
                                <input type="number" step="0.0001" class="form-control item-calc" placeholder="%" name="itens[__INDEX__][fust_pFUST]">
                                <input type="number" step="0.01" class="form-control" placeholder="Base" name="itens[__INDEX__][fust_vBC]">
                                <input type="number" step="0.01" class="form-control" placeholder="Valor" name="itens[__INDEX__][fust_vFUST]">
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>FUNTTEL - Alíquota % / Base / Valor</label>
                            <div class="d-flex" style="gap: 4px;">
                                <input type="number" step="0.0001" class="form-control item-calc" placeholder="%" name="itens[__INDEX__][funttel_pFUNTTEL]">
                                <input type="number" step="0.01" class="form-control" placeholder="Base" name="itens[__INDEX__][funttel_vBC]">
                                <input type="number" step="0.01" class="form-control" placeholder="Valor" name="itens[__INDEX__][funttel_vFUNTTEL]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@push('js')
<script>
const SERVICOS_DATA = @json($servicos->keyBy('id'));

(() => {
    const container = document.getElementById('itensContainer');
    const template = document.getElementById('itemTemplate');
    let index = 0;

    function addItem() {
        const html = template.innerHTML.replaceAll('__INDEX__', index);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        container.appendChild(wrapper.firstElementChild);
        index++;
        bindRow(container.lastElementChild);
    }

    function setField(row, campo, valor) {
        const input = row.querySelector('[name$="[' + campo + ']"]');
        if (input && valor !== null && valor !== undefined) {
            input.value = valor;
        }
    }

    function bindRow(row) {
        row.querySelector('.remove-item-btn').addEventListener('click', () => row.remove());

        const recalc = () => {
            const qtd = parseFloat(row.querySelector('[name$="[qFaturada]"]').value) || 0;
            const unit = parseFloat(row.querySelector('[name$="[vItem]"]').value) || 0;
            const desc = parseFloat(row.querySelector('[name$="[vDesc]"]').value) || 0;
            const outro = parseFloat(row.querySelector('[name$="[vOutro]"]').value) || 0;
            const total = (qtd * unit) - desc + outro;
            row.querySelector('.item-vprod').value = total.toFixed(2);

            const aplicarAliquota = (campoPercentual, campoBase, campoValor) => {
                const percInput = row.querySelector('[name$="[' + campoPercentual + ']"]');
                const baseInput = row.querySelector('[name$="[' + campoBase + ']"]');
                const valorInput = row.querySelector('[name$="[' + campoValor + ']"]');
                if (!percInput || !valorInput) {
                    return;
                }
                const perc = parseFloat(percInput.value) || 0;
                if (baseInput) {
                    baseInput.value = total.toFixed(2);
                }
                valorInput.value = (total * perc / 100).toFixed(2);
            };
            aplicarAliquota('icms_pICMS', 'icms_vBC', 'icms_vICMS');
            aplicarAliquota('pis_pPIS', 'pis_vBC', 'pis_vPIS');
            aplicarAliquota('cofins_pCOFINS', 'cofins_vBC', 'cofins_vCOFINS');
            aplicarAliquota('fust_pFUST', 'fust_vBC', 'fust_vFUST');
            aplicarAliquota('funttel_pFUNTTEL', 'funttel_vBC', 'funttel_vFUNTTEL');
        };
        row.querySelectorAll('.item-calc').forEach((el) => el.addEventListener('input', recalc));
        row.querySelectorAll('.item-calc').forEach((el) => el.addEventListener('change', recalc));

        const servicoSelect = row.querySelector('.servico-select');
        servicoSelect.addEventListener('change', () => {
            const servico = SERVICOS_DATA[servicoSelect.value];
            if (!servico) {
                return;
            }
            setField(row, 'cProd', servico.codigo);
            setField(row, 'xProd', servico.descricao);
            setField(row, 'cClass', servico.cClass);
            setField(row, 'cfop', servico.cfop);
            setField(row, 'uMed', servico.uMed);
            setField(row, 'vItem', servico.valor);
            setField(row, 'icms_cst', servico.icms_cst);
            setField(row, 'icms_pICMS', servico.icms_pICMS);
            setField(row, 'icms_pFCP', servico.icms_pFCP);
            setField(row, 'icms_orig', servico.icms_orig);
            setField(row, 'icms_csosn', servico.icms_csosn);
            setField(row, 'pis_cst', servico.pis_cst);
            setField(row, 'pis_pPIS', servico.pis_pPIS);
            setField(row, 'cofins_cst', servico.cofins_cst);
            setField(row, 'cofins_pCOFINS', servico.cofins_pCOFINS);
            setField(row, 'fust_pFUST', servico.fust_pFUST);
            setField(row, 'funttel_pFUNTTEL', servico.funttel_pFUNTTEL);
            recalc();
        });
    }

    document.getElementById('addItemBtn').addEventListener('click', addItem);

    @php
        $itemCamposJs = [
            'cProd', 'xProd', 'cClass', 'cfop', 'uMed', 'qFaturada', 'vItem', 'vDesc', 'vOutro', 'vProd',
            'icms_cst', 'icms_vBC', 'icms_pICMS', 'icms_vICMS', 'icms_pFCP', 'icms_vFCP',
            'icms_orig', 'icms_csosn',
            'pis_cst', 'pis_vBC', 'pis_pPIS', 'pis_vPIS',
            'cofins_cst', 'cofins_vBC', 'cofins_pCOFINS', 'cofins_vCOFINS',
            'fust_vBC', 'fust_pFUST', 'fust_vFUST',
            'funttel_vBC', 'funttel_pFUNTTEL', 'funttel_vFUNTTEL',
        ];
    @endphp
    @if (!empty($nfcom) && $nfcom->itens->count())
        @foreach ($nfcom->itens as $item)
            addItem();
            (() => {
                const row = container.children[{{ $loop->index }}];
                const data = @json($item->only($itemCamposJs));
                Object.keys(data).forEach((campo) => {
                    const input = row.querySelector('[name$="[' + campo + ']"]');
                    if (input && data[campo] !== null) {
                        input.value = data[campo];
                    }
                });
            })();
        @endforeach
    @else
        addItem();
    @endif
})();
</script>
@endpush
