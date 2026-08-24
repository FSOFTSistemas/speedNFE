@php
    $nfse = $nfse ?? null;
    $val = fn ($field, $default = '') => old($field, data_get($nfse, $field, $default));
    $servicoSelecionado = old('servico_id', data_get($nfse, 'servico_id'));
    $servicosNacionais = $servicosNacionais ?? collect();
    $nbsList = $nbsList ?? collect();
    $indOps = $indOps ?? collect();
    $cidades = $cidades ?? collect();
    $codigoIbgeEmpresa = data_get($empresa, 'endereco.codigoIBGE', data_get($empresa, 'codigoIBGE'));
    $digitsOnly = fn ($value) => preg_replace('/\D/', '', (string) $value);
    $codigoServicoNacional = fn ($value) => $digitsOnly($value) === '' ? '' : str_pad($digitsOnly($value), 6, '0', STR_PAD_LEFT);
@endphp

@push('css')
<style>
    .nfse-flow {
        --nfse-green: #477b57;
        --nfse-blue: #2f438f;
        --nfse-muted: #7a8194;
        --nfse-border: #d8dee8;
    }
    .nfse-steps {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin: 12px 0 28px;
        position: relative;
    }
    .nfse-steps::before {
        background: var(--nfse-border);
        content: "";
        height: 2px;
        left: 8%;
        position: absolute;
        right: 8%;
        top: 28px;
    }
    .nfse-step {
        color: var(--nfse-muted);
        position: relative;
        text-align: center;
        z-index: 1;
    }
    .nfse-step-icon {
        align-items: center;
        background: #fff;
        border: 2px solid #8c929d;
        border-radius: 50%;
        display: inline-flex;
        height: 56px;
        justify-content: center;
        margin-bottom: 8px;
        width: 56px;
    }
    .nfse-step.active,
    .nfse-step.done {
        color: var(--nfse-blue);
        font-weight: 600;
    }
    .nfse-step.active .nfse-step-icon,
    .nfse-step.done .nfse-step-icon {
        background: var(--nfse-blue);
        border-color: var(--nfse-blue);
        color: #fff;
    }
    .nfse-panel {
        border: 1px solid var(--nfse-border);
        border-radius: 6px;
        margin-bottom: 16px;
        padding: 18px;
    }
    .nfse-panel-title {
        color: var(--nfse-green);
        font-size: 1.05rem;
        font-weight: 600;
        letter-spacing: .02em;
        margin-bottom: 16px;
        text-transform: uppercase;
    }
    .nfse-radio-group label {
        display: block;
        font-weight: 400;
        margin-bottom: 8px;
    }
    .nfse-summary {
        background: #f8fafc;
        border: 1px solid var(--nfse-border);
        border-radius: 6px;
        padding: 14px 16px;
    }
    .nfse-summary strong {
        color: #343a40;
        display: block;
        font-size: .8rem;
        text-transform: uppercase;
    }
    .nfse-summary span {
        color: #111827;
    }
    .nfse-flow-section {
        display: none;
    }
    .nfse-flow-section.active {
        display: block;
    }
    .nfse-flow .select2-container {
        width: 100% !important;
    }
    .nfse-flow .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        border-radius: .25rem;
        height: calc(2.25rem + 2px);
    }
    .nfse-flow .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 2px);
        padding-left: .75rem;
    }
    .nfse-flow .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }
    @media (max-width: 767px) {
        .nfse-steps {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            row-gap: 14px;
        }
        .nfse-steps::before {
            display: none;
        }
    }
</style>
@endpush

