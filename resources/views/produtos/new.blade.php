@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Produtos</h1>
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
                        </ul>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('salvar_produto') }}">
                            @csrf

                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">
                                    <div class="row">
                                        <div class="col">
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

                                        <div class="col">
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
                                        <div class="col">
                                            <label for="codigo">Código de Barras</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                placeholder="Código de Barras...">
                                        </div>
                                        <div class="col">
                                            <label for="produto">Produto</label>
                                            <input class="form-control" type="text" name="produto" id="produto"
                                                required placeholder="Produto...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="ncm">NCM</label>
                                            <input class="form-control" type="text" name="ncm" id="ncm"
                                                required placeholder="Ncm...">
                                        </div>
                                        <div class="col">
                                            <label for="precocusto">Preço de Custo</label>
                                            <input class="form-control" type="number" name="precocusto" id="precocusto"
                                                step="0.01" required placeholder="Preço Custo...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="precovenda">Preço de Venda</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="number" name="precovenda"
                                                        step="0.01" id="precovenda" required
                                                        placeholder="Preço Venda...">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
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
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                    <div class="row">
                                        <div class="col">
                                            <label for="cfopinterno">CFOP Interno</label><br>
                                            <select class="form-control" style="width: 100%" name="cfopinterno" id="cfopinterno" required>
                                                <option value="">Selecione o CFOP Interno</option>
                                                @foreach ($cfops as $cfop)
                                                    <option value="{{ $cfop->cfop }}">{{ $cfop->cfop }} -
                                                        {{ $cfop->natureza }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="cfopexterno">CFOP Externo</label><br>
                                            <select class="form-control" style="width: 100%" name="cfopexterno" id="cfopexterno" required>
                                                <option value="">Selecione o CFOP Externo</option>
                                                @foreach ($cfops as $cfop)
                                                    <option value="{{ $cfop->cfop }}">{{ $cfop->cfop }} -
                                                        {{ $cfop->natureza }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="cst">CST</label>
                                            <select class="form-control" name="cst" id="cst" required>
                                                <option value="">Selecione o CST</option>
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
                                        <div class="col">
                                            <label for="cst_pis">CST/PIS</label>
                                            <select class="form-control" name="cst_pis" id="cst_pis" required>
                                                <option value="">Selecione o CST/PIS</option>
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
                                        <div class="col">
                                            <label for="cst_cofins">CST/COFINS</label>
                                            <select class="form-control" name="cst_cofins" id="cst_cofins" required>
                                                <option value="">Selecione o CST/COFINS</option>
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
                                        <div class="col">
                                            <label for="cofins">COFINS</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cofins"
                                                        id="cofins" value="0" required placeholder="Confins...">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="icms">ICMS</label>
                                            <input class="form-control" type="text" name="icms" id="icms"
                                                value="17" required placeholder="Icms...">
                                        </div>
                                        <div class="col">
                                            <label for="cst_csosn">CST/CSOSN</label>
                                            <select class="form-control" name="cst_csosn" id="cst_csosn" required>
                                                <option value="">Selecione o CST/CSOSN</option>
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

                                        <div class="col">
                                            <label for="pis">PIS</label>
                                            <input class="form-control" type="text" name="pis" id="pis"
                                                value="0" required placeholder="Pis...">
                                        </div>
                                        <div class="col">
                                            <label for="ipi">IPI</label>
                                            <input type="text" class="form-control" name="ipi" id="ipi"
                                                value="0" required placeholder="Ipi...">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-success form-control">Salvar
                                            Cliente</button>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>

                </div>

            </div>

        </div>


    </div>

@endsection
@section('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
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
    </script>
@endsection
