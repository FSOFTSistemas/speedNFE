@php
    $servico = $servico ?? null;
    $val = fn ($field) => old($field, data_get($servico, $field, ''));
@endphp

<div class="row">
    <div class="col-md-4 form-group">
        <label>Código</label>
        <input type="text" class="form-control" name="codigo" value="{{ $val('codigo') }}" required>
    </div>
    <div class="col-md-8 form-group">
        <label>Descrição</label>
        <input type="text" class="form-control" name="descricao" value="{{ $val('descricao') }}" required>
    </div>
</div>
<div class="row">
    <div class="col-md-3 form-group">
        <label>cClass (ANATEL)</label>
        <input type="text" class="form-control" name="cClass" value="{{ $val('cClass') }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label>CFOP</label>
        <input type="text" class="form-control" name="cfop" value="{{ $val('cfop') }}">
    </div>
    <div class="col-md-3 form-group">
        <label>Unidade</label>
        <input type="text" class="form-control" name="uMed" value="{{ $val('uMed') ?: 'UN' }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label>Valor Unitário</label>
        <input type="number" step="0.0001" class="form-control" name="valor" value="{{ $val('valor') }}" required>
    </div>
</div>

<hr>
<p class="mb-2"><strong>Tributos padrão (opcional — usados para pré-preencher na emissão)</strong></p>
<div class="row">
    <div class="col-md-3 form-group">
        <label>ICMS - CST (regime normal)</label>
        <input type="text" class="form-control" name="icms_cst" value="{{ $val('icms_cst') }}">
    </div>
    <div class="col-md-3 form-group">
        <label>ICMS - Alíquota %</label>
        <input type="number" step="0.01" class="form-control" name="icms_pICMS" value="{{ $val('icms_pICMS') }}">
    </div>
    <div class="col-md-3 form-group">
        <label>ICMS - Origem (Simples Nacional)</label>
        <select class="form-control" name="icms_orig">
            <option value="">-</option>
            @foreach (['0' => '0 - Nacional', '1' => '1 - Estrangeira (importação direta)', '2' => '2 - Estrangeira (mercado interno)'] as $codigo => $texto)
                <option value="{{ $codigo }}" @if ($val('icms_orig') == $codigo) selected @endif>{{ $texto }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label>ICMS - CSOSN (Simples Nacional)</label>
        <select class="form-control" name="icms_csosn">
            <option value="">-</option>
            @foreach (['101', '102', '103', '201', '202', '203', '300', '400', '500', '900'] as $codigo)
                <option value="{{ $codigo }}" @if ($val('icms_csosn') == $codigo) selected @endif>{{ $codigo }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-3 form-group">
        <label>PIS - CST</label>
        <input type="text" class="form-control" name="pis_cst" value="{{ $val('pis_cst') }}">
    </div>
    <div class="col-md-3 form-group">
        <label>PIS - Alíquota %</label>
        <input type="number" step="0.0001" class="form-control" name="pis_pPIS" value="{{ $val('pis_pPIS') }}">
    </div>
    <div class="col-md-3 form-group">
        <label>COFINS - CST</label>
        <input type="text" class="form-control" name="cofins_cst" value="{{ $val('cofins_cst') }}">
    </div>
    <div class="col-md-3 form-group">
        <label>COFINS - Alíquota %</label>
        <input type="number" step="0.0001" class="form-control" name="cofins_pCOFINS" value="{{ $val('cofins_pCOFINS') }}">
    </div>
</div>
<div class="row">
    <div class="col-md-3 form-group">
        <label>FUST - Alíquota %</label>
        <input type="number" step="0.0001" class="form-control" name="fust_pFUST" value="{{ $val('fust_pFUST') }}">
    </div>
    <div class="col-md-3 form-group">
        <label>FUNTTEL - Alíquota %</label>
        <input type="number" step="0.0001" class="form-control" name="funttel_pFUNTTEL" value="{{ $val('funttel_pFUNTTEL') }}">
    </div>
</div>