<div class="nfse-flow">
    <div class="nfse-steps" aria-label="Fluxo de emissão da NFS-e">
        <div class="nfse-step active" data-step-indicator="0">
            <div class="nfse-step-icon"><i class="fas fa-users"></i></div>
            <div>Pessoas</div>
        </div>
        <div class="nfse-step" data-step-indicator="1">
            <div class="nfse-step-icon"><i class="fas fa-wrench"></i></div>
            <div>Serviço</div>
        </div>
        <div class="nfse-step" data-step-indicator="2">
            <div class="nfse-step-icon"><i class="fas fa-dollar-sign"></i></div>
            <div>Valores</div>
        </div>
        <div class="nfse-step" data-step-indicator="3">
            <div class="nfse-step-icon"><i class="far fa-file-alt"></i></div>
            <div>Emitir NFS-e</div>
        </div>
    </div>

    <section class="nfse-flow-section active" data-step="0">
        <div class="nfse-panel">
            <div class="nfse-panel-title">Informações gerais</div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Informar dados da Reforma Tributária (Imposto sobre Bens e Serviços e Contribuição sobre Bens e Serviços)?</label>
                    <div class="nfse-radio-group">
                        <label><input type="radio" name="preencher_ibs_cbs" value="1" @if (old('preencher_ibs_cbs') == '1') checked @endif> Sim</label>
                        <label><input type="radio" name="preencher_ibs_cbs" value="0" @if (old('preencher_ibs_cbs', '0') == '0') checked @endif> Não</label>
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>Data em que o serviço foi prestado</label>
                    <input type="date" class="form-control" name="data_competencia" value="{{ $val('data_competencia', date('Y-m-d')) }}" required>
                </div>
            </div>
        </div>

        <div class="nfse-panel">
            <div class="nfse-panel-title">Emitente da NFS-e</div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Você irá emitir esta NFS-e como?</label>
                    <div class="nfse-radio-group">
                        <label><input type="radio" name="tpEmit" value="1" checked> Prestador/Fornecedor</label>
                        <label><input type="radio" name="tpEmit_visual" value="2" disabled> Tomador/Adquirente</label>
                        <label><input type="radio" name="tpEmit_visual" value="3" disabled> Intermediário</label>
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>Município</label>
                    <input type="text" class="form-control" value="{{ $codigoIbgeEmpresa }}" readonly>
                </div>
                <div class="col-md-4 form-group">
                    <label>Inscrição municipal do prestador</label>
                    <input type="text" class="form-control" value="{{ data_get($empresa, 'inscricao_municipal') }}" readonly>
                </div>
            </div>
        </div>

        <div class="nfse-panel">
            <div class="nfse-panel-title">Tomador/Adquirente do serviço</div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>Tomador do serviço</label>
                    <select class="form-control nfse-select2" name="cliente_id" id="cliente_id" data-placeholder="Pesquise pelo nome, CPF ou CNPJ do tomador" required>
                        <option value="">Selecione</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" data-documento="{{ $cliente->cpf_cnpj }}" @if ($val('cliente_id') == $cliente->id) selected @endif>
                                {{ $cliente->nome }} - {{ $cliente->cpf_cnpj }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>CPF ou CNPJ do tomador selecionado</label>
                    <input type="text" class="form-control" id="cliente_documento_preview" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label>Destinatário do serviço para apuração da Reforma Tributária</label>
                    <select class="form-control nfse-select2" name="indDest" data-placeholder="Selecione o destinatário para apuração tributária">
                        <option value="0" @if (($val('indDest') ?: '0') == '0') selected @endif>0 - Próprio tomador</option>
                        <option value="1" @if ($val('indDest') == '1') selected @endif>1 - Outro destinatário</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <section class="nfse-flow-section" data-step="1">
        <div class="nfse-panel">
            <div class="nfse-panel-title">Serviço</div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>Serviço cadastrado</label>
                    <select class="form-control nfse-select2" name="servico_id" id="servico_id" data-placeholder="Pesquise pelo código ou descrição do serviço cadastrado" required>
                        <option value="">Selecione</option>
                        @foreach ($servicos as $servico)
                            <option
                                value="{{ $servico->id }}"
                                data-valor="{{ $servico->valor }}"
                                data-ctribnac="{{ $codigoServicoNacional($servico->cTribNac) }}"
                                data-ctribmun="{{ $servico->cTribMun }}"
                                data-cnbs="{{ $digitsOnly($servico->cNBS) }}"
                                data-cindop="{{ $servico->cIndOp }}"
                                data-cclasstrib="{{ $servico->cClassTrib }}"
                                data-paliq="{{ $servico->pAliqISSQN }}"
                                data-descricao="{{ $servico->descricao }}"
                                @if ($servicoSelecionado == $servico->id) selected @endif
                            >
                                {{ $servico->codigo }} - {{ $servico->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label>Município onde o serviço foi prestado</label>
                    <select class="form-control nfse-select2" name="cLocPrestacao" id="cLocPrestacao" data-placeholder="Pesquise pelo nome da cidade, estado ou código IBGE" required>
                        <option value="">Selecione</option>
                        @foreach ($cidades as $cidade)
                            <option value="{{ $cidade->ibge }}" data-cidade="{{ $cidade->cidade }}" data-uf="{{ $cidade->uf }}" @if ($val('cLocPrestacao', $codigoIbgeEmpresa) == $cidade->ibge) selected @endif>
                                {{ $cidade->cidade }}/{{ $cidade->uf }} - Código IBGE {{ $cidade->ibge }}
                            </option>
                        @endforeach
                        @if ($val('cLocPrestacao', $codigoIbgeEmpresa) && ! $cidades->contains('ibge', $val('cLocPrestacao', $codigoIbgeEmpresa)))
                            <option value="{{ $val('cLocPrestacao', $codigoIbgeEmpresa) }}" selected>Código IBGE {{ $val('cLocPrestacao', $codigoIbgeEmpresa) }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label>Buscar município pelo CEP do local da prestação</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="cep_prestacao" placeholder="Digite o CEP">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary" id="buscar_cep_prestacao">
                                <i class="fas fa-search mr-1"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="cep_prestacao_status">Ao buscar pelo CEP, a cidade será selecionada automaticamente.</small>
                </div>
                <div class="col-md-12 form-group">
                    <label>Município de incidência do imposto, se diferente do local da prestação</label>
                    <select class="form-control nfse-select2" name="cLocIncid" id="cLocIncid" data-placeholder="Pesquise pelo nome da cidade, estado ou código IBGE">
                        <option value="">Usar o mesmo município da prestação</option>
                        @foreach ($cidades as $cidade)
                            <option value="{{ $cidade->ibge }}" data-cidade="{{ $cidade->cidade }}" data-uf="{{ $cidade->uf }}" @if ($val('cLocIncid') == $cidade->ibge) selected @endif>
                                {{ $cidade->cidade }}/{{ $cidade->uf }} - Código IBGE {{ $cidade->ibge }}
                            </option>
                        @endforeach
                        @if ($val('cLocIncid') && ! $cidades->contains('ibge', $val('cLocIncid')))
                            <option value="{{ $val('cLocIncid') }}" selected>Código IBGE {{ $val('cLocIncid') }}</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="nfse-panel">
            <div class="nfse-panel-title">Classificação nacional</div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>Código de tributação nacional do serviço</label>
                    <select class="form-control nfse-select2" name="cTribNac" data-placeholder="Pesquise pelo código ou descrição nacional do serviço" required>
                        <option value="">Selecione</option>
                        @foreach ($servicosNacionais as $item)
                            <option value="{{ $codigoServicoNacional($item->codigo) }}" @if ($codigoServicoNacional($val('cTribNac')) == $codigoServicoNacional($item->codigo)) selected @endif>
                                {{ $item->codigo }} - {{ $item->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label>Código municipal do serviço, quando o município exigir</label>
                    <input type="text" class="form-control" name="cTribMun" value="{{ $val('cTribMun') }}">
                </div>
                <div class="col-md-12 form-group">
                    <label>Código da Nomenclatura Brasileira de Serviços</label>
                    <select class="form-control nfse-select2" name="cNBS" data-placeholder="Pesquise pelo código ou descrição da Nomenclatura Brasileira de Serviços">
                        <option value="">Selecione</option>
                        @foreach ($nbsList as $item)
                            @if (strlen($digitsOnly($item->codigo)) === 9)
                                <option value="{{ $digitsOnly($item->codigo) }}" @if ($digitsOnly($val('cNBS')) == $digitsOnly($item->codigo)) selected @endif>
                                    {{ $item->codigo }} - {{ $item->descricao }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label>Indicador da operação para apuração da Reforma Tributária</label>
                    <select class="form-control nfse-select2" name="cIndOp" data-placeholder="Pesquise pelo indicador da operação da Reforma Tributária">
                        <option value="">Selecione</option>
                        @foreach ($indOps as $item)
                            <option value="{{ $item->codigo }}" @if ($val('cIndOp') == $item->codigo) selected @endif>
                                {{ $item->codigo }} - {{ $item->tipo_operacao }} {{ $item->local_operacao }} {{ $item->caracteristica_fornecimento }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Código de classificação tributária da Reforma Tributária</label>
                    <input type="text" class="form-control" name="cClassTrib" value="{{ $val('cClassTrib') }}" maxlength="6">
                </div>
                <div class="col-md-6 form-group">
                    <label>Alíquota do Imposto Sobre Serviços (%)</label>
                    <input type="number" step="0.0001" class="form-control" name="pAliq" value="{{ $val('pAliq') }}">
                </div>
                <div class="col-md-12 form-group">
                    <label>Descrição detalhada do serviço prestado</label>
                    <textarea class="form-control" name="discriminacao" rows="2" required>{{ $val('discriminacao') }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="nfse-flow-section" data-step="2">
        <div class="nfse-panel">
            <div class="nfse-panel-title">Valores</div>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>Valor bruto do serviço</label>
                    <input type="number" step="0.01" class="form-control nfse-money" name="vServ" value="{{ $val('vServ') }}" required>
                </div>
                <div class="col-md-3 form-group">
                    <label>Desconto Incondicionado</label>
                    <input type="number" step="0.01" class="form-control nfse-money" name="vDescIncond" value="{{ $val('vDescIncond', 0) }}">
                </div>
                <div class="col-md-3 form-group">
                    <label>Desconto Condicionado</label>
                    <input type="number" step="0.01" class="form-control nfse-money" name="vDescCond" value="{{ $val('vDescCond', 0) }}">
                </div>
                <div class="col-md-3 form-group">
                    <label>Deduções ou reduções permitidas</label>
                    <input type="number" step="0.01" class="form-control nfse-money" name="vDeducaoReducao" value="{{ $val('vDeducaoReducao', 0) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>Valor do Imposto Sobre Serviços</label>
                    <input type="number" step="0.01" class="form-control" name="vISSQN" value="{{ $val('vISSQN') }}">
                </div>
                <div class="col-md-3 form-group">
                    <label>Total de tributos retidos</label>
                    <input type="number" step="0.01" class="form-control nfse-money" name="vTotalRet" value="{{ $val('vTotalRet', 0) }}">
                </div>
                <div class="col-md-3 form-group">
                    <label>Valor total da NFS-e</label>
                    <input type="number" step="0.01" class="form-control" name="vTotNF" value="{{ $val('vTotNF') }}" readonly>
                </div>
                <div class="col-md-3 form-group">
                    <label>Valor líquido a receber</label>
                    <input type="number" step="0.01" class="form-control" name="vLiqPreview" value="{{ $val('vLiq') }}" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>Valor do Imposto sobre Bens e Serviços calculado com alíquota de 0,10%</label>
                    <input type="number" step="0.01" class="form-control" name="vIBS" value="{{ $val('vIBS', 0) }}" readonly>
                </div>
                <div class="col-md-3 form-group">
                    <label>Valor da Contribuição sobre Bens e Serviços calculado com alíquota de 0,90%</label>
                    <input type="number" step="0.01" class="form-control" name="vCBS" value="{{ $val('vCBS', 0) }}" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label>Informações complementares da nota</label>
                    <textarea class="form-control" name="informacoes_complementares" rows="2">{{ $val('informacoes_complementares') }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="nfse-flow-section" data-step="3">
        <div class="nfse-panel">
            <div class="nfse-panel-title">Revisão/Emissão</div>
            <div class="row">
                <div class="col-md-4 mb-3"><div class="nfse-summary"><strong>Tomador</strong><span data-summary="cliente">-</span></div></div>
                <div class="col-md-4 mb-3"><div class="nfse-summary"><strong>Serviço</strong><span data-summary="servico">-</span></div></div>
                <div class="col-md-4 mb-3"><div class="nfse-summary"><strong>Competência</strong><span data-summary="competencia">-</span></div></div>
                <div class="col-md-3 mb-3"><div class="nfse-summary"><strong>Código nacional do serviço</strong><span data-summary="ctribnac">-</span></div></div>
                <div class="col-md-3 mb-3"><div class="nfse-summary"><strong>Valor bruto do serviço</strong><span data-summary="vserv">R$ 0,00</span></div></div>
                <div class="col-md-3 mb-3"><div class="nfse-summary"><strong>Imposto Sobre Serviços</strong><span data-summary="vissqn">R$ 0,00</span></div></div>
                <div class="col-md-3 mb-3"><div class="nfse-summary"><strong>Total</strong><span data-summary="total">R$ 0,00</span></div></div>
            </div>
            <div class="alert alert-info mb-0">
                Ao salvar e enviar, o sistema gera a declaração da prestação do serviço, assina com o certificado digital da empresa, valida o arquivo XML e transmite para o ambiente nacional configurado.
            </div>
        </div>
    </section>

    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('nfse.index') }}" class="btn btn-secondary">Cancelar</a>
        <div>
            <button type="button" class="btn btn-outline-secondary" id="nfse-prev" style="display: none;"><i class="fas fa-chevron-left mr-1"></i> Voltar</button>
            <button type="button" class="btn btn-primary" id="nfse-next">Avançar <i class="fas fa-chevron-right ml-1"></i></button>
            <button type="submit" name="acao" value="rascunho" class="btn btn-success nfse-submit" style="display: none;">Salvar rascunho</button>
            <button type="submit" name="acao" value="emitir" class="btn btn-info nfse-submit" style="display: none;" onclick="return confirm('Salvar e transmitir essa NFS-e para o ambiente nacional?')">Salvar e enviar</button>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentStep = 0;
        const sections = Array.from(document.querySelectorAll('.nfse-flow-section'));
        const indicators = Array.from(document.querySelectorAll('[data-step-indicator]'));
        const prevButton = document.getElementById('nfse-prev');
        const nextButton = document.getElementById('nfse-next');
        const submitButtons = Array.from(document.querySelectorAll('.nfse-submit'));
        const serviceSelect = document.getElementById('servico_id');
        const clienteSelect = document.getElementById('cliente_id');
        const cepPrestacaoInput = document.getElementById('cep_prestacao');
        const cepPrestacaoButton = document.getElementById('buscar_cep_prestacao');
        const cepPrestacaoStatus = document.getElementById('cep_prestacao_status');
        const aliquotaIbs = 0.1;
        const aliquotaCbs = 0.9;
        const money = (value) => (Number(value || 0)).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        const field = (name) => document.querySelector(`[name="${name}"]`);
        const numberValue = (name) => parseFloat(field(name)?.value || '0') || 0;
        const setValue = (name, value, overwrite = false, label = null) => {
            const input = field(name);
            if (input && (overwrite || !input.value) && value !== undefined && value !== null && value !== '') {
                if (input.tagName === 'SELECT' && !Array.from(input.options).some((option) => option.value === String(value))) {
                    input.add(new Option(label || value, value, true, true));
                }
                input.value = value;
                if (window.jQuery && input.tagName === 'SELECT') {
                    $(input).trigger('change');
                }
            }
        };
        const selectedText = (select) => {
            if (!select || !select.value) return '-';
            return select.options[select.selectedIndex].text.trim();
        };
        const setSummary = (key, value) => {
            const target = document.querySelector(`[data-summary="${key}"]`);
            if (target) target.textContent = value || '-';
        };
        const updateClientePreview = () => {
            const preview = document.getElementById('cliente_documento_preview');
            if (!preview || !clienteSelect) return;
            preview.value = clienteSelect.options[clienteSelect.selectedIndex]?.dataset.documento || '';
        };
        const updateSummary = () => {
            setSummary('cliente', selectedText(clienteSelect));
            setSummary('servico', selectedText(serviceSelect));
            setSummary('competencia', field('data_competencia')?.value || '-');
            setSummary('ctribnac', field('cTribNac')?.value || '-');
            setSummary('vserv', money(numberValue('vServ')));
            setSummary('vissqn', money(numberValue('vISSQN')));
            setSummary('total', money(numberValue('vTotNF')));
        };
        const calculate = () => {
            const vServ = numberValue('vServ');
            const vDescIncond = numberValue('vDescIncond');
            const vDescCond = numberValue('vDescCond');
            const vDeducaoReducao = numberValue('vDeducaoReducao');
            const vTotalRet = numberValue('vTotalRet');
            const pAliq = numberValue('pAliq');
            const vBC = Math.max(0, vServ - vDescIncond - vDeducaoReducao);
            const vISSQN = Math.round((vBC * (pAliq / 100)) * 100) / 100;
            const vIBS = Math.round((vBC * (aliquotaIbs / 100)) * 100) / 100;
            const vCBS = Math.round((vBC * (aliquotaCbs / 100)) * 100) / 100;
            const vLiq = Math.max(0, vServ - vDescIncond - vDescCond - vTotalRet);
            const vTotNF = vLiq + vIBS + vCBS;
            setValue('vISSQN', vISSQN.toFixed(2), true);
            setValue('vIBS', vIBS.toFixed(2), true);
            setValue('vCBS', vCBS.toFixed(2), true);
            setValue('vTotNF', vTotNF.toFixed(2), true);
            setValue('vLiqPreview', vLiq.toFixed(2), true);
            updateSummary();
        };
        const showStep = (step) => {
            currentStep = Math.max(0, Math.min(step, sections.length - 1));
            sections.forEach((section, index) => section.classList.toggle('active', index === currentStep));
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentStep);
                indicator.classList.toggle('done', index < currentStep);
            });
            prevButton.style.display = currentStep === 0 ? 'none' : '';
            nextButton.style.display = currentStep === sections.length - 1 ? 'none' : '';
            submitButtons.forEach((button) => button.style.display = currentStep === sections.length - 1 ? '' : 'none');
            updateSummary();
        };
        const validateCurrentStep = () => {
            const inputs = Array.from(sections[currentStep].querySelectorAll('input, select, textarea'));
            for (const input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return false;
                }
            }
            return true;
        };

        if (window.jQuery && $.fn.select2) {
            $('.nfse-select2').each(function () {
                const placeholder = $(this).data('placeholder') || 'Selecione';
                $(this).select2({
                    placeholder,
                    allowClear: !this.required,
                    width: '100%',
                    language: {
                        noResults: () => 'Nenhum resultado encontrado',
                        searching: () => 'Buscando...',
                    },
                });
            });
            $('.nfse-select2').on('change', function () {
                updateClientePreview();
                updateSummary();
            });
        }

        const onlyNumbers = (value) => (value || '').replace(/\D/g, '');
        const setCepStatus = (message, className = 'text-muted') => {
            if (!cepPrestacaoStatus) return;
            cepPrestacaoStatus.className = `form-text ${className}`;
            cepPrestacaoStatus.textContent = message;
        };
        const buscarCepPrestacao = () => {
            const cep = onlyNumbers(cepPrestacaoInput?.value);
            if (!cep || cep.length !== 8) {
                setCepStatus('Informe um CEP com 8 dígitos para buscar o município.', 'text-danger');
                return;
            }

            setCepStatus('Buscando município pelo CEP...', 'text-muted');
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then((response) => response.json())
                .then((data) => {
                    if (data.erro || !data.ibge) {
                        setCepStatus('CEP não encontrado ou sem código de município.', 'text-danger');
                        return;
                    }

                    const label = `${data.localidade}/${data.uf} - Código IBGE ${data.ibge}`;
                    setValue('cLocPrestacao', data.ibge, true, label);
                    setCepStatus(`Município selecionado: ${label}.`, 'text-success');
                    updateSummary();
                })
                .catch(() => setCepStatus('Não foi possível buscar o CEP agora.', 'text-danger'));
        };

        document.querySelectorAll('[name="vServ"], [name="vDescIncond"], [name="vDescCond"], [name="vDeducaoReducao"], [name="vTotalRet"], [name="pAliq"]').forEach((input) => input.addEventListener('input', calculate));
        document.querySelectorAll('input, select, textarea').forEach((input) => {
            input.addEventListener('change', updateSummary);
            input.addEventListener('input', updateSummary);
        });
        if (cepPrestacaoButton) cepPrestacaoButton.addEventListener('click', buscarCepPrestacao);
        if (cepPrestacaoInput) {
            cepPrestacaoInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    buscarCepPrestacao();
                }
            });
        }
        if (clienteSelect) clienteSelect.addEventListener('change', updateClientePreview);
        if (serviceSelect) {
            serviceSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                setValue('vServ', selected.dataset.valor);
                setValue('cTribNac', selected.dataset.ctribnac);
                setValue('cTribMun', selected.dataset.ctribmun);
                setValue('cNBS', selected.dataset.cnbs);
                setValue('cIndOp', selected.dataset.cindop);
                setValue('cClassTrib', selected.dataset.cclasstrib);
                setValue('pAliq', selected.dataset.paliq);
                setValue('discriminacao', selected.dataset.descricao);
                calculate();
            });
        }
        prevButton.addEventListener('click', () => showStep(currentStep - 1));
        nextButton.addEventListener('click', () => {
            if (validateCurrentStep()) showStep(currentStep + 1);
        });

        updateClientePreview();
        calculate();
        showStep(0);
    });
</script>
@endpush
