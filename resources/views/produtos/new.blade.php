@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h5 class="m-0 text-dark">Produtos</h5>
        </div>
    </div>
@stop

@section('content')
    <a class="btn btn-secondary" href="{{ route('produto.index') }}" style="margin-bottom: 2%">Voltar</a>
    <div class="content">
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12" style="width: 100%">

                <div class="card card-primary card-outline card-tabs">

                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Informações do Produto</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Informações Fiscais</a>
                            </li>
                            <li class="nav-item" style="display: none" id="veicTab">
                                <a class="nav-link" id="veic-tab" data-toggle="pill" href="#veic" role="tab"
                                    aria-controls="veic" aria-selected="false">Informações de Veículo</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('salvar_produto') }}">
                            @csrf

                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            @if ($user->empresa_id == 1)
                                                <label>Empresa</label>
                                                <select onchange="javascript:liberarProdutos({{ $user->empresa_id }})"
                                                    class="form-control" name="empresa" id="empresa" required>
                                                    <option value="">--Escolha uma empresa--</option>
                                                    @foreach ($empresas as $emp)
                                                        <option value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input onload="javascript:liberarProdutos({{ $user->empresa_id }})"
                                                    type="hidden" value="{{ $user->empresa_id }}">
                                            @endif
                                        </div>

                                        <div class="col-md-6 col-xs-10">
                                            <label>Categoria</label>
                                            <select class="form-control" name="categoria" id="categoria" required>
                                                <option value="">-- Escolha uma categoria --</option>
                                                @foreach ($categorias as $categoria)
                                                    <option id="{{ $categoria->empresa_id }}" value="{{ $categoria->id }}">
                                                        {{ $categoria->descricao }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="codigo">Código de Barras</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                placeholder="Código de Barras...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="produto">Produto</label>
                                            <input class="form-control" type="text" name="produto" id="produto"
                                                required placeholder="Produto...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <div class="row">
                                                <div class="col-md-10 col-xs-10">
                                                    <label for="ncm">NCM</label>
                                                    <input class="form-control" type="text" name="ncm" id="ncm"
                                                        required placeholder="Ncm...">
                                                </div>
                                                <div class="col-md-2 col-xs-2">
                                                    <br>
                                                    <button title="Buscar Ncm" class="btn btn-light" type="button"
                                                        data-toggle="modal" data-target=".bd-ncm-modal-lg"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="precocusto">Preço de Custo</label>
                                            <input class="form-control" type="number" name="precocusto" id="precocusto"
                                                step="0.01" required placeholder="Preço Custo...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 col-xs-10">
                                            <label for="precovenda">Preço de Venda</label>
                                            <div class="row">
                                                <input class="form-control" type="number" name="precovenda"
                                                    step="0.01" id="precovenda" required placeholder="Preço Venda...">
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-10">
                                            <label for="un">Unidade</label>
                                            <select name="un" id="un" class="form-control" required>
                                                <option value="">-- Escolha uma Unidade --</option>
                                                <option value="un">UN</option>
                                                <option value="cx">CX</option>
                                                <option value="kg">KG</option>
                                                <option value="l">L</option>
                                                <option value="ml">ML</option>
                                                <option value="m">M</option>
                                                <option value="cm">CM</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="veic">Veículo?</label>
                                            <br>
                                            <input type="checkbox" name="tpProd" id="tpProd"
                                                onclick="checkVeic(this)">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopinterno">CFOP Interno</label><br>
                                            <select class="form-control" style="width: 100%" name="cfopinterno"
                                                id="cfopinterno" required>
                                                <option value="">--Selecione o CFOP Interno--</option>
                                                @foreach ($cfops as $cfop)
                                                    <option value="{{ $cfop->cfop }}">{{ $cfop->cfop }} -
                                                        {{ $cfop->natureza }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopexterno">CFOP Externo</label><br>
                                            <select class="form-control" style="width: 100%" name="cfopexterno"
                                                id="cfopexterno" required>
                                                <option value="">--Selecione o CFOP Externo--</option>
                                                @foreach ($cfops as $cfop)
                                                    <option value="{{ $cfop->cfop }}">{{ $cfop->cfop }} -
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
                                                <option value="00">00 - Tributação integral</option>
                                                <option value="10">10 - Tributação com ICMS e acréscimo de ST</option>
                                                <option value="20">20 - Tributação com ICMS e acréscimo de ST com
                                                    direito a crédito</option>
                                                <option value="30">30 - Tributação simplificada (sem direito a crédito)
                                                </option>
                                                <option value="40">40 - Tributação simplificada com acréscimo de ST
                                                </option>
                                                <option value="41">41 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária</option>
                                                <option value="50">50 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária com direito a crédito</option>
                                                <option value="51">51 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária sem direito a crédito</option>
                                                <option value="60">60 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária com acréscimo</option>
                                                <option value="70">70 - Redução de base de cálculo e cobrança do ICMS
                                                    por substituição tributária</option>
                                                <option value="90">90 - Outras operações</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_pis">CST/PIS</label>
                                            <select class="form-control" name="cst_pis" id="cst_pis" required>
                                                <option value="">--Selecione o CST/PIS--</option>
                                                <option value="1">1 - Operação Tributável com Alíquota Básica</option>
                                                <option value="2">2 - Operação Tributável com Alíquota Diferenciada
                                                </option>
                                                <option value="3">3 - Operação Tributável com Alíquota por Unidade de
                                                    Medida de Produto</option>
                                                <option value="4">4 - Operação Tributável Monofásica – Revenda a
                                                    Alíquota Zero</option>
                                                <option value="5">5 - Operação Tributável por Substituição Tributária
                                                </option>
                                                <option value="6">6 - Operação Tributável a Alíquota Zero</option>
                                                <option value="7">7 - Operação Isenta da Contribuição</option>
                                                <option value="8">8 - Operação sem Incidência da Contribuição</option>
                                                <option value="9">9 - Operação com Suspensão da Contribuição</option>
                                                <option value="49">49 - Outras Operações de Saída</option>
                                                <option value="50">50 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="51">51 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                                <option value="52">52 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita de Exportação</option>
                                                <option value="53">53 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                                <option value="54">54 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="55">55 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="56">56 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="60">60 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="61">61 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                                </option>
                                                <option value="62">62 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita de Exportação</option>
                                                <option value="63">63 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                                </option>
                                                <option value="64">64 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="65">65 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                                </option>
                                                <option value="66">66 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                    de Exportação</option>
                                                <option value="67">67 - Crédito Presumido – Outras Operações</option>
                                                <option value="70">70 - Operação de Aquisição sem Direito a Crédito
                                                </option>
                                                <option value="71">71 - Operação de Aquisição com Isenção</option>
                                                <option value="72">72 - Operação de Aquisição com Suspensão</option>
                                                <option value="73">73 - Operação de Aquisição a Alíquota Zero</option>
                                                <option value="74">74 - Operação de Aquisição sem Incidência da
                                                    Contribuição</option>
                                                <option value="75">75 - Operação de Aquisição por Substituição
                                                    Tributária</option>
                                                <option value="98">98 - Outras Operações de Entrada</option>
                                                <option value="99">99 - Outras Operações</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_cofins">CST/COFINS</label>
                                            <select class="form-control" name="cst_cofins" id="cst_cofins" required>
                                                <option value="">--Selecione o CST/COFINS--</option>
                                                <option value="1">1 - Operação Tributável com Alíquota Básica</option>
                                                <option value="2">2 - Operação Tributável com Alíquota Diferenciada
                                                </option>
                                                <option value="3">3 - Operação Tributável com Alíquota por Unidade de
                                                    Medida de Produto</option>
                                                <option value="4">4 - Operação Tributável Monofásica – Revenda a
                                                    Alíquota Zero</option>
                                                <option value="5">5 - Operação Tributável por Substituição Tributária
                                                </option>
                                                <option value="6">6 - Operação Tributável a Alíquota Zero</option>
                                                <option value="7">7 - Operação Isenta da Contribuição</option>
                                                <option value="8">8 - Operação sem Incidência da Contribuição</option>
                                                <option value="9">9 - Operação com Suspensão da Contribuição</option>
                                                <option value="49">49 - Outras Operações de Saída</option>
                                                <option value="50">50 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="51">51 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                                <option value="52">52 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita de Exportação</option>
                                                <option value="53">53 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                                <option value="54">54 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="55">55 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="56">56 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="60">60 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="61">61 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                                </option>
                                                <option value="62">62 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita de Exportação</option>
                                                <option value="63">63 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                                </option>
                                                <option value="64">64 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="65">65 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                                </option>
                                                <option value="66">66 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                    de Exportação</option>
                                                <option value="67">67 - Crédito Presumido – Outras Operações</option>
                                                <option value="70">70 - Operação de Aquisição sem Direito a Crédito
                                                </option>
                                                <option value="71">71 - Operação de Aquisição com Isenção</option>
                                                <option value="72">72 - Operação de Aquisição com Suspensão</option>
                                                <option value="73">73 - Operação de Aquisição a Alíquota Zero</option>
                                                <option value="74">74 - Operação de Aquisição sem Incidência da
                                                    Contribuição</option>
                                                <option value="75">75 - Operação de Aquisição por Substituição
                                                    Tributária</option>
                                                <option value="98">98 - Outras Operações de Entrada</option>
                                                <option value="99">99 - Outras Operações</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cofins">COFINS</label>
                                            <div class="row">
                                                <div class="col-md-6 col-xs-10">
                                                    <input class="form-control" type="text" name="cofins"
                                                        id="cofins" value="00" required placeholder="Confins...">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="icms">ICMS</label>
                                            <input class="form-control" type="text" name="icms" id="icms"
                                                value="17" required placeholder="Icms...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_csosn">CST/CSOSN</label>
                                            <select class="form-control" name="cst_csosn" id="cst_csosn" required>
                                                <option value="">--Selecione o CST/CSOSN--</option>
                                                <option value="101">101 - Tributada pelo Simples Nacional com permissão
                                                    de crédito</option>
                                                <option value="102">102 - Tributada pelo Simples Nacional sem permissão
                                                    de crédito</option>
                                                <option value="103">103 - Isenção do ICMS no Simples Nacional para faixa
                                                    de receita bruta</option>
                                                <option value="201">201 - Tributada pelo Simples Nacional com permissão
                                                    de crédito e com cobrança do ICMS por substituição tributária</option>
                                                <option value="202">202 - Tributada pelo Simples Nacional sem permissão
                                                    de crédito e com cobrança do ICMS por substituição tributária</option>
                                                <option value="203">203 - Isenção do ICMS no Simples Nacional para faixa
                                                    de receita bruta e com cobrança do ICMS por substituição tributária
                                                </option>
                                                <option value="300">300 - Imune</option>
                                                <option value="400">400 - Não tributada pelo Simples Nacional</option>
                                                <option value="500">500 - ICMS cobrado anteriormente por substituição
                                                    tributária (substituído) ou por antecipação</option>
                                                <option value="900">900 - Outros</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="pis">PIS</label>
                                            <input class="form-control" type="text" name="pis" id="pis"
                                                value="00" required placeholder="Pis...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="ipi">IPI</label>
                                            <input type="text" class="form-control" name="ipi" id="ipi"
                                                value="00" required placeholder="Ipi...">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="tpVeic" class="col-sm-6 col-form-label">Tipo de Veículo</label>
                                            <select class="form-control" name="tpVeic" id="tpVeic">
                                                <option value="02">CICLOMOTOR</option>
                                                <option value="03">MOTONETA</option>
                                                <option value="04">MOTOCICLO</option>
                                                <option value="05">TRICICLO</option>
                                                <option value="06">AUTOMÓVEL</option>
                                                <option value="07">MICROÔNIBUS</option>
                                                <option value="08">ÔNIBUS</option>
                                                <option value="10">REBOQUE</option>
                                                <option value="11">SEMIREBOQUE</option>
                                                <option value="13">CAMINHONETA</option>
                                                <option value="14">CAMINHÃO</option>
                                                <option value="17">C.TRATOR</option>
                                                <option value="22">ESP/ÔNIBUS</option>
                                                <option value="23">MISTO/CAM</option>
                                                <option value="24">CARGA/CAM</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="chassiVeic" class="col-sm-2 col-form-label">Chassi</label>
                                            <input type="text" class="form-control" id="chassiVeic" name="chassiVeic"
                                                oninput="this.value = this.value.toUpperCase()" value=""
                                                placeholder="Chassi...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="cenavanVeic" class="col-sm-4 col-form-label">Renavan</label>
                                            <input type="text" class="form-control" id="renavanVeic"
                                                name="renavanVeic" value="000000000" placeholder="Renavan...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="anoFabVeic" class="col-sm-6 col-form-label">Ano de
                                                Fabricação</label>
                                            <input type="number" class="form-control" id="anoFabVeic" name="anoFabVeic"
                                                min="1950" max="{{ date('Y') }}" value="{{ date('Y') }}"
                                                placeholder="Ano de Fabricação...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="anoModVeic" class="col-sm-6 col-form-label">Ano de Modelo</label>
                                            <input type="number" class="form-control" id="anoModVeic" name="anoModVeic"
                                                min="1950" max="{{ date('Y') + 1 }}" value="{{ date('Y') }}"
                                                placeholder="Ano de Modelo...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="pesoLVeic" class="col-sm-4 col-form-label">Peso Líquido</label>
                                            <input type="number" class="form-control" step="0.01" id="pesoLVeic"
                                                name="pesoLVeic" value="" placeholder="Peso Líquido...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pesoBVeic" class="col-sm-4 col-form-label">Peso Bruto</label>
                                            <input type="number" class="form-control" id="pesoBVeic" name="pesoBVeic"
                                                value="" placeholder="Peso Bruto...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="distVeic" class="col-sm-4 col-form-label">Distância</label>
                                            <input type="text" class="form-control" id="distVeic" name="distVeic"
                                                value="" placeholder="Distância...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label for="combVeic" class="col-sm-4 col-form-label">Combustível</label>
                                            <select class="form-control" name="combVeic" id="combVeic">
                                                <option value="1">ÁLCOOL</option>
                                                <option value="2">GASOLINA</option>
                                                <option value="3">DIESEL</option>
                                                <option value="16">ÁLCOOL/GASOLINA</option>
                                                <option value="17">GASOLINA/ÁLCOOL/GNV</option>
                                                <option value="18">GASOLINA/ELÉTRICO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-7">
                                            <label for="nMotorVeic" class="col-sm-4 col-form-label">Número do
                                                Motor</label>
                                            <input type="text" class="form-control" id="nMotorVeic" name="nMotorVeic"
                                                value="" placeholder="Número do Motor...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="cVVeic" class="col-sm-4 col-form-label">Potência</label>
                                            <input type="number" step="0.01" class="form-control" id="cVVeic" name="cVVeic"
                                                value="" placeholder="Potência...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cm3Veic" class="col-sm-6 col-form-label">Cilindradas</label>
                                            <input type="number" step="0.01" class="form-control" id="cm3Veic" name="cm3Veic"
                                                value="" placeholder="Cilindradas...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="serieVeic" class="col-sm-4 col-form-label">Série</label>
                                            <input type="text" class="form-control" id="serieVeic" name="serieVeic"
                                                value="" placeholder="Série...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="tpPVeic" class="col-sm-6 col-form-label">Tipo de Pintura</label>
                                            <input type="text" class="form-control" id="tpPVeic" name="tpPVeic"
                                                value="" placeholder="Tipo de Pintura...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="corVeic" class="col-sm-2 col-form-label">Cor</label>
                                            <input type="text" class="form-control" id="corVeic" name="corVeic"
                                                oninput="this.value = this.value.toUpperCase()" placeholder="Cor...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cCorVeic" class="col-sm-6 col-form-label">Código de Cor</label>
                                            <select class="form-control" name="cCorVeic" id="cCorVeic">
                                                <option value="01">AMARELO</option>
                                                <option value="02">AZUL</option>
                                                <option value="03">BEGE</option>
                                                <option value="04">BRANCA</option>
                                                <option value="05">CINZA</option>
                                                <option value="06">DOURADA</option>
                                                <option value="07">GRENAR</option>
                                                <option value="08">LARANJA</option>
                                                <option value="09">MARROM</option>
                                                <option value="10">PRATA</option>
                                                <option value="11">PRETA</option>
                                                <option value="12">ROSA</option>
                                                <option value="13">ROXA</option>
                                                <option value="14">VERDE</option>
                                                <option value="15">VERMELHA</option>
                                                <option value="16">FANTASIA</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="cCorMontVeic" class="col-sm-8 col-form-label">Código de Cor
                                                Montadora</label>
                                            <input type="text" class="form-control" id="cCorMontVeic"
                                                name="cCorMontVeic" value=""
                                                placeholder="Código de Cor Montadora...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cMarcaVeic" class="col-sm-8 col-form-label">Código da
                                                Marca</label>
                                            <input type="text" class="form-control" id="cMarcaVeic" name="cMarcaVeic"
                                                value="" placeholder="Código da Marca...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="condVeic" class="col-sm-8 col-form-label">Condição do
                                                Veículo</label>
                                            <select class="form-control" name="condVeic" id="condVeic">
                                                <option value="0" selected>ACABADO</option>
                                                <option value="1">INACABADO</option>
                                                <option value="2">SEMIACABO</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="espVeic" class="col-sm-8 col-form-label">Especificação do
                                                Veículo</label>
                                            <select class="form-control" name="espVeic" id="espVeic">
                                                <option value="1">PASSAGEIRO</option>
                                                <option value="2">CARGA</option>
                                                <option value="3">MISTO</option>
                                                <option value="4">CORRIDA</option>
                                                <option value="5">TRAÇÃO</option>
                                                <option value="6">ESPECIAL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="vinVeic" class="col-sm-6 col-form-label">VIN do Veículo</label>
                                            <select class="form-control" name="vinVeic" id="vinVeic">
                                                <option value="N" selected>NORMAL</option>
                                                <option value="R">REMARCADO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="lotVeic" class="col-sm-6 col-form-label">Lotação Máxima</label>
                                            <input type="text" class="form-control" id="lotVeic" name="lotVeic"
                                                value="" placeholder="Lotação Máxima...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label for="restriVeic" class="col-sm-6 col-form-label">Restrição do
                                                Veículo</label>
                                            <select class="form-control" name="restriVeic" id="restriVeic">
                                                <option value="0">NÃO HÁ</option>
                                                <option value="1">ALIENAÇÃO FIDUNCIÁRIA</option>
                                                <option value="2">ARRENDAMENTO MERCANTIL</option>
                                                <option value="3">RESERVA DE DOMÍNIO</option>
                                                <option value="4">PENHOR DE VEÍCULOS</option>
                                                <option value="9">OUTRAS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cargaVeic" class="col-sm-12 col-form-label">Carga Máxima</label>
                                            <input type="number" class="form-control" id="cargaVeic" name="cargaVeic"
                                                value="" placeholder="Carga Máxima...">
                                        </div>
                                        <div class="col-md-5">
                                            <label for="operVeic" class="col-sm-8 col-form-label">Tipo de Operação</label>
                                            <select class="form-control" name="operVeic" id="operVeic">
                                                <option value="1">VENDA CONCERSSIONÁRIA</option>
                                                <option value="2">FATURAMENTO DIRETO PARA CONSUMIDOR FINAL</option>
                                                <option value="3">VENDA DIRETO PARA GRANDES CONSUMIDORES</option>
                                                <option value="0">OUTRAS</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <br>
                                <div class="row" style="text-align: center">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success form-control">Salvar
                                            Produto</button>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade bd-ncm-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col-md-6 col-xs-10" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Selecione um NCM abaixo...</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="container">

                        <table class="table table-hover" id="ncms" style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>NCM</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($ncms as $ncm)
                                    <tr ondblclick="setaNcm('{{ $ncm->ncm }}')">
                                        <td>{{ $ncm->ncm }}</td>
                                        <td>{{ $ncm->descricao }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#ncms').DataTable({
                responsive: true,
                pageLength: 5,
                lengthMenu: [5, 10, 20],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });

        $(document).ready(function() {
            $('#cfopinterno').select2();
        });

        $(document).ready(function() {
            $('#cfopexterno').select2();
        });

        function liberarProdutos(empresa) {
            select = document.getElementById("categoria");
            document.getElementById('categoria').removeAttribute('disabled');

            if (empresa == 1) {
                var input, filter, ul, li, a, i, txtValue;
                input = document.getElementById('empresa');
                filter = input.options[input.selectedIndex].value;
                option = select.getElementsByTagName('option');
                // Loop through all list items, and hide those who don't match the search query
                for (i = 0; i < option.length; i++) {
                    a = option[i].id;
                    if (a == filter) {
                        option[i].style.display = "";
                    } else {
                        option[i].style.display = "none";
                    }
                }
            } else {
                select.value = "";
            }
        }

        function checkVeic(checkbox) {
            if (checkbox.checked) {
                document.getElementById('veicTab').style.display = 'block'
            } else {
                document.getElementById('veicTab').style.display = 'none'
            }
        }

        function setaNcm(ncm) {
            document.getElementById('ncm').value = ncm;
            var closeBtn = document.querySelector('[data-dismiss="modal"]');
            if (closeBtn) {
                closeBtn.click();
            }
        }
    </script>
@endsection
