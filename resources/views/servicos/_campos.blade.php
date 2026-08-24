@php
    $servico = $servico ?? null;
    $val = fn ($field, $default = '') => old($field, data_get($servico, $field, $default));
    $servicosNacionais = $servicosNacionais ?? collect();
    $nbsList = $nbsList ?? collect();
    $indOps = $indOps ?? collect();
    $tabsId = $servico ? 'servico-tabs-'.$servico->id : 'servico-tabs-create';
    $digitsOnly = fn ($value) => preg_replace('/\D/', '', (string) $value);
    $codigoServicoNacional = fn ($value) => $digitsOnly($value) === '' ? '' : str_pad($digitsOnly($value), 6, '0', STR_PAD_LEFT);

    $textoServicoNacional = function ($codigo) use ($servicosNacionais, $codigoServicoNacional) {
        $item = $servicosNacionais->first(fn ($item) => $item->codigo === $codigo || $codigoServicoNacional($item->codigo) === $codigoServicoNacional($codigo));
        return $item ? $item->codigo.' - '.$item->descricao : $codigo;
    };

    $textoNbs = function ($codigo) use ($nbsList) {
        $digitsOnly = fn ($value) => preg_replace('/\D/', '', (string) $value);
        $item = $nbsList->first(fn ($item) => $item->codigo === $codigo || $digitsOnly($item->codigo) === $digitsOnly($codigo));
        return $item ? $item->codigo.' - '.$item->descricao : $codigo;
    };

    $textoIndOp = function ($codigo) use ($indOps) {
        $item = $indOps->firstWhere('codigo', $codigo);
        return $item ? trim($item->codigo.' - '.$item->tipo_operacao.' '.$item->local_operacao.' '.$item->caracteristica_fornecimento) : $codigo;
    };
@endphp

<ul class="nav nav-tabs servico-form-tabs" id="{{ $tabsId }}" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="{{ $tabsId }}-basicos-tab" data-toggle="tab" href="#{{ $tabsId }}-basicos" role="tab" aria-controls="{{ $tabsId }}-basicos" aria-selected="true">
            <i class="fas fa-list-alt mr-1"></i> Dados básicos
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="{{ $tabsId }}-nfse-tab" data-toggle="tab" href="#{{ $tabsId }}-nfse" role="tab" aria-controls="{{ $tabsId }}-nfse" aria-selected="false">
            <i class="fas fa-file-invoice mr-1"></i> NFS-e Nacional
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="{{ $tabsId }}-nfcom-tab" data-toggle="tab" href="#{{ $tabsId }}-nfcom" role="tab" aria-controls="{{ $tabsId }}-nfcom" aria-selected="false">
            <i class="fas fa-broadcast-tower mr-1"></i> Tributos para NFCom
        </a>
    </li>
</ul>

<div class="tab-content servico-form-tab-content" id="{{ $tabsId }}-content">
    <div class="tab-pane fade show active" id="{{ $tabsId }}-basicos" role="tabpanel" aria-labelledby="{{ $tabsId }}-basicos-tab">
        <div class="servico-form-section">
    <div class="servico-form-title">Dados básicos</div>
    <div class="row">
        <div class="col-md-4 form-group">
            <label>Código interno do serviço</label>
            <input type="text" class="form-control" name="codigo" value="{{ $val('codigo') }}" required>
        </div>
        <div class="col-md-8 form-group">
            <label>Descrição completa do serviço</label>
            <input type="text" class="form-control" name="descricao" value="{{ $val('descricao') }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 form-group">
            <label>Código de classificação do serviço para telecomunicação</label>
            <input type="text" class="form-control" name="cClass" value="{{ $val('cClass', '0000000') }}" required>
        </div>
        <div class="col-md-6 form-group">
            <label>Código Fiscal de Operações e Prestações</label>
            <input type="text" class="form-control" name="cfop" value="{{ $val('cfop') }}">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 form-group">
            <label>Unidade de medida</label>
            <input type="text" class="form-control" name="uMed" value="{{ $val('uMed', 'UN') }}" required>
        </div>
        <div class="col-md-6 form-group">
            <label>Valor unitário padrão do serviço</label>
            <input type="number" step="0.0001" class="form-control" name="valor" value="{{ $val('valor') }}" required>
        </div>
    </div>
