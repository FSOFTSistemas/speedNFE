@extends('adminlte::page')

@section('title', 'Editar Produto')

@push('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --input-focus-border: #80bdff;
        --input-focus-shadow: rgba(0, 3, 58, .25);
    }
    body {
        font-family: 'Poppins', sans-serif;
    }
    .card-main {
        background: var(--card-bg) !important;
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color) !important;
        padding: 30px;
    }
    .custom-btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
        padding: 12px 20px !important;
        transition: all 0.3s ease !important;
    }
    .custom-btn-primary:hover {
        background-color: #00045e !important;
        border-color: #00045e !important;
        transform: translateY(-2px);
    }
    .custom-btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 10px 15px;
        height: auto;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow);
    }
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--text-light, #6c757d);
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
        background-color: transparent;
    }
    
    /* Select2 Custom */
    .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
        height: calc(1.5em + .75rem + 12px) !important;
        padding: 8px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem + 10px) !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--input-focus-border) !important;
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow) !important;
    }
    .select2-dropdown {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
    }
    
    /* Header da Reforma */
    .rtc-header {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid var(--primary-color);
        font-weight: 600;
        color: var(--primary-color);
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Editar Produto</h1>
        </div>
        <div class="col-md-2 text-md-right mt-2 mt-md-0">
            <a href="{{ route('produto.index') }}" class="btn custom-btn-secondary btn-block">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body">
            <ul class="nav nav-tabs" id="tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                        aria-controls="home" aria-selected="true"><b>Informações do Produto</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                        aria-controls="profile" aria-selected="false"><b>Informações Fiscais</b></a>
                </li>
                @if ($produto->tpProd == 1)
                    <li class="nav-item" id="veicTab">
                        <a class="nav-link" id="veic-tab" data-toggle="pill" href="#veic" role="tab" aria-controls="veic"
                            aria-selected="false"><b>Informações de Veículo</b></a>
                    </li>
                @endif
            </ul>

            <form class="needs-validation mt-4" novalidate method="POST" action="{{ route('update_produto', ['id' => $produto->id]) }}">
                @csrf
                @method('PUT')
                
                <div class="tab-content" id="tabContent">
                    {{-- ABA 1: INFORMAÇÕES DO PRODUTO --}}
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">
                            @if ($user->empresa_id == 1)
                                <div class="col-md-6 mb-3">
                                    <label for="empresa" class="form-label">Empresa (Admin)</label>
                                    <select class="form-select select2-basic" name="empresa" id="empresa" required>
                                        <option value="" disabled selected>Selecione uma Empresa</option>
                                        @foreach ($empresas as $emp)
                                            <option value="{{ $emp->id }}" @if ($produto->empresa_id == $emp->id) selected @endif>{{ $emp->fantasia }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                {{-- Usuário comum não muda a empresa --}}
                                <input type="hidden" name="empresa" id="empresa" value="{{ $produto->empresa_id }}">
                            @endif
                            
                            <div class="col-md-6 mb-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select class="form-select select2-basic" name="categoria" id="categoria" required>
                                    <option value="" disabled selected>Selecione uma Categoria</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" @if ($produto->categoria_id == $categoria->id) selected @endif>{{ $categoria->descricao }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Informe uma categoria.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9 mb-3">
                                <label for="produto" class="form-label">Nome do Produto</label>
                                <input class="form-control" type="text" name="produto" id="produto" required 
                                    placeholder="Ex: REFRIGERANTE COCA-COLA 2L" 
                                    oninput="this.value = this.value.toUpperCase()" 
                                    value="{{ $produto->produto }}">
                                <div class="invalid-feedback">Informe um nome de produto válido.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="codigo" class="form-label">Cód. de Barras (EAN)</label>
                                <input class="form-control" type="text" name="codigo" id="codigo" 
                                    placeholder="SEM GTIN" 
                                    oninput="this.value = this.value.toUpperCase()" 
                                    value="{{ $produto->codigo }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="ncm" class="form-label">NCM</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" name="ncm" id="ncm" required 
                                        placeholder="Nomenclatura Comum do Mercosul" 
                                        value="{{ $produto->ncm }}">
                                    <div class="input-group-append">
                                        <button title="Buscar Ncm" class="btn btn-dark" type="button" data-toggle="modal" data-target="#NcmModal" style="border-radius: 0 8px 8px 0;"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="invalid-feedback">Informe um NCM válido.</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="precocusto" class="form-label">Preço de Custo</label>
                                <input class="form-control" type="number" name="precocusto" id="precocusto" step="0.01" required 
                                    placeholder="R$ 0,00" value="{{ $produto->precocusto }}">
                                <div class="invalid-feedback">Informe um preço de custo válido.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="precovenda" class="form-label">Preço de Venda</label>
                                <input class="form-control" type="number" name="precovenda" step="0.01" id="precovenda" required 
                                    placeholder="R$ 0,00" value="{{ $produto->precovenda }}">
                                <div class="invalid-feedback">Informe um preço de venda válido.</div>
                            </div>
                        </div>

                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="un" class="form-label">Unidade de Medida</label>
                                <select name="un" id="un" class="form-select select2-basic" required>
                                    <option value="" disabled selected>Selecione</option>
                                    <option value="un" @if ($produto->un == 'un') selected @endif>UN</option>
                                    <option value="cx" @if ($produto->un == 'cx') selected @endif>CX</option>
                                    <option value="kg" @if ($produto->un == 'kg') selected @endif>KG</option>
                                    <option value="l" @if ($produto->un == 'l') selected @endif>L</option>
                                    <option value="ml" @if ($produto->un == 'ml') selected @endif>ML</option>
                                    <option value="m" @if ($produto->un == 'm') selected @endif>M</option>
                                    <option value="cm" @if ($produto->un == 'cm') selected @endif>CM</option>
                                    <option value="fd" @if ($produto->un == 'fd') selected @endif>FD</option>
                                    <option value="mil" @if ($produto->un == 'mil') selected @endif>MIL</option>
                                    <option value="sc" @if ($produto->un == 'sc') selected @endif>SC</option>
                                </select>
                                <div class="invalid-feedback">Informe uma unidade válida.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="estoque" class="form-label">Estoque Atual</label>
                                <input class="form-control" type="number" readonly disabled 
                                    value="{{ $produto->estoque->estoque ?? 0 }}">
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="tpProd" id="tpProd" @if ($produto->tpProd == 1) checked @endif>
                                    <label class="form-check-label text-bold" for="tpProd">É Veículo?</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ABA 2: INFORMAÇÕES FISCAIS --}}
{{-- ABA 2: INFORMAÇÕES FISCAIS --}}
<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    
    <div class="rtc-header mt-2 mb-4">
        <i class="fas fa-university mr-2"></i> Regime Tributário: 
        <strong>{{ in_array($empresa->crt, [1, 2, 4]) ? 'Simples Nacional / MEI' : 'Regime Normal' }}</strong>
    </div>

    {{-- BLOCO 1: CFOPs --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="cfopinterno" class="form-label">CFOP Interno (Dentro do Estado)</label>
            <select class="form-select select2-basic" name="cfopinterno" id="cfopinterno" required>
                <option value="" disabled>Selecione...</option>
                @foreach ($cfops as $cfop)
                    <option value="{{ $cfop->cfop }}" @if (old('cfopinterno', $produto->cfopinterno) == $cfop->cfop) selected @endif>{{ $cfop->cfop }} - {{ $cfop->natureza }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">Informe um CFOP interno.</div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="cfopexterno" class="form-label">CFOP Externo (Fora do Estado)</label>
            <select class="form-select select2-basic" name="cfopexterno" id="cfopexterno" required>
                <option value="" disabled>Selecione...</option>
                @foreach ($cfops as $cfop)
                    <option value="{{ $cfop->cfop }}" @if (old('cfopexterno', $produto->cfopexterno) == $cfop->cfop) selected @endif>{{ $cfop->cfop }} - {{ $cfop->natureza }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">Informe um CFOP externo.</div>
        </div>
    </div>

    {{-- BLOCO 2: TRIBUTAÇÃO CONDICIONAL --}}
    <div class="row">
        @if(in_array($empresa->crt, [1, 4]))
            {{-- EXIBE APENAS CSOSN (Simples/MEI) --}}
            <div class="col-md-12 mb-3">
                <label for="cst_csosn" class="form-label">CST/CSOSN (Simples Nacional)</label>
                <select class="form-select select2-basic" name="cst_csosn" id="cst_csosn" required>
                    <option value="" disabled>Selecione...</option>
                    <option value="101" @if (old('cst_csosn', $produto->cst_csosn) == '101') selected @endif>101 - Tributada pelo Simples Nacional com permissão de crédito</option>
                    <option value="102" @if (old('cst_csosn', $produto->cst_csosn) == '102') selected @endif>102 - Tributada pelo Simples Nacional sem permissão de crédito</option>
                    <option value="103" @if (old('cst_csosn', $produto->cst_csosn) == '103') selected @endif>103 - Isenção do ICMS no Simples Nacional para faixa de receita bruta</option>
                    <option value="201" @if (old('cst_csosn', $produto->cst_csosn) == '201') selected @endif>201 - Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                    <option value="202" @if (old('cst_csosn', $produto->cst_csosn) == '202') selected @endif>202 - Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                    <option value="203" @if (old('cst_csosn', $produto->cst_csosn) == '203') selected @endif>203 - Isenção do ICMS no Simples Nacional para faixa de receita bruta e com cobrança do ICMS por substituição tributária</option>
                    <option value="300" @if (old('cst_csosn', $produto->cst_csosn) == '300') selected @endif>300 - Imune</option>
                    <option value="400" @if (old('cst_csosn', $produto->cst_csosn) == '400') selected @endif>400 - Não tributada pelo Simples Nacional</option>
                    <option value="500" @if (old('cst_csosn', $produto->cst_csosn) == '500') selected @endif>500 - ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação</option>
                    <option value="900" @if (old('cst_csosn', $produto->cst_csosn) == '900') selected @endif>900 - Outros</option>
                </select>
                <div class="invalid-feedback">Informe um CST/CSOSN.</div>
            </div>
            <input type="hidden" name="cst" value="90">
        @else
            {{-- EXIBE APENAS CST (Regime Normal) --}}
            <div class="col-md-12 mb-3">
                <label for="cst" class="form-label">CST (ICMS)</label>
                <select class="form-select select2-basic" name="cst" id="cst" required>
                    <option value="" disabled>Selecione...</option>
                    <option value="00" @if (old('cst', $produto->cst) == '00') selected @endif>00 - Tributação integral</option>
                    <option value="10" @if (old('cst', $produto->cst) == '10') selected @endif>10 - Tributação com ICMS e acréscimo de ST</option>
                    <option value="20" @if (old('cst', $produto->cst) == '20') selected @endif>20 - Tributação com ICMS e acréscimo de ST com direito a crédito</option>
                    <option value="30" @if (old('cst', $produto->cst) == '30') selected @endif>30 - Tributação simplificada (sem direito a crédito)</option>
                    <option value="40" @if (old('cst', $produto->cst) == '40') selected @endif>40 - Tributação simplificada com acréscimo de ST</option>
                    <option value="41" @if (old('cst', $produto->cst) == '41') selected @endif>41 - Tributação com ICMS e acréscimo de ST por Substituição Tributária</option>
                    <option value="50" @if (old('cst', $produto->cst) == '50') selected @endif>50 - Tributação com ICMS e acréscimo de ST por Substituição Tributária com direito a crédito</option>
                    <option value="51" @if (old('cst', $produto->cst) == '51') selected @endif>51 - Tributação com ICMS e acréscimo de ST por Substituição Tributária sem direito a crédito</option>
                    <option value="60" @if (old('cst', $produto->cst) == '60') selected @endif>60 - Tributação com ICMS e acréscimo de ST por Substituição Tributária com acréscimo</option>
                    <option value="70" @if (old('cst', $produto->cst) == '70') selected @endif>70 - Redução de base de cálculo e cobrança do ICMS por substituição tributária</option>
                    <option value="90" @if (old('cst', $produto->cst) == '90') selected @endif>90 - Outras operações</option>
                </select>
                <div class="invalid-feedback">Informe um CST.</div>
            </div>
            <input type="hidden" name="cst_csosn" value="900">
        @endif
    </div>

    {{-- BLOCO 3: PIS / COFINS --}}
    <div class="row">
        <div class="col-12 mb-3">
            <label for="cst_pis" class="form-label">CST/PIS</label>
            <select class="form-select select2-basic" name="cst_pis" id="cst_pis" required>
                <option value="" disabled>Selecione...</option>
                <option value="1" @if (old('cst_pis', $produto->cst_pis) == '1') selected @endif>01 - Operação Tributável com Alíquota Básica</option>
                <option value="49" @if (old('cst_pis', $produto->cst_pis) == '49') selected @endif>49 - Outras Operações de Saída</option>
                <option value="99" @if (old('cst_pis', $produto->cst_pis) == '99') selected @endif>99 - Outras Operações</option>
            </select>
            <div class="invalid-feedback">Informe um CST/PIS.</div>
        </div>
    </div>
    <div class="row">
         <div class="col-12 mb-3">
            <label for="cst_cofins" class="form-label">CST/COFINS</label>
            <select class="form-select select2-basic" name="cst_cofins" id="cst_cofins" required>
                 <option value="" disabled>Selecione...</option>
                 <option value="1" @if (old('cst_cofins', $produto->cst_cofins) == '1') selected @endif>01 - Operação Tributável com Alíquota Básica</option>
                 <option value="49" @if (old('cst_cofins', $produto->cst_cofins) == '49') selected @endif>49 - Outras Operações de Saída</option>
                 <option value="99" @if (old('cst_cofins', $produto->cst_cofins) == '99') selected @endif>99 - Outras Operações</option>
            </select>
            <div class="invalid-feedback">Informe um CST/COFINS.</div>
        </div>
    </div>

    {{-- BLOCO 4: ALÍQUOTAS --}}
    <div class="row">
        <div class="col-md-3 mb-3">
            <label for="icms" class="form-label">ICMS (%)</label>
            <input class="form-control" type="text" name="icms" id="icms" value="{{ old('icms', $produto->icms) }}" required placeholder="0.00">
            <div class="invalid-feedback">Informe um ICMS válido.</div>
        </div>
        <div class="col-md-3 mb-3">
            <label for="pis" class="form-label">PIS (%)</label>
            <input class="form-control" type="text" name="pis" id="pis" value="{{ old('pis', $produto->pis) }}" required placeholder="0.00">
            <div class="invalid-feedback">Informe um PIS válido.</div>
        </div>
        <div class="col-md-3 mb-3">
            <label for="cofins" class="form-label">COFINS (%)</label>
            <input class="form-control" type="text" name="cofins" id="cofins" value="{{ old('cofins', $produto->cofins) }}" required placeholder="0.00">
            <div class="invalid-feedback">Informe um COFINS válido.</div>
        </div>
        <div class="col-md-3 mb-3">
            <label for="ipi" class="form-label">IPI (%)</label>
            <input type="text" class="form-control" name="ipi" id="ipi" value="{{ old('ipi', $produto->ipi) }}" required placeholder="0.00">
            <div class="invalid-feedback">Informe um IPI válido.</div>
        </div>
    </div>

    {{-- BLOCO 5: REFORMA TRIBUTÁRIA --}}
    <div class="rtc-header mt-4">
        <i class="fas fa-balance-scale mr-2"></i> Reforma Tributária (IBS / CBS - A partir de 2026)
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="cst_ibs_cbs" class="form-label">CST IBS/CBS</label>
            <select class="form-select select2-basic" name="cst_ibs_cbs" id="cst_ibs_cbs" onchange="limparCClass()">
                <option value="" disabled selected>Selecione...</option>
                @foreach ($csts as $cst)
                    <option value="{{ $cst->codigo }}" @if (old('cst_ibs_cbs', $produto->cst_ibs_cbs) == $cst->codigo) selected @endif>
                        {{ $cst->codigo }} - {{ Str::limit($cst->descricao, 80) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="cClassTrib" class="form-label">Classificação Tributária (cClassTrib)</label>
            <div class="input-group">
                <input type="text" class="form-control" name="cClassTrib" id="cClassTrib" 
                       value="{{ old('cClassTrib', $produto->cClassTrib) }}" placeholder="Selecione um CST primeiro..." required readonly>
                <div class="input-group-append">
                    <button title="Buscar Classificação" class="btn btn-primary custom-btn-primary" type="button" 
                            onclick="abrirModalCClass()">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="invalid-feedback">O código cClassTrib é obrigatório.</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="pIBS" class="form-label">Alíquota IBS (%)</label>
            <input class="form-control" type="number" step="0.01" name="pIBS" id="pIBS" value="{{ old('pIBS', $produto->pIBS) }}" placeholder="Ex: 12.00">
        </div>
        <div class="col-md-4 mb-3">
            <label for="pCBS" class="form-label">Alíquota CBS (%)</label>
            <input class="form-control" type="number" step="0.01" name="pCBS" id="pCBS" value="{{ old('pCBS', $produto->pCBS) }}" placeholder="Ex: 8.00">
        </div>
        <div class="col-md-4 mb-3">
            <label for="pIS_imposto" class="form-label">Alíq. Imposto Seletivo (%)</label>
            <input class="form-control" type="number" step="0.01" name="pIS_imposto" id="pIS_imposto" value="{{ old('pIS_imposto', $produto->pIS_imposto) }}" placeholder="Se houver incidência">
        </div>
    </div>
</div>

                    {{-- ABA 3: INFORMAÇÕES DO VEÍCULO (AGORA COMPLETO) --}}
                    @if ($produto->tpProd == 1)
                        <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="tpVeic" class="form-label">Tipo de Veículo</label>
                                    <select class="form-select select2-basic" name="tpVeic" id="tpVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="02" @if ($produto->tpVeic == '02') selected @endif>02 - CICLOMOTOR</option>
                                        <option value="03" @if ($produto->tpVeic == '03') selected @endif>03 - MOTONETA</option>
                                        <option value="04" @if ($produto->tpVeic == '04') selected @endif>04 - MOTOCICLO</option>
                                        <option value="05" @if ($produto->tpVeic == '05') selected @endif>05 - TRICICLO</option>
                                        <option value="06" @if ($produto->tpVeic == '06') selected @endif>06 - AUTOMÓVEL</option>
                                        <option value="07" @if ($produto->tpVeic == '07') selected @endif>07 - MICROÔNIBUS</option>
                                        <option value="08" @if ($produto->tpVeic == '08') selected @endif>08 - ÔNIBUS</option>
                                        <option value="10" @if ($produto->tpVeic == '10') selected @endif>10 - REBOQUE</option>
                                        <option value="11" @if ($produto->tpVeic == '11') selected @endif>11 - SEMIREBOQUE</option>
                                        <option value="13" @if ($produto->tpVeic == '13') selected @endif>13 - CAMINHONETA</option>
                                        <option value="14" @if ($produto->tpVeic == '14') selected @endif>14 - CAMINHÃO</option>
                                        <option value="17" @if ($produto->tpVeic == '17') selected @endif>17 - C.TRATOR</option>
                                        <option value="22" @if ($produto->tpVeic == '22') selected @endif>22 - ESP/ÔNIBUS</option>
                                        <option value="23" @if ($produto->tpVeic == '23') selected @endif>23 - MISTO/CAM</option>
                                        <option value="24" @if ($produto->tpVeic == '24') selected @endif>24 - CARGA/CAM</option>
                                    </select>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="chassiVeic" class="form-label">Chassi</label>
                                    <input type="text" class="form-control" id="chassiVeic" name="chassiVeic" value="{{ $produto->chassiVeic }}" placeholder="Chassi do veículo" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="renavanVeic" class="form-label">Renavam</label>
                                    <input type="text" class="form-control" id="renavanVeic" name="renavanVeic" value="{{ $produto->renavanVeic }}" placeholder="Número do Renavam">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="anoFabVeic" class="form-label">Ano de Fabricação</label>
                                    <input type="number" class="form-control" id="anoFabVeic" name="anoFabVeic" value="{{ $produto->anoFabVeic }}" placeholder="Ano de Fabricação">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="anoModVeic" class="form-label">Ano do Modelo</label>
                                    <input type="number" class="form-control" id="anoModVeic" name="anoModVeic" value="{{ $produto->anoModVeic }}" placeholder="Ano do Modelo">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="pesoLVeic" class="form-label">Peso Líquido (kg)</label>
                                    <input type="number" class="form-control" step="0.01" id="pesoLVeic" name="pesoLVeic" value="{{ $produto->pesoLVeic }}" placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="pesoBVeic" class="form-label">Peso Bruto (kg)</label>
                                    <input type="number" class="form-control" id="pesoBVeic" name="pesoBVeic" value="{{ $produto->pesoBVeic }}" placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="distVeic" class="form-label">Distância entre Eixos (mm)</label>
                                    <input type="text" class="form-control" id="distVeic" name="distVeic" value="{{ $produto->distVeic }}" placeholder="0000">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label for="combVeic" class="form-label">Combustível</label>
                                    <select class="form-select select2-basic" name="combVeic" id="combVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="01" @if ($produto->combVeic == '01') selected @endif>01 - ÁLCOOL</option>
                                        <option value="02" @if ($produto->combVeic == '02') selected @endif>02 - GASOLINA</option>
                                        <option value="03" @if ($produto->combVeic == '03') selected @endif>03 - DIESEL</option>
                                        <option value="16" @if ($produto->combVeic == '16') selected @endif>16 - ÁLCOOL/GASOLINA</option>
                                        <option value="17" @if ($produto->combVeic == '17') selected @endif>17 - GASOLINA/ÁLCOOL/GNV</option>
                                        <option value="18" @if ($produto->combVeic == '18') selected @endif>18 - GASOLINA/ELÉTRICO</option>
                                    </select>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <label for="nMotorVeic" class="form-label">Nº do Motor</label>
                                    <input type="text" class="form-control" id="nMotorVeic" name="nMotorVeic" value="{{ $produto->nMotorVeic }}" placeholder="Número do Motor" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="cvVeic" class="form-label">Potência (CV)</label>
                                    <input type="number" step="0.01" class="form-control" id="cvVeic" name="cvVeic" value="{{ $produto->cvVeic }}" placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="cm3Veic" class="form-label">Cilindradas (cm³)</label>
                                    <input type="number" step="0.01" class="form-control" id="cm3Veic" name="cm3Veic" value="{{ $produto->cm3Veic }}" placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="serieVeic" class="form-label">Série</label>
                                    <input type="text" class="form-control" id="serieVeic" name="serieVeic" value="{{ $produto->serieVeic }}" placeholder="Número de Série">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="tpPVeic" class="form-label">Tipo de Pintura</label>
                                    <input type="text" class="form-control" id="tpPVeic" name="tpPVeic" value="{{ $produto->tpPVeic }}" placeholder="Ex: Metálica">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="corVeic" class="form-label">Cor</label>
                                    <input type="text" class="form-control" id="corVeic" name="corVeic" value="{{ $produto->corVeic }}" placeholder="Ex: PRETO" oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="cCorVeic" class="form-label">Código da Cor (DENATRAN)</label>
                                    <select class="form-select select2-basic" name="cCorVeic" id="cCorVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="01" @if ($produto->cCorVeic == '01') selected @endif>01 - AMARELO</option>
                                        <option value="02" @if ($produto->cCorVeic == '02') selected @endif>02 - AZUL</option>
                                        <option value="03" @if ($produto->cCorVeic == '03') selected @endif>03 - BEGE</option>
                                        <option value="04" @if ($produto->cCorVeic == '04') selected @endif>04 - BRANCA</option>
                                        <option value="05" @if ($produto->cCorVeic == '05') selected @endif>05 - CINZA</option>
                                        <option value="06" @if ($produto->cCorVeic == '06') selected @endif>06 - DOURADA</option>
                                        <option value="07" @if ($produto->cCorVeic == '07') selected @endif>07 - GRENAR</option>
                                        <option value="08" @if ($produto->cCorVeic == '08') selected @endif>08 - LARANJA</option>
                                        <option value="09" @if ($produto->cCorVeic == '09') selected @endif>09 - MARROM</option>
                                        <option value="10" @if ($produto->cCorVeic == '10') selected @endif>10 - PRATA</option>
                                        <option value="11" @if ($produto->cCorVeic == '11') selected @endif>11 - PRETA</option>
                                        <option value="12" @if ($produto->cCorVeic == '12') selected @endif>12 - ROSA</option>
                                        <option value="13" @if ($produto->cCorVeic == '13') selected @endif>13 - ROXA</option>
                                        <option value="14" @if ($produto->cCorVeic == '14') selected @endif>14 - VERDE</option>
                                        <option value="15" @if ($produto->cCorVeic == '15') selected @endif>15 - VERMELHA</option>
                                        <option value="16" @if ($produto->cCorVeic == '16') selected @endif>16 - FANTASIA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="cCorMontVeic" class="form-label">Cód. Cor Montadora</label>
                                    <input type="text" class="form-control" id="cCorMontVeic" name="cCorMontVeic" value="{{ $produto->cCorMontVeic }}" placeholder="Código da Montadora">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="cMarcaVeic" class="form-label">Código da Marca</label>
                                    <input type="text" class="form-control" id="cMarcaVeic" name="cMarcaVeic" value="{{ $produto->cMarcaVeic }}" placeholder="Código da Marca/Modelo">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="condVeic" class="form-label">Condição do Veículo</label>
                                    <select class="form-select select2-basic" name="condVeic" id="condVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="1" @if ($produto->condVeic == '1') selected @endif>ACABADO</option>
                                        <option value="2" @if ($produto->condVeic == '2') selected @endif>INACABADO</option>
                                        <option value="3" @if ($produto->condVeic == '3') selected @endif>SEMIACABO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="espVeic" class="form-label">Espécie do Veículo</label>
                                    <select class="form-select select2-basic" name="espVeic" id="espVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="1" @if ($produto->espVeic == '1') selected @endif>PASSAGEIRO</option>
                                        <option value="2" @if ($produto->espVeic == '2') selected @endif>CARGA</option>
                                        <option value="3" @if ($produto->espVeic == '3') selected @endif>MISTO</option>
                                        <option value="4" @if ($produto->espVeic == '4') selected @endif>CORRIDA</option>
                                        <option value="5" @if ($produto->espVeic == '5') selected @endif>TRAÇÃO</option>
                                        <option value="6" @if ($produto->espVeic == '6') selected @endif>ESPECIAL</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="vinVeic" class="form-label">Condição do Chassi (VIN)</label>
                                    <select class="form-select select2-basic" name="vinVeic" id="vinVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="N" @if ($produto->vinVeic == 'N') selected @endif>NORMAL</option>
                                        <option value="R" @if ($produto->vinVeic == 'R') selected @endif>REMARCADO</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="lotVeic" class="form-label">Lotação Máxima</label>
                                    <input type="text" class="form-control" id="lotVeic" name="lotVeic" value="{{ $produto->lotVeic }}" placeholder="Nº de Pessoas">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label for="restriVeic" class="form-label">Restrição</label>
                                    <select class="form-select select2-basic" name="restriVeic" id="restriVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="0" @if ($produto->restriVeic == '0') selected @endif>NÃO HÁ</option>
                                        <option value="1" @if ($produto->restriVeic == '1') selected @endif>ALIENAÇÃO FIDUNCIÁRIA</option>
                                        <option value="2" @if ($produto->restriVeic == '2') selected @endif>ARRENDAMENTO MERCANTIL</option>
                                        <option value="3" @if ($produto->restriVeic == '3') selected @endif>RESERVA DE DOMÍNIO</option>
                                        <option value="4" @if ($produto->restriVeic == '4') selected @endif>PENHOR DE VEÍCULOS</option>
                                        <option value="9" @if ($produto->restriVeic == '9') selected @endif>OUTRAS</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="cargaVeic" class="form-label">Carga Máxima (kg)</label>
                                    <input type="number" class="form-control" id="cargaVeic" name="cargaVeic" value="{{ $produto->cargaVeic }}" placeholder="0">
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="operVeic" class="form-label">Tipo de Operação</label>
                                    <select class="form-select select2-basic" name="operVeic" id="operVeic">
                                        <option value="" disabled selected>Selecione...</option>
                                        <option value="1" @if ($produto->operVeic == '1') selected @endif>VENDA CONCESSIONÁRIA</option>
                                        <option value="2" @if ($produto->operVeic == '2') selected @endif>FATURAMENTO DIRETO PARA CONSUMIDOR FINAL</option>
                                        <option value="3" @if ($produto->operVeic == '3') selected @endif>VENDA DIRETO PARA GRANDES CONSUMIDORES</option>
                                        <option value="0" @if ($produto->operVeic == '0') selected @endif>OUTRAS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-right">
                        <button type="submit" class="btn custom-btn-primary">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL NCM --}}
    @component('components.modal', ['modalId' => 'NcmModal', 'modalTitle' => 'Selecione o NCM', 'sizeModal' => 'modal-lg'])
        @component('components.dataTable', ['responsive' => true, 'searching' => true, 'lengthChange' => true, 'pageLength' => 5, 'ordering' => true, 'showFooter' => false])
            <thead class="table-primary">
                <tr>
                    <th>NCM</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ncms as $ncm)
                    <tr style="cursor: pointer;" ondblclick="setaNcm('{{ $ncm->ncm }}')">
                        <td>{{ $ncm->ncm }}</td>
                        <td>{{ $ncm->descricao }}</td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    @endcomponent

    {{-- MODAL CCLASSTRIB (NOVO) --}}
    @component('components.modal', ['modalId' => 'CClassTribModal', 'modalTitle' => 'Selecione a Classificação Tributária', 'sizeModal' => 'modal-lg'])
        <div class="row mb-2">
            <div class="col-12">
                <input type="text" id="searchCClass" class="form-control" placeholder="Filtrar resultados..." onkeyup="filtrarTabelaModal()">
            </div>
        </div>
        <div style="max-height: 400px; overflow-y: auto;">
            <div id="loading-cclass" style="display:none; text-align:center; padding: 20px;">
                <i class="fas fa-spinner fa-spin fa-2x"></i><br>Carregando classificações...
            </div>
            <table class="table table-hover table-striped" id="tableCClass">
                <thead class="table-primary">
                    <tr>
                        <th style="width: 100px;">Código</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody id="tbodyCClass">
                    {{-- Linhas injetadas via JS --}}
                </tbody>
            </table>
        </div>
    @endcomponent
@stop

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa o Select2
            $('.select2-basic').select2({
                placeholder: "Selecione uma opção",
                allowClear: true,
                width: '100%'
            });
        });
        
        // Validação de formulário Bootstrap
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();

        function setaNcm(ncm) {
            document.getElementById('ncm').value = ncm;
            $('#NcmModal').modal('hide');
        }

        // --- FUNÇÕES DA REFORMA TRIBUTÁRIA ---

        function limparCClass() {
            document.getElementById('cClassTrib').value = '';
        }

        function abrirModalCClass() {
            const cst = document.getElementById('cst_ibs_cbs').value;
            
            if (!cst) {
                alert('Por favor, selecione primeiro o CST IBS/CBS.');
                // Tenta abrir o select2 se estiver inicializado
                $('#cst_ibs_cbs').select2('open'); 
                return;
            }

            // Abre o modal
            $('#CClassTribModal').modal('show');
            
            // Mostra loading e limpa tabela
            const tbody = document.getElementById('tbodyCClass');
            const loading = document.getElementById('loading-cclass');
            tbody.innerHTML = '';
            loading.style.display = 'block';

            // Chama a API
            fetch(`/api/cclasstrib/${cst}`)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="2" class="text-center">Nenhuma classificação encontrada para este CST.</td></tr>';
                        return;
                    }

                    // Popula a tabela
                    let html = '';
                    data.forEach(item => {
                        html += `
                            <tr style="cursor: pointer;" onclick="selecionarCClass('${item.codigo}')">
                                <td><b>${item.codigo}</b></td>
                                <td>${item.descricao}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    loading.style.display = 'none';
                    tbody.innerHTML = '<tr><td colspan="2" class="text-danger text-center">Erro ao buscar dados. Tente novamente.</td></tr>';
                });
        }

        function selecionarCClass(codigo) {
            document.getElementById('cClassTrib').value = codigo;
            $('#CClassTribModal').modal('hide');
        }

        function filtrarTabelaModal() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchCClass");
            filter = input.value.toUpperCase();
            table = document.getElementById("tableCClass");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                tdDesc = tr[i].getElementsByTagName("td")[1];
                tdCod = tr[i].getElementsByTagName("td")[0];
                if (tdDesc || tdCod) {
                    txtValueDesc = tdDesc.textContent || tdDesc.innerText;
                    txtValueCod = tdCod.textContent || tdCod.innerText;
                    if (txtValueDesc.toUpperCase().indexOf(filter) > -1 || txtValueCod.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }
    </script>
@endpush