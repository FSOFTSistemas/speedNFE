@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    {{-- <div class="row" style="text-align: center">
        <div class="col-md-6 col-xs-10">
            <h5 class="m-0 text-dark">Editar produto</h5>
        </div>
    </div> --}}
    <br>
@stop

@section('content')
    <div class="content">
        <a class="btn btn-secondary" style="margin-bottom: 2%" href="{{ route('produto.index') }}">Voltar</a>
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12" style="width: 100%">

                <div class="card card-primary card-outline card-tabs">

                    <div class="card-header p-0 pt-1 border-bottom-0">
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
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('update_produto', ['id' => $produto->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">


                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label>Empresa</label>
                                            <select class="form-control" name="empresa" id="empresa" disabled>
                                                <option>
                                                    {{ $produto->fantasia }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label>Categoria</label>
                                            <select class="form-control" name="categoria" id="categoria" required>
                                                <option value="{{ $produto->categoria_id }}">
                                                    {{ $produto->descricao }}</option>
                                                @foreach ($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}">
                                                        {{ $categoria->descricao }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="codigo">Código de Barras</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                value="{{ $produto->codigo }}" oninput="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="produto">Produto</label>
                                            <input class="form-control" type="text" name="produto" id="produto"
                                                value="{{ $produto->produto }}" required placeholder="Produto..." oninput="this.value = this.value.toUpperCase()">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="ncm">NCM</label>
                                            <input class="form-control" type="text" name="ncm" id="ncm"
                                                value="{{ $produto->ncm }}" required placeholder="Ncm...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="precocusto">Preço Custo</label>
                                            <input class="form-control" type="number" name="precocusto" id="precocusto"
                                                value="{{ $produto->precocusto }}" required placeholder="Preço Custo...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 col-xs-10">
                                            <label for="precovenda">Preço de Venda</label>
                                            <div class="row">
                                                <input class="form-control" type="number" name="precovenda" id="precovenda"
                                                    value="{{ $produto->precovenda }}" required
                                                    placeholder="Preço Venda...">
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-10">
                                            <label for="un">Unidade</label>
                                            <select name="un" id="un" class="form-control" required>
                                                <option value="un" @if ($produto->un == "un") selected @endif>UN</option>
                                                <option value="cx" @if ($produto->un == "cx") selected @endif>CX</option>
                                                <option value="kg" @if ($produto->un == "kg") selected @endif>KG</option>
                                                <option value="l" @if ($produto->un == "l") selected @endif>L</option>
                                                <option value="ml" @if ($produto->un == "ml") selected @endif>ML</option>
                                                <option value="m" @if ($produto->un == "m") selected @endif>M</option>
                                                <option value="cm" @if ($produto->un == "cm") selected @endif>CM</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="veic">Veículo?</label>
                                            <br>
                                            <input type="checkbox" disabled name="tpProd" id="tpProd"
                                                @if ($produto->tpProd) checked @endif>
                                                <input type="hidden" name="tpProd" id="tpProd" value="{{ $produto->tpProd ? 1 : 0 }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopinterno">CFOP Interno</label>
                                            <select class="form-control" style="width: 100%" name="cfopinterno"
                                                id="cfopinterno" required>
                                                <option value="">--Selecione o CFOP Interno--</option>
                                                @foreach ($cfops as $cfop)
                                                    <option value="{{ $cfop->cfop }}"
                                                        @if ($produto->cfop_interno == $cfop->cfop) selected @endif>
                                                        {{ $cfop->cfop }} -
                                                        {{ $cfop->natureza }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopexterno">CFOP Externo</label>
                                            <select class="form-control" style="width: 100%" name="cfopexterno"
                                                id="cfopexterno" required>
                                                <option value="">--Selecione o CFOP Externo--</option>
                                                @foreach ($cfops as $cfop)
                                                    <option value="{{ $cfop->cfop }}"
                                                        @if ($produto->cfop_externo == $cfop->cfop) selected @endif>
                                                        {{ $cfop->cfop }} -
                                                        {{ $cfop->natureza }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst">CST</label>
                                            <select class="form-control" name="cst" id="cst" required>
                                                <option value="">--Selecione o CST--</option>
                                                <option value="00" @if ($produto->cst == '00') selected @endif>00
                                                    - Tributação integral</option>
                                                <option value="10" @if ($produto->cst == '10') selected @endif>10
                                                    - Tributação com ICMS e acréscimo de ST</option>
                                                <option value="20" @if ($produto->cst == '20') selected @endif>20
                                                    - Tributação com ICMS e acréscimo de ST com
                                                    direito a crédito</option>
                                                <option value="30" @if ($produto->cst == '30') selected @endif>30
                                                    - Tributação simplificada (sem direito a crédito)
                                                </option>
                                                <option value="40" @if ($produto->cst == '40') selected @endif>40
                                                    - Tributação simplificada com acréscimo de ST
                                                </option>
                                                <option value="41" @if ($produto->cst == '41') selected @endif>41
                                                    - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária</option>
                                                <option value="50" @if ($produto->cst == '50') selected @endif>50
                                                    - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária com direito a crédito</option>
                                                <option value="51" @if ($produto->cst == '51') selected @endif>
                                                    51 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária sem direito a crédito</option>
                                                <option value="60" @if ($produto->cst == '60') selected @endif>
                                                    60 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária com acréscimo</option>
                                                <option value="70" @if ($produto->cst == '70') selected @endif>
                                                    70 - Redução de base de cálculo e cobrança do ICMS
                                                    por substituição tributária</option>
                                                <option value="90" @if ($produto->cst == '90') selected @endif>
                                                    90 - Outras operações</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_pis">CST/PIS</label>
                                            <select class="form-control" name="cst_pis" id="cst_pis" required>
                                                <option value="">--Selecione o CST/PIS--</option>
                                                <option value="1" @if ($produto->cst_pis == '1') selected @endif>
                                                    1 - Operação Tributável com Alíquota Básica</option>
                                                <option value="2" @if ($produto->cst_pis == '2') selected @endif>
                                                    2 - Operação Tributável com Alíquota Diferenciada
                                                </option>
                                                <option value="3" @if ($produto->cst_pis == '3') selected @endif>
                                                    3 - Operação Tributável com Alíquota por Unidade de
                                                    Medida de Produto</option>
                                                <option value="4" @if ($produto->cst_pis == '4') selected @endif>
                                                    4 - Operação Tributável Monofásica – Revenda a
                                                    Alíquota Zero</option>
                                                <option value="5" @if ($produto->cst_pis == '5') selected @endif>
                                                    5 - Operação Tributável por Substituição Tributária
                                                </option>
                                                <option value="6" @if ($produto->cst_pis == '6') selected @endif>
                                                    6 - Operação Tributável a Alíquota Zero</option>
                                                <option value="7" @if ($produto->cst_pis == '7') selected @endif>
                                                    7 - Operação Isenta da Contribuição</option>
                                                <option value="8" @if ($produto->cst_pis == '8') selected @endif>
                                                    8 - Operação sem Incidência da Contribuição</option>
                                                <option value="9" @if ($produto->cst_pis == '9') selected @endif>
                                                    9 - Operação com Suspensão da Contribuição</option>
                                                <option value="49" @if ($produto->cst_pis == '49') selected @endif>
                                                    49 - Outras Operações de Saída</option>
                                                <option value="50" @if ($produto->cst_pis == '50') selected @endif>
                                                    50 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="51" @if ($produto->cst_pis == '51') selected @endif>
                                                    51 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                                <option value="52" @if ($produto->cst_pis == '52') selected @endif>
                                                    52 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita de Exportação</option>
                                                <option value="53" @if ($produto->cst_pis == '53') selected @endif>
                                                    53 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                                <option value="54" @if ($produto->cst_pis == '54') selected @endif>
                                                    54 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="55" @if ($produto->cst_pis == '55') selected @endif>
                                                    55 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="56" @if ($produto->cst_pis == '56') selected @endif>
                                                    56 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="60" @if ($produto->cst_pis == '60') selected @endif>
                                                    60 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="61" @if ($produto->cst_pis == '61') selected @endif>
                                                    61 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                                </option>
                                                <option value="62" @if ($produto->cst_pis == '62') selected @endif>
                                                    62 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita de Exportação</option>
                                                <option value="63" @if ($produto->cst_pis == '63') selected @endif>
                                                    63 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                                </option>
                                                <option value="64" @if ($produto->cst_pis == '64') selected @endif>
                                                    64 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="65" @if ($produto->cst_pis == '65') selected @endif>
                                                    65 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                                </option>
                                                <option value="66" @if ($produto->cst_pis == '66') selected @endif>
                                                    66 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                    de Exportação</option>
                                                <option value="67" @if ($produto->cst_pis == '67') selected @endif>
                                                    67 - Crédito Presumido – Outras Operações</option>
                                                <option value="70" @if ($produto->cst_pis == '70') selected @endif>
                                                    70 - Operação de Aquisição sem Direito a Crédito
                                                </option>
                                                <option value="71" @if ($produto->cst_pis == '71') selected @endif>
                                                    71 - Operação de Aquisição com Isenção</option>
                                                <option value="72" @if ($produto->cst_pis == '72') selected @endif>
                                                    72 - Operação de Aquisição com Suspensão</option>
                                                <option value="73" @if ($produto->cst_pis == '73') selected @endif>
                                                    73 - Operação de Aquisição a Alíquota Zero</option>
                                                <option value="74" @if ($produto->cst_pis == '74') selected @endif>
                                                    74 - Operação de Aquisição sem Incidência da
                                                    Contribuição</option>
                                                <option value="75" @if ($produto->cst_pis == '75') selected @endif>
                                                    75 - Operação de Aquisição por Substituição
                                                    Tributária</option>
                                                <option value="98" @if ($produto->cst_pis == '98') selected @endif>
                                                    98 - Outras Operações de Entrada</option>
                                                <option value="99" @if ($produto->cst_pis == '99') selected @endif>
                                                    99 - Outras Operações</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_cofins">CST/COFINS</label>
                                            <select class="form-control" name="cst_cofins" id="cst_cofins" required>
                                                <option value="">--Selecione o CST/COFINS--</option>
                                                <option value="1" @if ($produto->cst_cofins == '1') selected @endif>
                                                    1 - Operação Tributável com Alíquota Básica</option>
                                                <option value="2" @if ($produto->cst_cofins == '2') selected @endif>
                                                    2 - Operação Tributável com Alíquota Diferenciada
                                                </option>
                                                <option value="3" @if ($produto->cst_cofins == '3') selected @endif>
                                                    3 - Operação Tributável com Alíquota por Unidade de
                                                    Medida de Produto</option>
                                                <option value="4" @if ($produto->cst_cofins == '4') selected @endif>
                                                    4 - Operação Tributável Monofásica – Revenda a
                                                    Alíquota Zero</option>
                                                <option value="5" @if ($produto->cst_cofins == '5') selected @endif>
                                                    5 - Operação Tributável por Substituição Tributária
                                                </option>
                                                <option value="6" @if ($produto->cst_cofins == '6') selected @endif>
                                                    6 - Operação Tributável a Alíquota Zero</option>
                                                <option value="7" @if ($produto->cst_cofins == '7') selected @endif>
                                                    7 - Operação Isenta da Contribuição</option>
                                                <option value="8" @if ($produto->cst_cofins == '8') selected @endif>
                                                    8 - Operação sem Incidência da Contribuição</option>
                                                <option value="9" @if ($produto->cst_cofins == '9') selected @endif>
                                                    9 - Operação com Suspensão da Contribuição</option>
                                                <option value="49" @if ($produto->cst_cofins == '49') selected @endif>
                                                    49 - Outras Operações de Saída</option>
                                                <option value="50" @if ($produto->cst_cofins == '50') selected @endif>
                                                    50 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="51" @if ($produto->cst_cofins == '51') selected @endif>
                                                    51 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                                <option value="52" @if ($produto->cst_cofins == '52') selected @endif>
                                                    52 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita de Exportação</option>
                                                <option value="53" @if ($produto->cst_cofins == '53') selected @endif>
                                                    53 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                                <option value="54" @if ($produto->cst_cofins == '54') selected @endif>
                                                    54 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="55" @if ($produto->cst_cofins == '55') selected @endif>
                                                    55 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="56" @if ($produto->cst_cofins == '56') selected @endif>
                                                    56 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="60" @if ($produto->cst_cofins == '60') selected @endif>
                                                    60 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="61" @if ($produto->cst_cofins == '61') selected @endif>
                                                    61 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                                </option>
                                                <option value="62" @if ($produto->cst_cofins == '62') selected @endif>
                                                    62 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita de Exportação</option>
                                                <option value="63" @if ($produto->cst_cofins == '63') selected @endif>
                                                    63 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                                </option>
                                                <option value="64" @if ($produto->cst_cofins == '64') selected @endif>
                                                    64 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="65" @if ($produto->cst_cofins == '65') selected @endif>
                                                    65 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                                </option>
                                                <option value="66" @if ($produto->cst_cofins == '66') selected @endif>
                                                    66 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                    de Exportação</option>
                                                <option value="67" @if ($produto->cst_cofins == '67') selected @endif>
                                                    67 - Crédito Presumido – Outras Operações</option>
                                                <option value="70" @if ($produto->cst_cofins == '70') selected @endif>
                                                    70 - Operação de Aquisição sem Direito a Crédito
                                                </option>
                                                <option value="71" @if ($produto->cst_cofins == '71') selected @endif>
                                                    71 - Operação de Aquisição com Isenção</option>
                                                <option value="72" @if ($produto->cst_cofins == '72') selected @endif>
                                                    72 - Operação de Aquisição com Suspensão</option>
                                                <option value="73" @if ($produto->cst_cofins == '73') selected @endif>
                                                    73 - Operação de Aquisição a Alíquota Zero</option>
                                                <option value="74" @if ($produto->cst_cofins == '74') selected @endif>
                                                    74 - Operação de Aquisição sem Incidência da
                                                    Contribuição</option>
                                                <option value="75" @if ($produto->cst_cofins == '75') selected @endif>
                                                    75 - Operação de Aquisição por Substituição
                                                    Tributária</option>
                                                <option value="98" @if ($produto->cst_cofins == '98') selected @endif>
                                                    98 - Outras Operações de Entrada</option>
                                                <option value="99" @if ($produto->cst_cofins == '99') selected @endif>
                                                    99 - Outras Operações</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cofins">COFINS</label>
                                            <div class="row">
                                                    <input class="form-control" type="text" name="cofins"
                                                        id="cofins" value="{{ $produto->cofins }}" required
                                                        placeholder="Cofins...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="icms">ICMS</label>
                                            <input class="form-control" type="text" name="icms" id="icms"
                                                value="{{ $produto->icms }}" required placeholder="Icms...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_csosn">CST/CSOSN</label>
                                            <select class="form-control" name="cst_csosn" id="cst_csosn" required>
                                                <option value="">--Selecione o CST/CSOSN--</option>
                                                <option value="101" @if ($produto->cst_csosn == '101') selected @endif>
                                                    101 - Tributada pelo Simples Nacional com permissão
                                                    de crédito</option>
                                                <option value="102" @if ($produto->cst_csosn == '102') selected @endif>
                                                    102 - Tributada pelo Simples Nacional sem permissão
                                                    de crédito</option>
                                                <option value="103" @if ($produto->cst_csosn == '103') selected @endif>
                                                    103 - Isenção do ICMS no Simples Nacional para faixa
                                                    de receita bruta</option>
                                                <option value="201" @if ($produto->cst_csosn == '201') selected @endif>
                                                    201 - Tributada pelo Simples Nacional com permissão
                                                    de crédito e com cobrança do ICMS por substituição tributária</option>
                                                <option value="202" @if ($produto->cst_csosn == '202') selected @endif>
                                                    202 - Tributada pelo Simples Nacional sem permissão
                                                    de crédito e com cobrança do ICMS por substituição tributária</option>
                                                <option value="203" @if ($produto->cst_csosn == '203') selected @endif>
                                                    203 - Isenção do ICMS no Simples Nacional para faixa
                                                    de receita bruta e com cobrança do ICMS por substituição tributária
                                                </option>
                                                <option value="300" @if ($produto->cst_csosn == '300') selected @endif>
                                                    300 - Imune</option>
                                                <option value="400" @if ($produto->cst_csosn == '400') selected @endif>
                                                    400 - Não tributada pelo Simples Nacional</option>
                                                <option value="500" @if ($produto->cst_csosn == '500') selected @endif>
                                                    500 - ICMS cobrado anteriormente por substituição
                                                    tributária (substituído) ou por antecipação</option>
                                                <option value="900" @if ($produto->cst_csosn == '900') selected @endif>
                                                    900 - Outros</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="pis">PIS</label>
                                            <input class="form-control" type="text" name="pis" id="pis"
                                                value="{{ $produto->pis }}" required placeholder="Pis...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="ipi">IPI</label>
                                            <input type="text" class="form-control" name="ipi" id="ipi"
                                                value="{{ $produto->ipi }}" required placeholder="Ipi...">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                                    @if ($produto->tpProd)
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="tpVeic" class="col-sm-6 col-form-label">Tipo de
                                                    Veículo</label>
                                                <select class="form-control" name="tpVeic" id="tpVeic" required>
                                                    <option value="02"
                                                        {{ $produto->tpVeic == '02' ? 'selected' : '' }}>
                                                        CICLOMOTOR</option>
                                                    <option value="03"
                                                        {{ $produto->tpVeic == '03' ? 'selected' : '' }}>
                                                        MOTONETA</option>
                                                    <option value="04"
                                                        {{ $produto->tpVeic == '04' ? 'selected' : '' }}>
                                                        MOTOCICLO</option>
                                                    <option value="05"
                                                        {{ $produto->tpVeic == '05' ? 'selected' : '' }}>
                                                        TRICICLO</option>
                                                    <option value="06"
                                                        {{ $produto->tpVeic == '06' ? 'selected' : '' }}>
                                                        AUTOMÓVEL</option>
                                                    <option value="07"
                                                        {{ $produto->tpVeic == '07' ? 'selected' : '' }}>
                                                        MICROÔNIBUS</option>
                                                    <option value="08"
                                                        {{ $produto->tpVeic == '08' ? 'selected' : '' }}>
                                                        ÔNIBUS</option>
                                                    <option value="10"
                                                        {{ $produto->tpVeic == '10' ? 'selected' : '' }}>
                                                        REBOQUE</option>
                                                    <option value="11"
                                                        {{ $produto->tpVeic == '11' ? 'selected' : '' }}>
                                                        SEMIREBOQUE</option>
                                                    <option value="13"
                                                        {{ $produto->tpVeic == '13' ? 'selected' : '' }}>
                                                        CAMINHONETA</option>
                                                    <option value="14"
                                                        {{ $produto->tpVeic == '14' ? 'selected' : '' }}>
                                                        CAMINHÃO</option>
                                                    <option value="17"
                                                        {{ $produto->tpVeic == '17' ? 'selected' : '' }}>
                                                        C.TRATOR</option>
                                                    <option value="22"
                                                        {{ $produto->tpVeic == '22' ? 'selected' : '' }}>
                                                        ESP/ÔNIBUS</option>
                                                    <option value="23"
                                                        {{ $produto->tpVeic == '23' ? 'selected' : '' }}>
                                                        MISTO/CAM</option>
                                                    <option value="24"
                                                        {{ $produto->tpVeic == '24' ? 'selected' : '' }}>
                                                        CARGA/CAM</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="chassiVeic" class="col-sm-2 col-form-label">Chassi</label>
                                                <input type="text" class="form-control" id="chassiVeic" required
                                                    name="chassiVeic" oninput="this.value = this.value.toUpperCase()"
                                                    value="{{ $produto->chassiVeic }}" placeholder="Chassi...">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="renavanVeic" class="col-sm-4 col-form-label">Renavan</label>
                                                <input type="text" class="form-control" id="renavanVeic" required
                                                    name="renavanVeic" value="{{ $produto->renavanVeic }}"
                                                    placeholder="Renavan...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="anoFabVeic" class="col-sm-6 col-form-label">Ano de
                                                    Fabricação</label>
                                                <input type="number" class="form-control" id="anoFabVeic" required
                                                    name="anoFabVeic" min="1950" max="{{ date('Y') }}"
                                                    value="{{ $produto->anoFabVeic }}"
                                                    placeholder="Ano de Fabricação...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="anoModVeic" class="col-sm-6 col-form-label">Ano de
                                                    Modelo</label>
                                                <input type="number" class="form-control" id="anoModVeic" required
                                                    name="anoModVeic" min="1950" max="{{ date('Y') + 1 }}"
                                                    value="{{ $produto->anoModVeic }}" placeholder="Ano de Modelo...">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="pesoLVeic" class="col-sm-4 col-form-label">Peso
                                                    Líquido</label>
                                                <input type="number" class="form-control" step="0.01" id="pesoLVeic"
                                                    required name="pesoLVeic" value="{{ $produto->pesoLVeic }}"
                                                    placeholder="Peso Líquido...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pesoBVeic" class="col-sm-4 col-form-label">Peso Bruto</label>
                                                <input type="number" class="form-control" id="pesoBVeic" required
                                                    name="pesoBVeic" value="{{ $produto->pesoBVeic }}"
                                                    placeholder="Peso Bruto...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="distVeic" class="col-sm-4 col-form-label">Distância</label>
                                                <input type="text" class="form-control" id="distVeic" required
                                                    name="distVeic" value="{{ $produto->distVeic }}"
                                                    placeholder="Distância...">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-5">
                                                <label for="combVeic" class="col-sm-4 col-form-label">Combustível</label>
                                                <select class="form-control" name="combVeic" id="combVeic" required>
                                                    <option value="1"
                                                        {{ $produto->combVeic == '1' ? 'selected' : '' }}>
                                                        ÁLCOOL</option>
                                                    <option value="2"
                                                        {{ $produto->combVeic == '2' ? 'selected' : '' }}>
                                                        GASOLINA</option>
                                                    <option value="3"
                                                        {{ $produto->combVeic == '3' ? 'selected' : '' }}>
                                                        DIESEL</option>
                                                    <option value="16"
                                                        {{ $produto->combVeic == '16' ? 'selected' : '' }}>
                                                        ÁLCOOL/GASOLINA</option>
                                                    <option value="17"
                                                        {{ $produto->combVeic == '17' ? 'selected' : '' }}>
                                                        GASOLINA/ÁLCOOL/GNV</option>
                                                    <option value="18"
                                                        {{ $produto->combVeic == '18' ? 'selected' : '' }}>
                                                        GASOLINA/ELÉTRICO</option>
                                                </select>
                                            </div>
                                            <div class="col-md-7">
                                                <label for="nMotorVeic" class="col-sm-4 col-form-label">Número do
                                                    Motor</label>
                                                <input type="text" class="form-control" id="nMotorVeic" required
                                                    name="nMotorVeic" value="{{ $produto->nMotorVeic }}"
                                                    placeholder="Número do Motor..." oninput="this.value = this.value.toUpperCase()">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="cvVeic" class="col-sm-4 col-form-label">Potência</label>
                                                <input type="number" step="0.01" class="form-control" id="cvVeic"
                                                    required name="cvVeic" value="{{ $produto->cvVeic }}"
                                                    placeholder="Potência...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="cm3Veic" class="col-sm-6 col-form-label">Cilindradas</label>
                                                <input type="number" step="0.01" class="form-control" id="cm3Veic"
                                                    required name="cm3Veic" value="{{ $produto->cm3Veic }}"
                                                    placeholder="Cilindradas...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="serieVeic" class="col-sm-4 col-form-label">Série</label>
                                                <input type="text" class="form-control" id="serieVeic" required
                                                    name="serieVeic" value="{{ $produto->serieVeic }}"
                                                    placeholder="Série...">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="tpPVeic" class="col-sm-6 col-form-label">Tipo de
                                                    Pintura</label>
                                                <input type="text" class="form-control" id="tpPVeic" required
                                                    name="tpPVeic" value="{{ $produto->tpPVeic }}"
                                                    placeholder="Tipo de Pintura...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="corVeic" class="col-sm-2 col-form-label">Cor</label>
                                                <input type="text" class="form-control" id="corVeic" required
                                                    name="corVeic" value="{{ $produto->corVeic }}"
                                                    oninput="this.value = this.value.toUpperCase()" placeholder="Cor...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="cCorVeic" class="col-sm-6 col-form-label">Código de
                                                    Cor</label>
                                                <select class="form-control" name="cCorVeic" id="cCorVeic" required>
                                                    <option value="01"
                                                        {{ $produto->cCorVeic == '01' ? 'selected' : '' }}>
                                                        AMARELO</option>
                                                    <option value="02"
                                                        {{ $produto->cCorVeic == '02' ? 'selected' : '' }}>
                                                        AZUL</option>
                                                    <option value="03"
                                                        {{ $produto->cCorVeic == '03' ? 'selected' : '' }}>
                                                        BEGE</option>
                                                    <option value="04"
                                                        {{ $produto->cCorVeic == '04' ? 'selected' : '' }}>
                                                        BRANCA</option>
                                                    <option value="05"
                                                        {{ $produto->cCorVeic == '05' ? 'selected' : '' }}>
                                                        CINZA</option>
                                                    <option value="06"
                                                        {{ $produto->cCorVeic == '06' ? 'selected' : '' }}>
                                                        DOURADA</option>
                                                    <option value="07"
                                                        {{ $produto->cCorVeic == '07' ? 'selected' : '' }}>
                                                        GRENAR</option>
                                                    <option value="08"
                                                        {{ $produto->cCorVeic == '08' ? 'selected' : '' }}>
                                                        LARANJA</option>
                                                    <option value="09"
                                                        {{ $produto->cCorVeic == '09' ? 'selected' : '' }}>
                                                        MARROM</option>
                                                    <option value="10"
                                                        {{ $produto->cCorVeic == '10' ? 'selected' : '' }}>
                                                        PRATA</option>
                                                    <option value="11"
                                                        {{ $produto->cCorVeic == '11' ? 'selected' : '' }}>
                                                        PRETA</option>
                                                    <option value="12"
                                                        {{ $produto->cCorVeic == '12' ? 'selected' : '' }}>
                                                        ROSA</option>
                                                    <option value="13"
                                                        {{ $produto->cCorVeic == '13' ? 'selected' : '' }}>
                                                        ROXA</option>
                                                    <option value="14"
                                                        {{ $produto->cCorVeic == '14' ? 'selected' : '' }}>
                                                        VERDE</option>
                                                    <option value="15"
                                                        {{ $produto->cCorVeic == '15' ? 'selected' : '' }}>
                                                        VERMELHA</option>
                                                    <option value="16"
                                                        {{ $produto->cCorVeic == '16' ? 'selected' : '' }}>
                                                        FANTASIA</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="cCorMontVeic" class="col-sm-8 col-form-label">Código de Cor
                                                    Montadora</label>
                                                <input type="text" class="form-control" id="cCorMontVeic" required
                                                    name="cCorMontVeic" value="{{ $produto->cCorMontVeic }}"
                                                    placeholder="Código de Cor Montadora...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="cMarcaVeic" class="col-sm-8 col-form-label">Código da
                                                    Marca</label>
                                                <input type="text" class="form-control" id="cMarcaVeic" required
                                                    name="cMarcaVeic" value="{{ $produto->cMarcaVeic }}"
                                                    placeholder="Código da Marca...">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="condVeic" class="col-sm-8 col-form-label">Condição do
                                                    Veículo</label>
                                                <select class="form-control" name="condVeic" id="condVeic" required>
                                                    <option value="1"
                                                        {{ $produto->condVeic == '1' ? 'selected' : '' }}>
                                                        ACABADO</option>
                                                    <option value="2"
                                                        {{ $produto->condVeic == '2' ? 'selected' : '' }}>
                                                        INACABADO</option>
                                                    <option value="3"
                                                        {{ $produto->condVeic == '3' ? 'selected' : '' }}>
                                                        SEMIACABO</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="espVeic" class="col-sm-8 col-form-label">Especificação do
                                                    Veículo</label>
                                                <select class="form-control" name="espVeic" id="espVeic" required>
                                                    <option value="1"
                                                        {{ $produto->espVeic == '1' ? 'selected' : '' }}>
                                                        PASSAGEIRO</option>
                                                    <option value="2"
                                                        {{ $produto->espVeic == '2' ? 'selected' : '' }}>
                                                        CARGA</option>
                                                    <option value="3"
                                                        {{ $produto->espVeic == '3' ? 'selected' : '' }}>
                                                        MISTO</option>
                                                    <option value="4"
                                                        {{ $produto->espVeic == '4' ? 'selected' : '' }}>
                                                        CORRIDA</option>
                                                    <option value="5"
                                                        {{ $produto->espVeic == '5' ? 'selected' : '' }}>
                                                        TRAÇÃO</option>
                                                    <option value="6"
                                                        {{ $produto->espVeic == '6' ? 'selected' : '' }}>
                                                        ESPECIAL</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="vinVeic" class="col-sm-6 col-form-label">VIN do
                                                    Veículo</label>
                                                <select class="form-control" name="vinVeic" id="vinVeic" required>
                                                    <option value="N"
                                                        {{ $produto->vinVeic == 'N' ? 'selected' : '' }}>
                                                        NORMAL</option>
                                                    <option value="R"
                                                        {{ $produto->vinVeic == 'R' ? 'selected' : '' }}>
                                                        REMARCADO</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="lotVeic" class="col-sm-6 col-form-label">Lotação
                                                    Máxima</label>
                                                <input type="text" class="form-control" id="lotVeic" required
                                                    name="lotVeic" value="{{ $produto->lotVeic }}"
                                                    placeholder="Lotação Máxima...">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-5">
                                                <label for="restriVeic" class="col-sm-6 col-form-label">Restrição do
                                                    Veículo</label>
                                                <select class="form-control" name="restriVeic" id="restriVeic" required>
                                                    <option value="0"
                                                        {{ $produto->restriVeic == '0' ? 'selected' : '' }}>NÃO HÁ</option>
                                                    <option value="1"
                                                        {{ $produto->restriVeic == '1' ? 'selected' : '' }}>ALIENAÇÃO
                                                        FIDUNCIÁRIA</option>
                                                    <option value="2"
                                                        {{ $produto->restriVeic == '2' ? 'selected' : '' }}>ARRENDAMENTO
                                                        MERCANTIL</option>
                                                    <option value="3"
                                                        {{ $produto->restriVeic == '3' ? 'selected' : '' }}>RESERVA DE
                                                        DOMÍNIO
                                                    </option>
                                                    <option value="4"
                                                        {{ $produto->restriVeic == '4' ? 'selected' : '' }}>PENHOR DE
                                                        VEÍCULOS
                                                    </option>
                                                    <option value="9"
                                                        {{ $produto->restriVeic == '9 ' ? 'selected' : '' }}>OUTRAS
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="cargaVeic" class="col-sm-12 col-form-label">Carga
                                                    Máxima</label>
                                                <input type="number" class="form-control" id="cargaVeic" required
                                                    name="cargaVeic" value="{{ $produto->cargaVeic }}"
                                                    placeholder="Carga Máxima...">
                                            </div>
                                            <div class="col-md-5">
                                                <label for="operVeic" class="col-sm-8 col-form-label">Tipo de
                                                    Operação</label>
                                                <select class="form-control" name="operVeic" id="operVeic" required>
                                                    <option value="1"
                                                        {{ $produto->operVeic == '1' ? 'selected' : '' }}>
                                                        VENDA CONCERSSIONÁRIA</option>
                                                    <option value="2"
                                                        {{ $produto->operVeic == '2' ? 'selected' : '' }}>
                                                        FATURAMENTO DIRETO PARA CONSUMIDOR FINAL</option>
                                                    <option value="3"
                                                        {{ $produto->operVeic == '3' ? 'selected' : '' }}>
                                                        VENDA DIRETO PARA GRANDES CONSUMIDORES</option>
                                                    <option value="0"
                                                        {{ $produto->operVeic == '0' ? 'selected' : '' }}>
                                                        OUTRAS</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row" style="text-align: center; margin-top: 2%;">
                                <div class="col">
                                    <button class="btn btn-success form-control" type="submit">Salvar Produto</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
