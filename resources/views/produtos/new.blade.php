@extends('adminlte::page')

@section('title', 'Cadastro de Produto')

@push('css')
<style>
    /* Estilos importados para consistência */
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
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    .custom-btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color);
        color: #fff !important;
        font-weight: 500;
        border-radius: 8px;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-primary:hover:not(:disabled) {
        background-color: #00045e !important;
        border-color: #00045e !important;
        transform: translateY(-2px);
    }
    .custom-btn-primary:disabled {
        background-color: #a0a1b8 !important;
        border-color: #a0a1b8 !important;
        cursor: not-allowed !important;
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
    .modal-content {
        border-radius: 15px;
    }
    
    /* Estilos customizados para o Select2 */
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
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        line-height: normal !important;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Cadastro de Produto</h1>
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
                <li class="nav-item" style="display: none" id="veicTab">
                    <a class="nav-link" id="veic-tab" data-toggle="pill" href="#veic" role="tab" aria-controls="veic"
                        aria-selected="false"><b>Informações de Veículo</b></a>
                </li>
            </ul>
            <form class="needs-validation mt-4" novalidate method="POST" action="{{ route('salvar_produto') }}" id="main-form">
                @csrf
                <div class="tab-content" id="tabContent">
                    {{-- ABA 1: INFORMAÇÕES DO PRODUTO --}}
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">
                            @if ($user->empresa_id == 1)
                                <div class="col-md-6 mb-3">
                                    <label for="empresa" class="form-label">Empresa</label>
                                    <select onchange="liberarProdutos()" class="form-select select2-basic" name="empresa" id="empresa" required>
                                        <option value="" disabled selected>Selecione uma Empresa</option>
                                        @foreach ($empresas as $emp)
                                            <option value="{{ $emp->id }}" @if (old('empresa') == $emp->id) selected @endif>{{ $emp->fantasia }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Informe uma empresa.</div>
                                </div>
                            @else
                                <input type="hidden" name="empresa" id="empresa" value="{{ $user->empresa_id }}">
                            @endif
                            <div class="col-md-6 mb-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select class="form-select select2-basic" name="categoria" id="categoria" required {{ $user->empresa_id != 1 ? '' : 'disabled' }}>
                                    <option value="" disabled selected>Selecione uma Categoria</option>
                                    @foreach ($categorias as $categoria)
                                        <option class="categoria-option" data-empresa="{{ $categoria->empresa_id }}" value="{{ $categoria->id }}" @if (old('categoria') == $categoria->id) selected @endif>{{ $categoria->descricao }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Informe uma categoria.</div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-9 mb-3">
                                <label for="produto" class="form-label">Nome do Produto</label>
                                <input class="form-control" type="text" name="produto" id="produto" required placeholder="Ex: REFRIGERANTE COCA-COLA 2L" oninput="this.value = this.value.toUpperCase()" value="{{ old('produto') }}">
                                <div class="invalid-feedback">Informe um nome de produto válido.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="codigo" class="form-label">Cód. de Barras (EAN)</label>
                                <input class="form-control" type="text" name="codigo" id="codigo" placeholder="SEM GTIN" oninput="this.value = this.value.toUpperCase()" value="{{ old('codigo') }}" required>
                                <small class="font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Caso o produto não tenha código de barras, por favor inserir 'SEM GTIN' no campo acima</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="ncm" class="form-label">NCM</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" name="ncm" id="ncm" required placeholder="Nomenclatura Comum do Mercosul" value="{{ old('ncm') }}">
                                    <button title="Buscar Ncm" class="btn btn-dark" type="button" data-toggle="modal" data-target="#NcmModal" style="border-radius: 0 8px 8px 0;"><i class="fa fa-search"></i></button>
                                    <div class="invalid-feedback">Informe um NCM válido.</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="precocusto" class="form-label">Preço de Custo</label>
                                <input class="form-control" type="number" name="precocusto" id="precocusto" step="0.01" required placeholder="R$ 0,00" value="{{ old('precocusto') }}">
                                <div class="invalid-feedback">Informe um preço de custo válido.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="precovenda" class="form-label">Preço de Venda</label>
                                <input class="form-control" type="number" name="precovenda" step="0.01" id="precovenda" required placeholder="R$ 0,00" value="{{ old('precovenda') }}">
                                <div class="invalid-feedback">Informe um preço de venda válido.</div>
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="un" class="form-label">Unidade de Medida</label>
                                <select name="un" id="un" class="form-select select2-basic" required>
                                    <option value="" disabled selected>Selecione</option>
                                    <option value="un" @if (old('un') == 'un') selected @endif>UN</option>
                                    <option value="cx" @if (old('un') == 'cx') selected @endif>CX</option>
                                    <option value="kg" @if (old('un') == 'kg') selected @endif>KG</option>
                                    <option value="l" @if (old('un') == 'l') selected @endif>L</option>
                                    <option value="ml" @if (old('un') == 'ml') selected @endif>ML</option>
                                    <option value="m" @if (old('un') == 'm') selected @endif>M</option>
                                    <option value="cm" @if (old('un') == 'cm') selected @endif>CM</option>
                                    <option value="fd" @if (old('un') == 'fd') selected @endif>FD</option>
                                    <option value="mil" @if (old('un') == 'mil') selected @endif>MIL</option>
                                    <option value="sc" @if (old('un') == 'sc') selected @endif>SC</option>
                                </select>
                                <div class="invalid-feedback">Informe uma unidade válida.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="estoque" class="form-label">Estoque Inicial</label>
                                <input class="form-control" type="number" name="estoque" id="estoque" placeholder="Qtd." value="{{ old('estoque') ?? 1 }}">
                                <div class="invalid-feedback">Informe um estoque válido.</div>
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="tpProd" id="tpProd" @if (old('tpProd')) checked @endif>
                                    <label class="form-check-label text-bold" for="tpProd">É Veículo?</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ABA 2: INFORMAÇÕES FISCAIS --}}
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cfopinterno" class="form-label">CFOP Interno (Dentro do Estado)</label>
                                <select class="form-select select2-basic" name="cfopinterno" id="cfopinterno" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    @foreach ($cfops as $cfop)
                                        <option value="{{ $cfop->cfop }}" @if (old('cfopinterno') == $cfop->cfop) selected @endif>{{ $cfop->cfop }} - {{ $cfop->natureza }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Informe um CFOP interno.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cfopexterno" class="form-label">CFOP Externo (Fora do Estado)</label>
                                <select class="form-select select2-basic" name="cfopexterno" id="cfopexterno" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    @foreach ($cfops as $cfop)
                                        <option value="{{ $cfop->cfop }}" @if (old('cfopexterno') == $cfop->cfop) selected @endif>{{ $cfop->cfop }} - {{ $cfop->natureza }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Informe um CFOP externo.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cst" class="form-label">CST</label>
                                <select class="form-select select2-basic" name="cst" id="cst" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="00" @if (old('cst') == '00') selected @endif>00 - Tributação integral</option>
                                    <option value="10" @if (old('cst') == '10') selected @endif>10 - Tributação com ICMS e acréscimo de ST</option>
                                    <option value="20" @if (old('cst') == '20') selected @endif>20 - Tributação com ICMS e acréscimo de ST com direito a crédito</option>
                                    <option value="30" @if (old('cst') == '30') selected @endif>30 - Tributação simplificada (sem direito a crédito)</option>
                                    <option value="40" @if (old('cst') == '40') selected @endif>40 - Tributação simplificada com acréscimo de ST</option>
                                    <option value="41" @if (old('cst') == '41') selected @endif>41 - Tributação com ICMS e acréscimo de ST por Substituição Tributária</option>
                                    <option value="50" @if (old('cst') == '50') selected @endif>50 - Tributação com ICMS e acréscimo de ST por Substituição Tributária com direito a crédito</option>
                                    <option value="51" @if (old('cst') == '51') selected @endif>51 - Tributação com ICMS e acréscimo de ST por Substituição Tributária sem direito a crédito</option>
                                    <option value="60" @if (old('cst') == '60') selected @endif>60 - Tributação com ICMS e acréscimo de ST por Substituição Tributária com acréscimo</option>
                                    <option value="70" @if (old('cst') == '70') selected @endif>70 - Redução de base de cálculo e cobrança do ICMS por substituição tributária</option>
                                    <option value="90" @if (old('cst') == '90') selected @endif>90 - Outras operações</option>
                                </select>
                                <div class="invalid-feedback">Informe um CST.</div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="cst_csosn" class="form-label">CST/CSOSN</label>
                                <select class="form-select select2-basic" name="cst_csosn" id="cst_csosn" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="101" @if (old('cst_csosn') == '101') selected @endif>101 - Tributada pelo Simples Nacional com permissão de crédito</option>
                                    <option value="102" @if (old('cst_csosn') == '102') selected @endif>102 - Tributada pelo Simples Nacional sem permissão de crédito</option>
                                    <option value="103" @if (old('cst_csosn') == '103') selected @endif>103 - Isenção do ICMS no Simples Nacional para faixa de receita bruta</option>
                                    <option value="201" @if (old('cst_csosn') == '201') selected @endif>201 - Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                                    <option value="202" @if (old('cst_csosn') == '202') selected @endif>202 - Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                                    <option value="203" @if (old('cst_csosn') == '203') selected @endif>203 - Isenção do ICMS no Simples Nacional para faixa de receita bruta e com cobrança do ICMS por substituição tributária</option>
                                    <option value="300" @if (old('cst_csosn') == '300') selected @endif>300 - Imune</option>
                                    <option value="400" @if (old('cst_csosn') == '400') selected @endif>400 - Não tributada pelo Simples Nacional</option>
                                    <option value="500" @if (old('cst_csosn') == '500') selected @endif>500 - ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação</option>
                                    <option value="900" @if (old('cst_csosn') == '900') selected @endif>900 - Outros</option>
                                </select>
                                <div class="invalid-feedback">Informe um CST/CSOSN.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="cst_pis" class="form-label">CST/PIS</label>
                                <select class="form-select select2-basic" name="cst_pis" id="cst_pis" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="1" @if (old('cst_pis') == '1') selected @endif>1 - Operação Tributável com Alíquota Básica</option>
                                    <option value="2" @if (old('cst_pis') == '2') selected @endif>2 - Operação Tributável com Alíquota Diferenciada</option>
                                    <option value="3" @if (old('cst_pis') == '3') selected @endif>3 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                    <option value="4" @if (old('cst_pis') == '4') selected @endif>4 - Operação Tributável Monofásica – Revenda a Alíquota Zero</option>
                                    <option value="5" @if (old('cst_pis') == '5') selected @endif>5 - Operação Tributável por Substituição Tributária</option>
                                    <option value="6" @if (old('cst_pis') == '6') selected @endif>6 - Operação Tributável a Alíquota Zero</option>
                                    <option value="7" @if (old('cst_pis') == '7') selected @endif>7 - Operação Isenta da Contribuição</option>
                                    <option value="8" @if (old('cst_pis') == '8') selected @endif>8 - Operação sem Incidência da Contribuição</option>
                                    <option value="9" @if (old('cst_pis') == '9') selected @endif>9 - Operação com Suspensão da Contribuição</option>
                                    <option value="49" @if (old('cst_pis') == '49') selected @endif>49 - Outras Operações de Saída</option>
                                    <option value="50" @if (old('cst_pis') == '50') selected @endif>50 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                    <option value="51" @if (old('cst_pis') == '51') selected @endif>51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                    <option value="52" @if (old('cst_pis') == '52') selected @endif>52 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita de Exportação</option>
                                    <option value="53" @if (old('cst_pis') == '53') selected @endif>53 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                    <option value="54" @if (old('cst_pis') == '54') selected @endif>54 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                    <option value="55" @if (old('cst_pis') == '55') selected @endif>55 - Operação com Direito a Crédito – Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                    <option value="56" @if (old('cst_pis') == '56') selected @endif>56 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação</option>
                                    <option value="60" @if (old('cst_pis') == '60') selected @endif>60 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                    <option value="61" @if (old('cst_pis') == '61') selected @endif>61 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                    <option value="62" @if (old('cst_pis') == '62') selected @endif>62 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação</option>
                                    <option value="63" @if (old('cst_pis') == '63') selected @endif>63 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                    <option value="64" @if (old('cst_pis') == '64') selected @endif>64 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                    <option value="65" @if (old('cst_pis') == '65') selected @endif>65 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação</option>
                                    <option value="66" @if (old('cst_pis') == '66') selected @endif>66 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and de Exportação</option>
                                    <option value="67" @if (old('cst_pis') == '67') selected @endif>67 - Crédito Presumido – Outras Operações</option>
                                    <option value="70" @if (old('cst_pis') == '70') selected @endif>70 - Operação de Aquisição sem Direito a Crédito</option>
                                    <option value="71" @if (old('cst_pis') == '71') selected @endif>71 - Operação de Aquisição com Isenção</option>
                                    <option value="72" @if (old('cst_pis') == '72') selected @endif>72 - Operação de Aquisição com Suspensão</option>
                                    <option value="73" @if (old('cst_pis') == '73') selected @endif>73 - Operação de Aquisição a Alíquota Zero</option>
                                    <option value="74" @if (old('cst_pis') == '74') selected @endif>74 - Operação de Aquisição sem Incidência da Contribuição</option>
                                    <option value="75" @if (old('cst_pis') == '75') selected @endif>75 - Operação de Aquisição por Substituição Tributária</option>
                                    <option value="98" @if (old('cst_pis') == '98') selected @endif>98 - Outras Operações de Entrada</option>
                                    <option value="99" @if (old('cst_pis') == '99') selected @endif>99 - Outras Operações</option>
                                </select>
                                <div class="invalid-feedback">Informe um CST/PIS.</div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-12 mb-3">
                                <label for="cst_cofins" class="form-label">CST/COFINS</label>
                                <select class="form-select select2-basic" name="cst_cofins" id="cst_cofins" required>
                                     <option value="" disabled selected>Selecione...</option>
                                     <option value="1" @if (old('cst_cofins') == '1') selected @endif>1 - Operação Tributável com Alíquota Básica</option>
                                     <option value="2" @if (old('cst_cofins') == '2') selected @endif>2 - Operação Tributável com Alíquota Diferenciada</option>
                                     <option value="3" @if (old('cst_cofins') == '3') selected @endif>3 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                     <option value="4" @if (old('cst_cofins') == '4') selected @endif>4 - Operação Tributável Monofásica – Revenda a Alíquota Zero</option>
                                     <option value="5" @if (old('cst_cofins') == '5') selected @endif>5 - Operação Tributável por Substituição Tributária</option>
                                     <option value="6" @if (old('cst_cofins') == '6') selected @endif>6 - Operação Tributável a Alíquota Zero</option>
                                     <option value="7" @if (old('cst_cofins') == '7') selected @endif>7 - Operação Isenta da Contribuição</option>
                                     <option value="8" @if (old('cst_cofins') == '8') selected @endif>8 - Operação sem Incidência da Contribuição</option>
                                     <option value="9" @if (old('cst_cofins') == '9') selected @endif>9 - Operação com Suspensão da Contribuição</option>
                                     <option value="49" @if (old('cst_cofins') == '49') selected @endif>49 - Outras Operações de Saída</option>
                                     <option value="50" @if (old('cst_cofins') == '50') selected @endif>50 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                     <option value="51" @if (old('cst_cofins') == '51') selected @endif>51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                     <option value="52" @if (old('cst_cofins') == '52') selected @endif>52 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita de Exportação</option>
                                     <option value="53" @if (old('cst_cofins') == '53') selected @endif>53 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                     <option value="54" @if (old('cst_cofins') == '54') selected @endif>54 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                     <option value="55" @if (old('cst_cofins') == '55') selected @endif>55 - Operação com Direito a Crédito – Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                     <option value="56" @if (old('cst_cofins') == '56') selected @endif>56 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação</option>
                                     <option value="60" @if (old('cst_cofins') == '60') selected @endif>60 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                     <option value="61" @if (old('cst_cofins') == '61') selected @endif>61 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                     <option value="62" @if (old('cst_cofins') == '62') selected @endif>62 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação</option>
                                     <option value="63" @if (old('cst_cofins') == '63') selected @endif>63 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                     <option value="64" @if (old('cst_cofins') == '64') selected @endif>64 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                     <option value="65" @if (old('cst_cofins') == '65') selected @endif>65 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação</option>
                                     <option value="66" @if (old('cst_cofins') == '66') selected @endif>66 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and de Exportação</option>
                                     <option value="67" @if (old('cst_cofins') == '67') selected @endif>67 - Crédito Presumido – Outras Operações</option>
                                     <option value="70" @if (old('cst_cofins') == '70') selected @endif>70 - Operação de Aquisição sem Direito a Crédito</option>
                                     <option value="71" @if (old('cst_cofins') == '71') selected @endif>71 - Operação de Aquisição com Isenção</option>
                                     <option value="72" @if (old('cst_cofins') == '72') selected @endif>72 - Operação de Aquisição com Suspensão</option>
                                     <option value="73" @if (old('cst_cofins') == '73') selected @endif>73 - Operação de Aquisição a Alíquota Zero</option>
                                     <option value="74" @if (old('cst_cofins') == '74') selected @endif>74 - Operação de Aquisição sem Incidência da Contribuição</option>
                                     <option value="75" @if (old('cst_cofins') == '75') selected @endif>75 - Operação de Aquisição por Substituição Tributária</option>
                                     <option value="98" @if (old('cst_cofins') == '98') selected @endif>98 - Outras Operações de Entrada</option>
                                     <option value="99" @if (old('cst_cofins') == '99') selected @endif>99 - Outras Operações</option>
                                </select>
                                <div class="invalid-feedback">Informe um CST/COFINS.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="icms" class="form-label">ICMS (%)</label>
                                <input class="form-control" type="number" name="icms" id="icms" value="{{ old('icms') ?? 20.5 }}" required placeholder="0.00">
                                <div class="invalid-feedback">Informe um ICMS válido.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="pis" class="form-label">PIS (%)</label>
                                <input class="form-control" type="text" name="pis" id="pis" value="{{ old('pis') ?? '00' }}" required placeholder="0.00">
                                <div class="invalid-feedback">Informe um PIS válido.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="cofins" class="form-label">COFINS (%)</label>
                                <input class="form-control" type="text" name="cofins" id="cofins" value="{{ old('cofins') ?? '00' }}" required placeholder="0.00">
                                <div class="invalid-feedback">Informe um COFINS válido.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="ipi" class="form-label">IPI (%)</label>
                                <input type="text" class="form-control" name="ipi" id="ipi" value="{{ old('ipi') ?? '00' }}" required placeholder="0.00">
                                <div class="invalid-feedback">Informe um IPI válido.</div>
                            </div>
                        </div>
                    </div>

                    {{-- ABA 3: INFORMAÇÕES DO VEÍCULO --}}
                    <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tpVeic" class="form-label">Tipo de Veículo</label>
                                <select class="form-select select2-basic" name="tpVeic" id="tpVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="02" @if (old('tpVeic') == '02') selected @endif>02 - CICLOMOTOR</option>
                                    <option value="03" @if (old('tpVeic') == '03') selected @endif>03 - MOTONETA</option>
                                    <option value="04" @if (old('tpVeic') == '04') selected @endif>04 - MOTOCICLO</option>
                                    <option value="05" @if (old('tpVeic') == '05') selected @endif>05 - TRICICLO</option>
                                    <option value="06" @if (old('tpVeic') == '06') selected @endif>06 - AUTOMÓVEL</option>
                                    <option value="07" @if (old('tpVeic') == '07') selected @endif>07 - MICROÔNIBUS</option>
                                    <option value="08" @if (old('tpVeic') == '07') selected @endif>07 - ÔNIBUS</option>
                                    <option value="10" @if (old('tpVeic') == '10') selected @endif>10 - REBOQUE</option>
                                    <option value="11" @if (old('tpVeic') == '11') selected @endif>11 - SEMIREBOQUE</option>
                                    <option value="13" @if (old('tpVeic') == '13') selected @endif>13 - CAMINHONETA</option>
                                    <option value="14" @if (old('tpVeic') == '14') selected @endif>14 - CAMINHÃO</option>
                                    <option value="17" @if (old('tpVeic') == '17') selected @endif>17 - C.TRATOR</option>
                                    <option value="22" @if (old('tpVeic') == '22') selected @endif>22 - ESP/ÔNIBUS</option>
                                    <option value="23" @if (old('tpVeic') == '23') selected @endif>23 - MISTO/CAM</option>
                                    <option value="24" @if (old('tpVeic') == '24') selected @endif>24 - CARGA/CAM</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="chassiVeic" class="form-label">Chassi</label>
                                <input type="text" class="form-control" id="chassiVeic" name="chassiVeic" oninput="this.value = this.value.toUpperCase()" value="{{ old('chassVeic') }}" placeholder="Chassi do veículo">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="renavanVeic" class="form-label">Renavam</label>
                                <input type="text" class="form-control" id="renavanVeic" name="renavanVeic" value="{{ old('renavanVeic') ?? '000000000' }}" placeholder="Número do Renavam">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="anoFabVeic" class="form-label">Ano de Fabricação</label>
                                <input type="number" class="form-control" id="anoFabVeic" name="anoFabVeic" min="1950" max="{{ date('Y') }}" value="{{ old('anoFabVeic') ?? date('Y') }}" placeholder="Ano de Fabricação">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="anoModVeic" class="form-label">Ano do Modelo</label>
                                <input type="number" class="form-control" id="anoModVeic" name="anoModVeic" min="1950" max="{{ date('Y') + 1 }}" value="{{ old('anoModVeic') ?? date('Y') }}" placeholder="Ano do Modelo">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-4 mb-3">
                                <label for="pesoLVeic" class="form-label">Peso Líquido (kg)</label>
                                <input type="number" class="form-control" step="0.01" id="pesoLVeic" name="pesoLVeic" value="{{ old('pesoLVeic') }}" placeholder="0.00">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="pesoBVeic" class="form-label">Peso Bruto (kg)</label>
                                <input type="number" class="form-control" id="pesoBVeic" name="pesoBVeic" value="{{ old('pesoBVeic') }}" placeholder="0.00">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="distVeic" class="form-label">Distância entre Eixos (mm)</label>
                                <input type="text" class="form-control" id="distVeic" name="distVeic" value="{{ old('distVeic') }}" placeholder="0000">
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="combVeic" class="form-label">Combustível</label>
                                <select class="form-select select2-basic" name="combVeic" id="combVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="01" @if (old('combVeic') == '1') selected @endif>01 - ÁLCOOL</option>
                                    <option value="02" @if (old('combVeic') == '2') selected @endif>02 - GASOLINA</option>
                                    <option value="03" @if (old('combVeic') == '3') selected @endif>03 - DIESEL</option>
                                    <option value="16" @if (old('combVeic') == '16') selected @endif>16 - ÁLCOOL/GASOLINA</option>
                                    <option value="17" @if (old('combVeic') == '17') selected @endif>17 - GASOLINA/ÁLCOOL/GNV</option>
                                    <option value="18" @if (old('combVeic') == '18') selected @endif>18 - GASOLINA/ELÉTRICO</option>
                                </select>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label for="nMotorVeic" class="form-label">Nº do Motor</label>
                                <input type="text" class="form-control" id="nMotorVeic" name="nMotorVeic" value="{{ old('nMotorVeic') }}" placeholder="Número do Motor" oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-4 mb-3">
                                <label for="cvVeic" class="form-label">Potência (CV)</label>
                                <input type="number" step="0.01" class="form-control" id="cvVeic" name="cvVeic" value="{{ old('cvVeic') }}" placeholder="0.00">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cm3Veic" class="form-label">Cilindradas (cm³)</label>
                                <input type="number" step="0.01" class="form-control" id="cm3Veic" name="cm3Veic" value="{{ old('cm3Veic') }}" placeholder="0.00">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="serieVeic" class="form-label">Série</label>
                                <input type="text" class="form-control" id="serieVeic" name="serieVeic" value="{{ old('serieVeic') }}" placeholder="Número de Série">
                            </div>
                        </div>
                         <div class="row">
                             <div class="col-md-4 mb-3">
                                <label for="tpPVeic" class="form-label">Tipo de Pintura</label>
                                <input type="text" class="form-control" id="tpPVeic" name="tpPVeic" value="{{ old('tpPVeic') }}" placeholder="Ex: Metálica">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="corVeic" class="form-label">Cor</label>
                                <input type="text" class="form-control" id="corVeic" name="corVeic" oninput="this.value = this.value.toUpperCase()" value="{{ old('corVeic') }}" placeholder="Ex: PRETO">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="cCorVeic" class="form-label">Código da Cor (DENATRAN)</label>
                                <select class="form-select select2-basic" name="cCorVeic" id="cCorVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="01" @if (old('cCorVeic') == '01') selected @endif>01 - AMARELO</option>
                                    <option value="02" @if (old('cCorVeic') == '02') selected @endif>02 - AZUL</option>
                                    <option value="03" @if (old('cCorVeic') == '03') selected @endif>03 - BEGE</option>
                                    <option value="04" @if (old('cCorVeic') == '04') selected @endif>04 - BRANCA</option>
                                    <option value="05" @if (old('cCorVeic') == '05') selected @endif>05 - CINZA</option>
                                    <option value="06" @if (old('cCorVeic') == '06') selected @endif>06 - DOURADA</option>
                                    <option value="07" @if (old('cCorVeic') == '07') selected @endif>07 - GRENAR</option>
                                    <option value="08" @if (old('cCorVeic') == '08') selected @endif>08 - LARANJA</option>
                                    <option value="09" @if (old('cCorVeic') == '09') selected @endif>09 - MARROM</option>
                                    <option value="10" @if (old('cCorVeic') == '10') selected @endif>10 - PRATA</option>
                                    <option value="11" @if (old('cCorVeic') == '11') selected @endif>11 - PRETA</option>
                                    <option value="12" @if (old('cCorVeic') == '12') selected @endif>12 - ROSA</option>
                                    <option value="13" @if (old('cCorVeic') == '13') selected @endif>13 - ROXA</option>
                                    <option value="14" @if (old('cCorVeic') == '14') selected @endif>14 - VERDE</option>
                                    <option value="15" @if (old('cCorVeic') == '15') selected @endif>15 - VERMELHA</option>
                                    <option value="16" @if (old('cCorVeic') == '16') selected @endif>16 - FANTASIA</option>
                                </select>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cCorMontVeic" class="form-label">Cód. Cor Montadora</label>
                                <input type="text" class="form-control" id="cCorMontVeic" name="cCorMontVeic" value="{{ old('cCorMontVeic') }}" placeholder="Código da Montadora">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="cMarcaVeic" class="form-label">Código da Marca</label>
                                <input type="text" class="form-control" id="cMarcaVeic" name="cMarcaVeic" value="{{ old('cMarcaVeic') }}" placeholder="Código da Marca/Modelo">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="condVeic" class="form-label">Condição do Veículo</label>
                                <select class="form-select select2-basic" name="condVeic" id="condVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="1" @if (old('condVeic') == '1') selected @endif>ACABADO</option>
                                    <option value="2" @if (old('condVeic') == '2') selected @endif>INACABADO</option>
                                    <option value="3" @if (old('condVeic') == '3') selected @endif>SEMIACABO</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="espVeic" class="form-label">Espécie do Veículo</label>
                                <select class="form-select select2-basic" name="espVeic" id="espVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="1" @if (old('espVeic') == '1') selected @endif>PASSAGEIRO</option>
                                    <option value="2" @if (old('espVeic') == '2') selected @endif>CARGA</option>
                                    <option value="3" @if (old('espVeic') == '3') selected @endif>MISTO</option>
                                    <option value="4" @if (old('espVeic') == '4') selected @endif>CORRIDA</option>
                                    <option value="5" @if (old('espVeic') == '5') selected @endif>TRAÇÃO</option>
                                    <option value="6" @if (old('espVeic') == '6') selected @endif>ESPECIAL</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="vinVeic" class="form-label">Condição do Chassi (VIN)</label>
                                <select class="form-select select2-basic" name="vinVeic" id="vinVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="N" @if (old('vinVeic') == 'N') selected @endif>NORMAL</option>
                                    <option value="R" @if (old('vinVeic') == 'R') selected @endif>REMARCADO</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lotVeic" class="form-label">Lotação Máxima</label>
                                <input type="text" class="form-control" id="lotVeic" name="lotVeic" value="{{ old('lotVeic') }}" placeholder="Nº de Pessoas">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-5 mb-3">
                                <label for="restriVeic" class="form-label">Restrição</label>
                                <select class="form-select select2-basic" name="restriVeic" id="restriVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="0" @if (old('restriVeic') == '0') selected @endif>NÃO HÁ</option>
                                    <option value="1" @if (old('restriVeic') == '1') selected @endif>ALIENAÇÃO FIDUNCIÁRIA</option>
                                    <option value="2" @if (old('restriVeic') == '2') selected @endif>ARRENDAMENTO MERCANTIL</option>
                                    <option value="3" @if (old('restriVeic') == '3') selected @endif>RESERVA DE DOMÍNIO</option>
                                    <option value="4" @if (old('restriVeic') == '4') selected @endif>PENHOR DE VEÍCULOS</option>
                                    <option value="9" @if (old('restriVeic') == '9') selected @endif>OUTRAS</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="cargaVeic" class="form-label">Carga Máxima (kg)</label>
                                <input type="number" class="form-control" id="cargaVeic" name="cargaVeic" value="{{ old('cargaVeic') }}" placeholder="0">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="operVeic" class="form-label">Tipo de Operação</label>
                                <select class="form-select select2-basic" name="operVeic" id="operVeic">
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="1" @if (old('operVeic') == '1') selected @endif>VENDA CONCESSIONÁRIA</option>
                                    <option value="2" @if (old('operVeic') == '2') selected @endif>FATURAMENTO DIRETO PARA CONSUMIDOR FINAL</option>
                                    <option value="3" @if (old('operVeic') == '3') selected @endif>VENDA DIRETO PARA GRANDES CONSUMIDORES</option>
                                    <option value="0" @if (old('operVeic') == '0') selected @endif>OUTRAS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ÁREA DE BOTÕES DINÂMICOS --}}
                <div class="row mt-4">
                    <div class="col-12 text-right">
                        <div id="buttons-area">
                            {{-- Os botões serão injetados aqui via JavaScript --}}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

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

            // Elementos do DOM
            const form = document.getElementById('main-form');
            const buttonsArea = document.getElementById('buttons-area');
            const isVehicleCheck = document.getElementById('tpProd');
            
            const tabs = {
                home: document.getElementById('home'),
                profile: document.getElementById('profile'),
                veic: document.getElementById('veic')
            };

            const tabLinks = {
                home: $('#home-tab'),
                profile: $('#profile-tab'),
                veic: $('#veic-tab')
            };

            // HTML dos botões
            const btnProximo1HTML = `<button type="button" id="btn-proximo-1" class="btn custom-btn-primary" disabled>Próximo <i class="fas fa-arrow-right ml-1"></i></button>`;
            const btnProximo2HTML = `<button type="button" id="btn-proximo-2" class="btn custom-btn-primary" disabled>Próximo <i class="fas fa-arrow-right ml-1"></i></button>`;
            const btnSalvarHTML = `<button type="submit" id="btn-salvar" class="btn custom-btn-primary">Salvar Produto</button>`;
            const btnSalvarVeicHTML = `<button type="submit" id="btn-salvar-veic" class="btn custom-btn-primary" disabled>Salvar Produto</button>`;
            
            // Função para verificar validade de uma aba
            const checkTabValidity = (tabElement) => {
                const fields = tabElement.querySelectorAll('[required]');
                for (const field of fields) {
                    if (!field.value) return false;
                }
                return true;
            };

            // Atualiza o estado dos botões da aba ativa
            const updateButtonState = () => {
                const activeTabId = document.querySelector('.tab-pane.active').id;
                
                if (activeTabId === 'home') {
                    const btn = document.getElementById('btn-proximo-1');
                    if(btn) btn.disabled = !checkTabValidity(tabs.home);
                } else if (activeTabId === 'profile' && isVehicleCheck.checked) {
                    const btn = document.getElementById('btn-proximo-2');
                    if(btn) btn.disabled = !checkTabValidity(tabs.profile);
                } else if (activeTabId === 'veic') {
                    const btn = document.getElementById('btn-salvar-veic');
                    if(btn) btn.disabled = !checkTabValidity(tabs.veic);
                }
            };
            
            // Atualiza quais botões são visíveis
            const updateButtonVisibility = () => {
                const activeTabId = document.querySelector('.tab-pane.active').id;
                buttonsArea.innerHTML = ''; // Limpa a área

                if (activeTabId === 'home') {
                    buttonsArea.innerHTML = btnProximo1HTML;
                } else if (activeTabId === 'profile') {
                    if (isVehicleCheck.checked) {
                        buttonsArea.innerHTML = btnProximo2HTML;
                    } else {
                        buttonsArea.innerHTML = btnSalvarHTML;
                    }
                } else if (activeTabId === 'veic') {
                    if (isVehicleCheck.checked) {
                        buttonsArea.innerHTML = btnSalvarVeicHTML;
                    }
                }
                updateButtonState(); // Atualiza o estado do novo botão
            };

            // EVENT LISTENERS
            form.addEventListener('input', updateButtonState);
            $('.select2-basic').on('change', updateButtonState);

            isVehicleCheck.addEventListener('change', () => {
                document.getElementById('veicTab').style.display = isVehicleCheck.checked ? 'block' : 'none';
                const veicFields = tabs.veic.querySelectorAll('input, select');
                veicFields.forEach(field => {
                    // Limpa a validação anterior antes de mudar o estado
                    field.classList.remove('is-invalid', 'is-valid');
                    field.required = isVehicleCheck.checked;
                });
                updateButtonVisibility();
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', updateButtonVisibility);

            // Delegação de eventos para os botões dinâmicos
            buttonsArea.addEventListener('click', (e) => {
                if (e.target.id === 'btn-proximo-1') {
                    tabLinks.profile.tab('show');
                } else if (e.target.id === 'btn-proximo-2') {
                    tabLinks.veic.tab('show');
                }
            });
            
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const firstInvalidField = form.querySelector(':invalid');
                    if (firstInvalidField) {
                        const tabPane = firstInvalidField.closest('.tab-pane');
                        if (tabPane) {
                            const tabLink = document.querySelector(`.nav-tabs .nav-link[href="#${tabPane.id}"]`);
                            if (tabLink) {
                                $(tabLink).tab('show');
                                setTimeout(() => { 
                                    firstInvalidField.focus(); 
                                    // Para campos select2, o foco precisa ser aberto
                                    if ($(firstInvalidField).hasClass('select2-basic')) {
                                        $(firstInvalidField).select2('open');
                                    }
                                }, 250);
                            }
                        }
                    }
                }
                form.classList.add('was-validated');
            });
            
            // Lógica inicial
            updateButtonVisibility();
            isVehicleCheck.dispatchEvent(new Event('change')); // Garante estado inicial correto
       });
       
        function liberarProdutos() {
            const empresaSelect = document.getElementById("empresa");
            const categoriaSelect = document.getElementById("categoria");
            const selectedEmpresa = empresaSelect.value;

            categoriaSelect.removeAttribute('disabled');
            // Reset do Select2
            $(categoriaSelect).val(null).trigger('change');
            
            const options = categoriaSelect.querySelectorAll('.categoria-option');
            options.forEach(option => {
                if (option.dataset.empresa == selectedEmpresa) {
                    option.style.display = "";
                } else {
                    option.style.display = "none";
                }
            });
             // Atualiza o Select2 após manipular as opções
            $(categoriaSelect).trigger('change');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            @if ($user->empresa_id != 1)
                const categoriaSelect = document.getElementById("categoria");
                const empresaId = document.getElementById("empresa").value;
                categoriaSelect.removeAttribute('disabled');
                const options = categoriaSelect.querySelectorAll('.categoria-option');
                options.forEach(option => {
                    if (option.dataset.empresa == empresaId) {
                        option.style.display = "";
                    } else {
                        option.style.display = "none";
                    }
                });
                $(categoriaSelect).trigger('change');
            @endif
        });

        function setaNcm(ncm) {
            document.getElementById('ncm').value = ncm;
            $('#NcmModal').modal('hide');
        }
    </script>
@endpush

