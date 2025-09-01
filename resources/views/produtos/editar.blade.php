@extends('adminlte::page')

@section('title', 'Editar Produto')

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
        transform: translateY(-2px) !important;
    }
    .custom-btn-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
        padding: 10px 20px !important;
        transition: all 0.3s ease !important;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
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
            <h1 class="m-0 text-dark" style="font-weight: 600;">Edição de Produto</h1>
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
            <li class="nav-item" style="display: {{ $produto->tpProd ? 'block' : 'none' }}" id="veicTab">
                <a class="nav-link" id="veic-tab" data-toggle="pill" href="#veic" role="tab"
                    aria-controls="veic" aria-selected="false"><b>Informações de Veículo</b></a>
            </li>
        </ul>
        <form class="needs-validation mt-4" novalidate method="POST" action="{{ route('update_produto', [$produto->id]) }}">
            @csrf
            @method('PUT')
            <div class="tab-content" id="tabContent">
                {{-- ABA 1: INFORMAÇÕES DO PRODUTO --}}
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="empresa" class="form-label">Empresa</label>
                            <select class="form-select select2-basic" name="empresa" id="empresa" disabled>
                                <option>{{ $produto->fantasia }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select select2-basic" name="categoria" id="categoria" required>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @if($produto->categoria_id == $categoria->id) selected @endif>{{ $categoria->descricao }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Informe uma categoria.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label for="produto" class="form-label">Nome do Produto</label>
                            <input class="form-control" type="text" name="produto" id="produto" value="{{ $produto->produto }}" required oninput="this.value = this.value.toUpperCase()">
                            <div class="invalid-feedback">Informe um nome de produto.</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="codigo" class="form-label">Cód. de Barras (EAN)</label>
                            <input class="form-control" type="text" name="codigo" id="codigo" value="{{ $produto->codigo }}" oninput="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ncm" class="form-label">NCM</label>
                            <div class="input-group">
                                <input class="form-control" type="text" name="ncm" id="ncm" value="{{ $produto->ncm }}" required>
                                <button title="Buscar Ncm" class="btn btn-dark" type="button" data-toggle="modal" data-target="#NcmModal" style="border-radius: 0 8px 8px 0;"><i class="fa fa-search"></i></button>
                                <div class="invalid-feedback">Informe um ncm.</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="precocusto" class="form-label">Preço Custo</label>
                            <input class="form-control" type="number" name="precocusto" id="precocusto" value="{{ $produto->precocusto }}" step="0.01" required>
                            <div class="invalid-feedback">Informe um preço de custo.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="precovenda" class="form-label">Preço de Venda</label>
                            <input class="form-control" type="number" name="precovenda" id="precovenda" value="{{ $produto->precovenda }}" step="0.01" required>
                            <div class="invalid-feedback">Informe um preço de venda.</div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label for="un" class="form-label">Unidade</label>
                            <select name="un" id="un" class="form-select select2-basic" required>
                                <option value="un" @if ($produto->un == 'un') selected @endif>UN</option>
                                <option value="cx" @if ($produto->un == 'cx') selected @endif>CX</option>
                                <option value="kg" @if ($produto->un == 'kg') selected @endif>KG</option>
                                <option value="l" @if ($produto->un == 'l') selected @endif>L</option>
                                <option value="ml" @if ($produto->un == 'ml') selected @endif>ML</option>
                                <option value="m" @if ($produto->un == 'm') selected @endif>M</option>
                                <option value="cm" @if ($produto->un == 'cm') selected @endif>CM</option>
                                <option value="fd" @if ($produto->un == 'fd') selected @endif>FD</option>
                                <option value="mil" @if ($produto->un == 'mil') selected @endif>MIL</option>
                                <option value="SC" @if ($produto->un == 'SC') selected @endif>SC</option>
                            </select>
                            <div class="invalid-feedback">Informe uma unidade.</div>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" disabled name="tpProd" id="tpProd" @if ($produto->tpProd) checked @endif>
                                <input type="hidden" name="tpProd" value="{{ $produto->tpProd ? 1 : 0 }}">
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
                                <option value="">Selecione o CFOP Interno</option>
                                @foreach ($cfops as $cfop)
                                    <option value="{{ $cfop->cfop }}" @if ($produto->cfop_interno == $cfop->cfop) selected @endif>{{ $cfop->cfop }} - {{ $cfop->natureza }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Informe um CFOP interno válido.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cfopexterno" class="form-label">CFOP Externo (Fora do Estado)</label>
                            <select class="form-select select2-basic" name="cfopexterno" id="cfopexterno" required>
                                <option value="">Selecione o CFOP Externo</option>
                                @foreach ($cfops as $cfop)
                                    <option value="{{ $cfop->cfop }}" @if ($produto->cfop_externo == $cfop->cfop) selected @endif>{{ $cfop->cfop }} - {{ $cfop->natureza }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Informe um CFOP externo válido.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cst" class="form-label">CST</label>
                            <select class="form-select select2-basic" name="cst" id="cst" required>
                                <option value="00" @if ($produto->cst == '00') selected @endif>00 - Tributação integral</option>
                                <option value="10" @if ($produto->cst == '10') selected @endif>10 - Tributação com ICMS e acréscimo de ST</option>
                                <option value="20" @if ($produto->cst == '20') selected @endif>20 - Tributação com ICMS e acréscimo de ST com direito a crédito</option>
                                <option value="30" @if ($produto->cst == '30') selected @endif>30 - Tributação simplificada (sem direito a crédito)</option>
                                <option value="40" @if ($produto->cst == '40') selected @endif>40 - Tributação simplificada com acréscimo de ST</option>
                                <option value="41" @if ($produto->cst == '41') selected @endif>41 - Tributação com ICMS e acréscimo de ST por Substituição Tributária</option>
                                <option value="50" @if ($produto->cst == '50') selected @endif>50 - Tributação com ICMS e acréscimo de ST por Substituição Tributária com direito a crédito</option>
                                <option value="51" @if ($produto->cst == '51') selected @endif>51 - Tributação com ICMS e acréscimo de ST por Substituição Tributária sem direito a crédito</option>
                                <option value="60" @if ($produto->cst == '60') selected @endif>60 - Tributação com ICMS e acréscimo de ST por Substituição Tributária com acréscimo</option>
                                <option value="70" @if ($produto->cst == '70') selected @endif>70 - Redução de base de cálculo e cobrança do ICMS por substituição tributária</option>
                                <option value="90" @if ($produto->cst == '90') selected @endif>90 - Outras operações</option>
                            </select>
                            <div class="invalid-feedback">Informe o cst.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cst_pis" class="form-label">CST/PIS</label>
                            <select class="form-select select2-basic" name="cst_pis" id="cst_pis" required>
                                <option value="">--Selecione o CST/PIS--</option>
                                <option value="1" @if ($produto->cst_pis == '1') selected @endif>1 - Operação Tributável com Alíquota Básica</option>
                                <option value="2" @if ($produto->cst_pis == '2') selected @endif>2 - Operação Tributável com Alíquota Diferenciada</option>
                                <option value="3" @if ($produto->cst_pis == '3') selected @endif>3 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                <option value="4" @if ($produto->cst_pis == '4') selected @endif>4 - Operação Tributável Monofásica – Revenda a Alíquota Zero</option>
                                <option value="5" @if ($produto->cst_pis == '5') selected @endif>5 - Operação Tributável por Substituição Tributária</option>
                                <option value="6" @if ($produto->cst_pis == '6') selected @endif>6 - Operação Tributável a Alíquota Zero</option>
                                <option value="7" @if ($produto->cst_pis == '7') selected @endif>7 - Operação Isenta da Contribuição</option>
                                <option value="8" @if ($produto->cst_pis == '8') selected @endif>8 - Operação sem Incidência da Contribuição</option>
                                <option value="9" @if ($produto->cst_pis == '9') selected @endif>9 - Operação com Suspensão da Contribuição</option>
                                <option value="49" @if ($produto->cst_pis == '49') selected @endif>49 - Outras Operações de Saída</option>
                                <option value="50" @if ($produto->cst_pis == '50') selected @endif>50 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                <option value="51" @if ($produto->cst_pis == '51') selected @endif>51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                <option value="52" @if ($produto->cst_pis == '52') selected @endif>52 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita de Exportação</option>
                                <option value="53" @if ($produto->cst_pis == '53') selected @endif>53 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                <option value="54" @if ($produto->cst_pis == '54') selected @endif>54 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                <option value="55" @if ($produto->cst_pis == '55') selected @endif>55 - Operação com Direito a Crédito – Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                <option value="56" @if ($produto->cst_pis == '56') selected @endif>56 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação</option>
                                <option value="60" @if ($produto->cst_pis == '60') selected @endif>60 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                <option value="61" @if ($produto->cst_pis == '61') selected @endif>61 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                <option value="62" @if ($produto->cst_pis == '62') selected @endif>62 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação</option>
                                <option value="63" @if ($produto->cst_pis == '63') selected @endif>63 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                <option value="64" @if ($produto->cst_pis == '64') selected @endif>64 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                <option value="65" @if ($produto->cst_pis == '65') selected @endif>65 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação</option>
                                <option value="66" @if ($produto->cst_pis == '66') selected @endif>66 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and de Exportação</option>
                                <option value="67" @if ($produto->cst_pis == '67') selected @endif>67 - Crédito Presumido – Outras Operações</option>
                                <option value="70" @if ($produto->cst_pis == '70') selected @endif>70 - Operação de Aquisição sem Direito a Crédito</option>
                                <option value="71" @if ($produto->cst_pis == '71') selected @endif>71 - Operação de Aquisição com Isenção</option>
                                <option value="72" @if ($produto->cst_pis == '72') selected @endif>72 - Operação de Aquisição com Suspensão</option>
                                <option value="73" @if ($produto->cst_pis == '73') selected @endif>73 - Operação de Aquisição a Alíquota Zero</option>
                                <option value="74" @if ($produto->cst_pis == '74') selected @endif>74 - Operação de Aquisição sem Incidência da Contribuição</option>
                                <option value="75" @if ($produto->cst_pis == '75') selected @endif>75 - Operação de Aquisição por Substituição Tributária</option>
                                <option value="98" @if ($produto->cst_pis == '98') selected @endif>98 - Outras Operações de Entrada</option>
                                <option value="99" @if ($produto->cst_pis == '99') selected @endif>99 - Outras Operações</option>
                            </select>
                            <div class="invalid-feedback">Informe um cst/pis.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cst_cofins" class="form-label">CST/COFINS</label>
                            <select class="form-select select2-basic" name="cst_cofins" id="cst_cofins" required>
                                <option value="">--Selecione o CST/COFINS--</option>
                                <option value="1" @if ($produto->cst_cofins == '1') selected @endif>1 - Operação Tributável com Alíquota Básica</option>
                                <option value="2" @if ($produto->cst_cofins == '2') selected @endif>2 - Operação Tributável com Alíquota Diferenciada</option>
                                <option value="3" @if ($produto->cst_cofins == '3') selected @endif>3 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                <option value="4" @if ($produto->cst_cofins == '4') selected @endif>4 - Operação Tributável Monofásica – Revenda a Alíquota Zero</option>
                                <option value="5" @if ($produto->cst_cofins == '5') selected @endif>5 - Operação Tributável por Substituição Tributária</option>
                                <option value="6" @if ($produto->cst_cofins == '6') selected @endif>6 - Operação Tributável a Alíquota Zero</option>
                                <option value="7" @if ($produto->cst_cofins == '7') selected @endif>7 - Operação Isenta da Contribuição</option>
                                <option value="8" @if ($produto->cst_cofins == '8') selected @endif>8 - Operação sem Incidência da Contribuição</option>
                                <option value="9" @if ($produto->cst_cofins == '9') selected @endif>9 - Operação com Suspensão da Contribuição</option>
                                <option value="49" @if ($produto->cst_cofins == '49') selected @endif>49 - Outras Operações de Saída</option>
                                <option value="50" @if ($produto->cst_cofins == '50') selected @endif>50 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                <option value="51" @if ($produto->cst_cofins == '51') selected @endif>51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                <option value="52" @if ($produto->cst_cofins == '52') selected @endif>52 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita de Exportação</option>
                                <option value="53" @if ($produto->cst_cofins == '53') selected @endif>53 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                <option value="54" @if ($produto->cst_cofins == '54') selected @endif>54 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                <option value="55" @if ($produto->cst_cofins == '55') selected @endif>55 - Operação com Direito a Crédito – Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                <option value="56" @if ($produto->cst_cofins == '56') selected @endif>56 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação</option>
                                <option value="60" @if ($produto->cst_cofins == '60') selected @endif>60 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                <option value="61" @if ($produto->cst_cofins == '61') selected @endif>61 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                <option value="62" @if ($produto->cst_cofins == '62') selected @endif>62 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação</option>
                                <option value="63" @if ($produto->cst_cofins == '63') selected @endif>63 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                <option value="64" @if ($produto->cst_cofins == '64') selected @endif>64 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                <option value="65" @if ($produto->cst_cofins == '65') selected @endif>65 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação</option>
                                <option value="66" @if ($produto->cst_cofins == '66') selected @endif>66 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and de Exportação</option>
                                <option value="67" @if ($produto->cst_cofins == '67') selected @endif>67 - Crédito Presumido – Outras Operações</option>
                                <option value="70" @if ($produto->cst_cofins == '70') selected @endif>70 - Operação de Aquisição sem Direito a Crédito</option>
                                <option value="71" @if ($produto->cst_cofins == '71') selected @endif>71 - Operação de Aquisição com Isenção</option>
                                <option value="72" @if ($produto->cst_cofins == '72') selected @endif>72 - Operação de Aquisição com Suspensão</option>
                                <option value="73" @if ($produto->cst_cofins == '73') selected @endif>73 - Operação de Aquisição a Alíquota Zero</option>
                                <option value="74" @if ($produto->cst_cofins == '74') selected @endif>74 - Operação de Aquisição sem Incidência da Contribuição</option>
                                <option value="75" @if ($produto->cst_cofins == '75') selected @endif>75 - Operação de Aquisição por Substituição Tributária</option>
                                <option value="98" @if ($produto->cst_cofins == '98') selected @endif>98 - Outras Operações de Entrada</option>
                                <option value="99" @if ($produto->cst_cofins == '99') selected @endif>99 - Outras Operações</option>
                            </select>
                            <div class="invalid-feedback">Informe um cst/cofins.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cst_csosn" class="form-label">CST/CSOSN</label>
                            <select class="form-select select2-basic" name="cst_csosn" id="cst_csosn" required>
                                <option value="">--Selecione o CST/CSOSN--</option>
                                <option value="101" @if ($produto->cst_csosn == '101') selected @endif>101 - Tributada pelo Simples Nacional com permissão de crédito</option>
                                <option value="102" @if ($produto->cst_csosn == '102') selected @endif>102 - Tributada pelo Simples Nacional sem permissão de crédito</option>
                                <option value="103" @if ($produto->cst_csosn == '103') selected @endif>103 - Isenção do ICMS no Simples Nacional para faixa de receita bruta</option>
                                <option value="201" @if ($produto->cst_csosn == '201') selected @endif>201 - Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                                <option value="202" @if ($produto->cst_csosn == '202') selected @endif>202 - Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                                <option value="203" @if ($produto->cst_csosn == '203') selected @endif>203 - Isenção do ICMS no Simples Nacional para faixa de receita bruta e com cobrança do ICMS por substituição tributária</option>
                                <option value="300" @if ($produto->cst_csosn == '300') selected @endif>300 - Imune</option>
                                <option value="400" @if ($produto->cst_csosn == '400') selected @endif>400 - Não tributada pelo Simples Nacional</option>
                                <option value="500" @if ($produto->cst_csosn == '500') selected @endif>500 - ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação</option>
                                <option value="900" @if ($produto->cst_csosn == '900') selected @endif>900 - Outros</option>
                            </select>
                            <div class="invalid-feedback">Informe um icms.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="icms" class="form-label">ICMS (%)</label>
                            <input class="form-control" type="number" name="icms" id="icms" step="0.01" value="{{ $produto->icms }}" required>
                            <div class="invalid-feedback">Informe um icms.</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cofins" class="form-label">COFINS (%)</label>
                            <input class="form-control" type="text" name="cofins" id="cofins" value="{{ $produto->cofins }}" required>
                            <div class="invalid-feedback">Informe um confins.</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="pis" class="form-label">PIS (%)</label>
                            <input class="form-control" type="text" name="pis" id="pis" value="{{ $produto->pis }}" required>
                            <div class="invalid-feedback">Informe um pis.</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="ipi" class="form-label">IPI (%)</label>
                            <input type="text" class="form-control" name="ipi" id="ipi" value="{{ $produto->ipi }}" required>
                            <div class="invalid-feedback">Informe um ipi.</div>
                        </div>
                    </div>
                </div>

                {{-- ABA 3: INFORMAÇÕES DO VEÍCULO --}}
                <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                    @if ($produto->tpProd)
                       <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tpVeic" class="form-label">Tipo de Veículo</label>
                                <select class="form-select select2-basic" name="tpVeic" id="tpVeic" required>
                                    <option value="02" {{ $produto->tpVeic == '02' ? 'selected' : '' }}>CICLOMOTOR</option>
                                    <option value="03" {{ $produto->tpVeic == '03' ? 'selected' : '' }}>MOTONETA</option>
                                    <option value="04" {{ $produto->tpVeic == '04' ? 'selected' : '' }}>MOTOCICLO</option>
                                    <option value="05" {{ $produto->tpVeic == '05' ? 'selected' : '' }}>TRICICLO</option>
                                    <option value="06" {{ $produto->tpVeic == '06' ? 'selected' : '' }}>AUTOMÓVEL</option>
                                    <option value="07" {{ $produto->tpVeic == '07' ? 'selected' : '' }}>MICROÔNIBUS</option>
                                    <option value="08" {{ $produto->tpVeic == '08' ? 'selected' : '' }}>ÔNIBUS</option>
                                    <option value="10" {{ $produto->tpVeic == '10' ? 'selected' : '' }}>REBOQUE</option>
                                    <option value="11" {{ $produto->tpVeic == '11' ? 'selected' : '' }}>SEMIREBOQUE</option>
                                    <option value="13" {{ $produto->tpVeic == '13' ? 'selected' : '' }}>CAMINHONETA</option>
                                    <option value="14" {{ $produto->tpVeic == '14' ? 'selected' : '' }}>CAMINHÃO</option>
                                    <option value="17" {{ $produto->tpVeic == '17' ? 'selected' : '' }}>C.TRATOR</option>
                                    <option value="22" {{ $produto->tpVeic == '22' ? 'selected' : '' }}>ESP/ÔNIBUS</option>
                                    <option value="23" {{ $produto->tpVeic == '23' ? 'selected' : '' }}>MISTO/CAM</option>
                                    <option value="24" {{ $produto->tpVeic == '24' ? 'selected' : '' }}>CARGA/CAM</option>
                                </select>
                                <div class="invalid-feedback">Informe um tipo de veículo válido.</div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="chassiVeic" class="form-label">Chassi</label>
                                <input type="text" class="form-control" id="chassiVeic" required name="chassiVeic" oninput="this.value = this.value.toUpperCase()" value="{{ $produto->chassiVeic }}">
                                <div class="invalid-feedback">Informe um chassi.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="renavanVeic" class="form-label">Renavam</label>
                                <input type="text" class="form-control" id="renavanVeic" required name="renavanVeic" value="{{ $produto->renavanVeic }}">
                                <div class="invalid-feedback">Informe um renavan.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="anoFabVeic" class="form-label">Ano de Fabricação</label>
                                <input type="number" class="form-control" id="anoFabVeic" required name="anoFabVeic" min="1950" max="{{ date('Y') }}" value="{{ $produto->anoFabVeic }}">
                                <div class="invalid-feedback">Informe um ano de fabricação.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="anoModVeic" class="form-label">Ano de Modelo</label>
                                <input type="number" class="form-control" id="anoModVeic" required name="anoModVeic" min="1950" max="{{ date('Y') + 1 }}" value="{{ $produto->anoModVeic }}">
                                <div class="invalid-feedback">Informe um ano de modelo.</div>
                            </div>
                        </div>
                        {{-- O restante dos campos de veículo seguem o mesmo padrão... --}}
                    @endif
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6 mx-auto text-center">
                    <button class="btn custom-btn-primary btn-block" type="submit">Salvar Alterações</button>
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
            // Inicializa o Select2 em todos os selects com a classe
            $('.select2-basic').select2({
                placeholder: "Selecione uma opção",
                allowClear: true,
                width: '100%'
            });
        });
        
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
    </script>
@endpush