</div>
    </div>

    <div class="tab-pane fade" id="{{ $tabsId }}-nfse" role="tabpanel" aria-labelledby="{{ $tabsId }}-nfse-tab">
        <div class="servico-form-section">
            <div class="servico-form-title">NFS-e Nacional</div>
            <div class="row">
        <div class="col-md-12 form-group">
            <label>Código de tributação nacional do serviço</label>
            <select class="form-control servico-select2 servico-domain-select" name="cTribNac" data-domain="servicos-nacionais" data-placeholder="Pesquise pelo código ou descrição nacional do serviço">
                <option value=""></option>
                @if ($val('cTribNac'))
                    <option value="{{ $codigoServicoNacional($val('cTribNac')) }}" selected>{{ $textoServicoNacional($val('cTribNac')) }}</option>
                @endif
            </select>
        </div>
        <div class="col-md-12 form-group">
            <label>Código municipal do serviço, quando o município exigir</label>
            <input type="text" class="form-control" name="cTribMun" value="{{ $val('cTribMun') }}">
        </div>
        <div class="col-md-12 form-group">
            <label>Código da Nomenclatura Brasileira de Serviços</label>
            <select class="form-control servico-select2 servico-domain-select" name="cNBS" data-domain="nbs" data-placeholder="Pesquise pelo código ou descrição da Nomenclatura Brasileira de Serviços">
                <option value=""></option>
                @if ($val('cNBS'))
                    <option value="{{ $digitsOnly($val('cNBS')) }}" selected>{{ $textoNbs($val('cNBS')) }}</option>
                @endif
            </select>
        </div>
        <div class="col-md-12 form-group">
            <label>Indicador da operação para apuração da Reforma Tributária</label>
            <select class="form-control servico-select2 servico-domain-select" name="cIndOp" data-domain="indicadores-operacao" data-placeholder="Pesquise pelo indicador da operação da Reforma Tributária">
                <option value=""></option>
                @if ($val('cIndOp'))
                    <option value="{{ $val('cIndOp') }}" selected>{{ $textoIndOp($val('cIndOp')) }}</option>
                @endif
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
            <input type="number" step="0.0001" class="form-control" name="pAliqISSQN" value="{{ $val('pAliqISSQN') }}">
        </div>
            </div>
            <div class="row">
        <div class="col-md-6 form-group">
            <label>Tributação do Imposto Sobre Serviços</label>
            <select class="form-control servico-select2" name="tribISSQN" data-placeholder="Selecione a tributação do Imposto Sobre Serviços">
                <option value=""></option>
                @foreach (['1' => '1 - Operação tributável', '2' => '2 - Imunidade', '3' => '3 - Exportação', '4' => '4 - Não incidência'] as $codigo => $texto)
                    <option value="{{ $codigo }}" @if ($val('tribISSQN') == $codigo) selected @endif>{{ $texto }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 form-group">
            <label>Retenção do Imposto Sobre Serviços</label>
            <select class="form-control servico-select2" name="tpRetISSQN" data-placeholder="Selecione a retenção do Imposto Sobre Serviços">
                <option value=""></option>
                @foreach (['1' => '1 - Não retido', '2' => '2 - Retido pelo tomador', '3' => '3 - Retido pelo intermediário'] as $codigo => $texto)
                    <option value="{{ $codigo }}" @if ($val('tpRetISSQN') == $codigo) selected @endif>{{ $texto }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 form-group">
            <label>Código de Situação Tributária da Reforma Tributária</label>
            <input type="text" class="form-control" name="cst_ibs_cbs" value="{{ $val('cst_ibs_cbs') }}" maxlength="3">
        </div>
        <div class="col-md-6 form-group">
            <label>Percentual redutor da Reforma Tributária (%)</label>
            <input type="number" step="0.0001" class="form-control" name="pRedutorIBSCBS" value="{{ $val('pRedutorIBSCBS') }}">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 form-group">
            <label>Finalidade da NFS-e</label>
            <select class="form-control servico-select2" name="finNFSe" data-placeholder="Selecione a finalidade da NFS-e">
                <option value=""></option>
                <option value="0" @if (($val('finNFSe') ?: '0') == '0') selected @endif>0 - NFS-e regular</option>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label>Operação com consumidor final?</label>
            <select class="form-control servico-select2" name="indFinal" data-placeholder="Selecione se é consumidor final">
                <option value=""></option>
                <option value="0" @if (($val('indFinal') ?: '0') == '0') selected @endif>0 - Não</option>
                <option value="1" @if ($val('indFinal') == '1') selected @endif>1 - Sim</option>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label>Destinatário da operação</label>
            <select class="form-control servico-select2" name="indDest" data-placeholder="Selecione o destinatário da operação">
                <option value=""></option>
                <option value="0" @if (($val('indDest') ?: '0') == '0') selected @endif>0 - Tomador</option>
                <option value="1" @if ($val('indDest') == '1') selected @endif>1 - Outro destinatário</option>
            </select>
        </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="{{ $tabsId }}-nfcom" role="tabpanel" aria-labelledby="{{ $tabsId }}-nfcom-tab">
        <div class="servico-form-section">
            <div class="servico-form-title">Tributos padrão para NFCom</div>
            <div class="row">
        <div class="col-md-6 form-group">
            <label>Código de Situação Tributária do ICMS no regime normal</label>
            <input type="text" class="form-control" name="icms_cst" value="{{ $val('icms_cst') }}">
        </div>
        <div class="col-md-6 form-group">
            <label>Alíquota do Imposto sobre Circulação de Mercadorias e Serviços (%)</label>
            <input type="number" step="0.01" class="form-control" name="icms_pICMS" value="{{ $val('icms_pICMS') }}">
        </div>
            </div>
            <div class="row">
        <div class="col-md-6 form-group">
            <label>Origem da mercadoria ou serviço no Simples Nacional</label>
            <select class="form-control servico-select2" name="icms_orig" data-placeholder="Selecione a origem no Simples Nacional">
                <option value=""></option>
                @foreach (['0' => '0 - Nacional', '1' => '1 - Estrangeira com importação direta', '2' => '2 - Estrangeira adquirida no mercado interno'] as $codigo => $texto)
                    <option value="{{ $codigo }}" @if ($val('icms_orig') == $codigo) selected @endif>{{ $texto }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 form-group">
            <label>Código de Situação da Operação no Simples Nacional</label>
            <select class="form-control servico-select2" name="icms_csosn" data-placeholder="Selecione a situação da operação no Simples Nacional">
                <option value=""></option>
                @foreach (['101', '102', '103', '201', '202', '203', '300', '400', '500', '900'] as $codigo)
                    <option value="{{ $codigo }}" @if ($val('icms_csosn') == $codigo) selected @endif>{{ $codigo }}</option>
                @endforeach
            </select>
        </div>
            </div>
            <div class="row">
        <div class="col-md-6 form-group">
            <label>Código de Situação Tributária do PIS</label>
            <input type="text" class="form-control" name="pis_cst" value="{{ $val('pis_cst') }}">
        </div>
        <div class="col-md-6 form-group">
            <label>Alíquota do PIS (%)</label>
            <input type="number" step="0.0001" class="form-control" name="pis_pPIS" value="{{ $val('pis_pPIS') }}">
        </div>
            </div>
            <div class="row">
        <div class="col-md-6 form-group">
            <label>Código de Situação Tributária da COFINS</label>
            <input type="text" class="form-control" name="cofins_cst" value="{{ $val('cofins_cst') }}">
        </div>
        <div class="col-md-6 form-group">
            <label>Alíquota da COFINS (%)</label>
            <input type="number" step="0.0001" class="form-control" name="cofins_pCOFINS" value="{{ $val('cofins_pCOFINS') }}">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 form-group">
            <label>Alíquota do Fundo de Universalização dos Serviços de Telecomunicações (%)</label>
            <input type="number" step="0.0001" class="form-control" name="fust_pFUST" value="{{ $val('fust_pFUST') }}">
        </div>
        <div class="col-md-6 form-group">
            <label>Alíquota do Fundo para o Desenvolvimento Tecnológico das Telecomunicações (%)</label>
            <input type="number" step="0.0001" class="form-control" name="funttel_pFUNTTEL" value="{{ $val('funttel_pFUNTTEL') }}">
        </div>
            </div>
        </div>
    </div>
</div>
