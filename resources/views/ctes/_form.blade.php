@php
    $cte = $cte ?? null;
    $old = fn ($field, $default = '') => old($field, data_get($cte, $field, $default));
@endphp

<div class="row">
    <div class="col-md-6 form-group">
        <label for="remetente_id" class="form-label">Remetente</label>
        <select class="form-control" id="remetente_id" name="remetente_id" required>
            <option value="">Selecione</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}" @if ((string) $old('remetente_id') === (string) $cliente->id) selected @endif>
                    {{ $cliente->nome }} - {{ $cliente->cpf_cnpj }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label for="destinatario_id" class="form-label">Destinatário</label>
        <select class="form-control" id="destinatario_id" name="destinatario_id" required>
            <option value="">Selecione</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}" @if ((string) $old('destinatario_id') === (string) $cliente->id) selected @endif>
                    {{ $cliente->nome }} - {{ $cliente->cpf_cnpj }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label for="toma" class="form-label">Tomador do Serviço</label>
        <select class="form-control" id="toma" name="toma" required>
            <option value="0" @if ((string) $old('toma', '0') === '0') selected @endif>Remetente</option>
            <option value="1" @if ((string) $old('toma') === '1') selected @endif>Destinatário</option>
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="cfop" class="form-label">CFOP</label>
        <input type="text" class="form-control" id="cfop" name="cfop" value="{{ $old('cfop', '5353') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label for="veiculo_id" class="form-label">Veículo de Tração</label>
        <select class="form-control" id="veiculo_id" name="veiculo_id" required>
            <option value="">Selecione</option>
            @foreach ($veiculos as $veiculo)
                <option value="{{ $veiculo->id }}" @if ((string) $old('veiculo_id') === (string) $veiculo->id) selected @endif>
                    {{ $veiculo->placa }} - {{ $veiculo->descricao }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="motorista_id" class="form-label">Motorista</label>
        <select class="form-control" id="motorista_id" name="motorista_id">
            <option value="">Selecione</option>
            @foreach ($motoristas as $motorista)
                <option value="{{ $motorista->id }}" @if ((string) $old('motorista_id') === (string) $motorista->id) selected @endif>
                    {{ $motorista->nome }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<hr>
<h5>Percurso</h5>
<div class="row">
    <div class="col-md-3 form-group">
        <label for="uf_inicio" class="form-label">UF Início</label>
        <select class="form-control" id="uf_inicio" name="uf_inicio" required>
            <option value="">Selecione</option>
            @foreach ($ufs ?? \App\Enums\UfEnum::cases() as $uf)
                <option value="{{ $uf->value }}" @if ($old('uf_inicio') === $uf->value) selected @endif>{{ $uf->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="mun_ini_codigo" class="form-label">Cód. IBGE Município Início</label>
        <input type="text" class="form-control" id="mun_ini_codigo" name="mun_ini_codigo" value="{{ $old('mun_ini_codigo') }}" required>
    </div>
    <div class="col-md-6 form-group">
        <label for="mun_ini_nome" class="form-label">Município de Início</label>
        <input type="text" class="form-control" id="mun_ini_nome" name="mun_ini_nome" value="{{ $old('mun_ini_nome') }}" required>
    </div>
</div>
<div class="row">
    <div class="col-md-3 form-group">
        <label for="uf_fim" class="form-label">UF Fim</label>
        <select class="form-control" id="uf_fim" name="uf_fim" required>
            <option value="">Selecione</option>
            @foreach ($ufs ?? \App\Enums\UfEnum::cases() as $uf)
                <option value="{{ $uf->value }}" @if ($old('uf_fim') === $uf->value) selected @endif>{{ $uf->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="mun_fim_codigo" class="form-label">Cód. IBGE Município Fim</label>
        <input type="text" class="form-control" id="mun_fim_codigo" name="mun_fim_codigo" value="{{ $old('mun_fim_codigo') }}" required>
    </div>
    <div class="col-md-6 form-group">
        <label for="mun_fim_nome" class="form-label">Município de Fim</label>
        <input type="text" class="form-control" id="mun_fim_nome" name="mun_fim_nome" value="{{ $old('mun_fim_nome') }}" required>
    </div>
</div>

<hr>
<h5>Carga</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label for="xProd" class="form-label">Produto Predominante</label>
        <input type="text" class="form-control" id="xProd" name="xProd" value="{{ $old('xProd') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label for="qCarga" class="form-label">Peso da Carga (kg)</label>
        <input type="number" step="0.0001" class="form-control" id="qCarga" name="qCarga" value="{{ $old('qCarga') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label for="vCarga" class="form-label">Valor da Carga</label>
        <input type="number" step="0.01" class="form-control" id="vCarga" name="vCarga" value="{{ $old('vCarga') }}" required>
    </div>
</div>

<hr>
<h5>Valores da Prestação</h5>
<div class="row">
    <div class="col-md-3 form-group">
        <label for="vTPrest" class="form-label">Valor Total da Prestação</label>
        <input type="number" step="0.01" class="form-control item-calc" id="vTPrest" name="vTPrest" value="{{ $old('vTPrest') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label for="vRec" class="form-label">Valor a Receber</label>
        <input type="number" step="0.01" class="form-control" id="vRec" name="vRec" value="{{ $old('vRec') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label for="picms" class="form-label">ICMS - Alíquota %</label>
        <input type="number" step="0.01" class="form-control item-calc" id="picms" name="picms" value="{{ $old('picms', '0') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label class="form-label">ICMS - Valor (calculado)</label>
        <input type="text" class="form-control" id="vicms_preview" readonly>
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label for="info_fisco" class="form-label">Informações ao Fisco</label>
        <input type="text" class="form-control" id="info_fisco" name="info_fisco" value="{{ $old('info_fisco') }}">
    </div>
    <div class="col-md-6 form-group">
        <label for="info_contribuinte" class="form-label">Informações ao Contribuinte</label>
        <input type="text" class="form-control" id="info_contribuinte" name="info_contribuinte" value="{{ $old('info_contribuinte') }}">
    </div>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="m-0">Documentos Transportados</h5>
    <button type="button" class="btn btn-sm custom-btn custom-btn-info" id="addDocBtn">
        <i class="fas fa-plus mr-1"></i> Adicionar documento
    </button>
</div>

<div id="documentosContainer"></div>

<template id="documentoTemplate">
    <div class="row documento-row align-items-end">
        <div class="col-md-3 form-group">
            <label>Tipo</label>
            <select class="form-control" name="documentos[__INDEX__][tipo_documento]" required>
                <option value="NFe">NFe</option>
                <option value="NFCom">NFCom</option>
            </select>
        </div>
        <div class="col-md-7 form-group">
            <label>Chave de Acesso (44 dígitos)</label>
            <input type="text" class="form-control" name="documentos[__INDEX__][chave]" minlength="44" maxlength="44" required>
        </div>
        <div class="col-md-2 form-group">
            <button type="button" class="btn btn-outline-danger remove-doc-btn"><i class="fas fa-trash"></i></button>
        </div>
    </div>
</template>

@push('js')
<script>
(() => {
    const container = document.getElementById('documentosContainer');
    const template = document.getElementById('documentoTemplate');
    let index = 0;

    function addDocumento(tipo, chave) {
        const html = template.innerHTML.replaceAll('__INDEX__', index);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const row = wrapper.firstElementChild;
        container.appendChild(row);
        if (tipo) {
            row.querySelector('select').value = tipo;
        }
        if (chave) {
            row.querySelector('input').value = chave;
        }
        row.querySelector('.remove-doc-btn').addEventListener('click', () => row.remove());
        index++;
    }

    document.getElementById('addDocBtn').addEventListener('click', () => addDocumento());

    const documentosExistentes = @json(optional($cte)->documentos ?? []);
    if (documentosExistentes.length) {
        documentosExistentes.forEach((doc) => addDocumento(doc.tipo_documento, doc.chave));
    } else {
        addDocumento();
    }

    const vTPrest = document.getElementById('vTPrest');
    const picms = document.getElementById('picms');
    const preview = document.getElementById('vicms_preview');
    const recalcIcms = () => {
        const total = parseFloat(vTPrest.value) || 0;
        const perc = parseFloat(picms.value) || 0;
        preview.value = (total * perc / 100).toFixed(2);
    };
    vTPrest.addEventListener('input', recalcIcms);
    picms.addEventListener('input', recalcIcms);
    recalcIcms();
})();
</script>
@endpush
