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
                                                        <option value="{{ $emp->id }}" @if(old('empresa') == $emp->id) selected @endif>{{ $emp->fantasia }}</option>
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
                                                    <option id="{{ $categoria->empresa_id }}" value="{{ $categoria->id }}" @if(old('categoria') == $categoria->id) selected @endif>
                                                        {{ $categoria->descricao }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="codigo">Código de Barras</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                placeholder="Código de Barras..." oninput="this.value = this.value.toUpperCase()" value="{{ old('codigo') }}">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="produto">Produto</label>
                                            <input class="form-control" type="text" name="produto" id="produto"
                                                required placeholder="Produto..." oninput="this.value = this.value.toUpperCase()" value="{{ old('produto') }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <div class="row">
                                                <div class="col-md-10 col-xs-10">
                                                    <label for="ncm">NCM</label>
                                                    <input class="form-control" type="text" name="ncm" id="ncm"
                                                        required placeholder="Ncm..." value="{{ old('ncm') }}">
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
                                                step="0.01" required placeholder="Preço Custo..." value="{{ old('precocusto') }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 col-xs-10">
                                            <label for="precovenda">Preço de Venda</label>
                                            <div class="row">
                                                <input class="form-control" type="number" name="precovenda"
                                                    step="0.01" id="precovenda" required placeholder="Preço Venda..." value="{{ old('precovenda') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-10">
                                            <label for="un">Unidade</label>
                                            <select name="un" id="un" class="form-control" required>
                                                <option value="">-- Escolha uma Unidade --</option>
                                                <option value="UN" @if(old('un') == "UN") selected @endif>UN</option>
                                                <option value="CX" @if(old('un') == "CX") selected @endif>CX</option>
                                                <option value="KG" @if(old('un') == "CX") selected @endif>KG</option>
                                                <option value="L" @if(old('un') == "KG") selected @endif>L</option>
                                                <option value="ML" @if(old('un') == "ML") selected @endif>ML</option>
                                                <option value="M" @if(old('un') == "M") selected @endif>M</option>
                                                <option value="CM" @if(old('un') == "CM") selected @endif>CM</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="veic">Veículo?</label>
                                            <br>
                                            <input type="checkbox" name="tpProd" id="tpProd"
                                                onclick="checkVeic(this)" @if(old('tpProd')) checked @endif>
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
                                                    <option value="{{ $cfop->cfop }}" @if(old('cfopinterno') == $cfop->cfop) selected @endif>{{ $cfop->cfop }} -
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
                                                    <option value="{{ $cfop->cfop }}" @if(old('cfopexterno') == $cfop->cfop) selected @endif>{{ $cfop->cfop }} -
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
                                                <option value="00" @if(old('cst') == "00") selected @endif>00 - Tributação integral</option>
                                                <option value="10" @if(old('cst') == "10") selected @endif>10 - Tributação com ICMS e acréscimo de ST</option>
                                                <option value="20" @if(old('cst') == "20") selected @endif>20 - Tributação com ICMS e acréscimo de ST com
                                                    direito a crédito</option>
                                                <option value="30" @if(old('cst') == "30") selected @endif>30 - Tributação simplificada (sem direito a crédito)
                                                </option>
                                                <option value="40" @if(old('cst') == "40") selected @endif>40 - Tributação simplificada com acréscimo de ST
                                                </option>
                                                <option value="41" @if(old('cst') == "41") selected @endif>41 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária</option>
                                                <option value="50" @if(old('cst') == "50") selected @endif>50 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária com direito a crédito</option>
                                                <option value="51" @if(old('cst') == "51") selected @endif>51 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária sem direito a crédito</option>
                                                <option value="60" @if(old('cst') == "60") selected @endif>60 - Tributação com ICMS e acréscimo de ST por
                                                    Substituição Tributária com acréscimo</option>
                                                <option value="70" @if(old('cst') == "70") selected @endif>70 - Redução de base de cálculo e cobrança do ICMS
                                                    por substituição tributária</option>
                                                <option value="90" @if(old('cst') == "90") selected @endif>90 - Outras operações</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_pis">CST/PIS</label>
                                            <select class="form-control" name="cst_pis" id="cst_pis" required>
                                                <option value="">--Selecione o CST/PIS--</option>
                                                <option value="1" @if(old('cst_pis') == "1") selected @endif>1 - Operação Tributável com Alíquota Básica</option>
                                                <option value="2" @if(old('cst_pis') == "2") selected @endif>2 - Operação Tributável com Alíquota Diferenciada
                                                </option>
                                                <option value="3" @if(old('cst_pis') == "3") selected @endif>3 - Operação Tributável com Alíquota por Unidade de
                                                    Medida de Produto</option>
                                                <option value="4" @if(old('cst_pis') == "4") selected @endif>4 - Operação Tributável Monofásica – Revenda a
                                                    Alíquota Zero</option>
                                                <option value="5" @if(old('cst_pis') == "5") selected @endif>5 - Operação Tributável por Substituição Tributária
                                                </option>
                                                <option value="6" @if(old('cst_pis') == "6") selected @endif>6 - Operação Tributável a Alíquota Zero</option>
                                                <option value="7" @if(old('cst_pis') == "7") selected @endif>7 - Operação Isenta da Contribuição</option>
                                                <option value="8" @if(old('cst_pis') == "8") selected @endif>8 - Operação sem Incidência da Contribuição</option>
                                                <option value="9" @if(old('cst_pis') == "9") selected @endif>9 - Operação com Suspensão da Contribuição</option>
                                                <option value="49" @if(old('cst_pis') == "49") selected @endif>49 - Outras Operações de Saída</option>
                                                <option value="50" @if(old('cst_pis') == "50") selected @endif>50 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="51" @if(old('cst_pis') == "51") selected @endif>51 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                                <option value="52" @if(old('cst_pis') == "52") selected @endif>52 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita de Exportação</option>
                                                <option value="53" @if(old('cst_pis') == "53") selected @endif>53 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                                <option value="54" @if(old('cst_pis') == "54") selected @endif>54 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="55" @if(old('cst_pis') == "55") selected @endif>55 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="56" @if(old('cst_pis') == "56") selected @endif>56 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="60" @if(old('cst_pis') == "60") selected @endif>60 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="61" @if(old('cst_pis') == "61") selected @endif>61 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                                </option>
                                                <option value="62" @if(old('cst_pis') == "62") selected @endif>62 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita de Exportação</option>
                                                <option value="63" @if(old('cst_pis') == "63") selected @endif>63 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                                </option>
                                                <option value="64" @if(old('cst_pis') == "64") selected @endif>64 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="65" @if(old('cst_pis') == "65") selected @endif>65 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                                </option>
                                                <option value="66" @if(old('cst_pis') == "66") selected @endif>66 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                    de Exportação</option>
                                                <option value="67" @if(old('cst_pis') == "67") selected @endif>67 - Crédito Presumido – Outras Operações</option>
                                                <option value="70" @if(old('cst_pis') == "70") selected @endif>70 - Operação de Aquisição sem Direito a Crédito
                                                </option>
                                                <option value="71" @if(old('cst_pis') == "71") selected @endif>71 - Operação de Aquisição com Isenção</option>
                                                <option value="72" @if(old('cst_pis') == "72") selected @endif>72 - Operação de Aquisição com Suspensão</option>
                                                <option value="73" @if(old('cst_pis') == "73") selected @endif>73 - Operação de Aquisição a Alíquota Zero</option>
                                                <option value="74" @if(old('cst_pis') == "74") selected @endif>74 - Operação de Aquisição sem Incidência da
                                                    Contribuição</option>
                                                <option value="75" @if(old('cst_pis') == "75") selected @endif>75 - Operação de Aquisição por Substituição
                                                    Tributária</option>
                                                <option value="98" @if(old('cst_pis') == "98") selected @endif>98 - Outras Operações de Entrada</option>
                                                <option value="99" @if(old('cst_pis') == "99") selected @endif>99 - Outras Operações</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_cofins">CST/COFINS</label>
                                            <select class="form-control" name="cst_cofins" id="cst_cofins" required>
                                                <option value="">--Selecione o CST/COFINS--</option>
                                                <option value="1" @if(old('cst_cofins') == "1") selected @endif>1 - Operação Tributável com Alíquota Básica</option>
                                                <option value="2" @if(old('cst_cofins') == "2") selected @endif>2 - Operação Tributável com Alíquota Diferenciada
                                                </option>
                                                <option value="3" @if(old('cst_cofins') == "3") selected @endif>3 - Operação Tributável com Alíquota por Unidade de
                                                    Medida de Produto</option>
                                                <option value="4" @if(old('cst_cofins') == "4") selected @endif>4 - Operação Tributável Monofásica – Revenda a
                                                    Alíquota Zero</option>
                                                <option value="5" @if(old('cst_cofins') == "5") selected @endif>5 - Operação Tributável por Substituição Tributária
                                                </option>
                                                <option value="6" @if(old('cst_cofins') == "6") selected @endif>6 - Operação Tributável a Alíquota Zero</option>
                                                <option value="7" @if(old('cst_cofins') == "7") selected @endif>7 - Operação Isenta da Contribuição</option>
                                                <option value="8" @if(old('cst_cofins') == "8") selected @endif>8 - Operação sem Incidência da Contribuição</option>
                                                <option value="9" @if(old('cst_cofins') == "9") selected @endif>9 - Operação com Suspensão da Contribuição</option>
                                                <option value="49" @if(old('cst_cofins') == "49") selected @endif>49 - Outras Operações de Saída</option>
                                                <option value="50" @if(old('cst_cofins') == "50") selected @endif>50 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="51" @if(old('cst_cofins') == "51") selected @endif>51 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                                <option value="52" @if(old('cst_cofins') == "52") selected @endif>52 - Operação com Direito a Crédito – Vinculada
                                                    Exclusivamente a Receita de Exportação</option>
                                                <option value="53" @if(old('cst_cofins') == "53") selected @endif>53 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                                <option value="54" @if(old('cst_cofins') == "54") selected @endif>54 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="55" @if(old('cst_cofins') == "55") selected @endif>55 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                                <option value="56" @if(old('cst_cofins') == "56") selected @endif>56 - Operação com Direito a Crédito – Vinculada a
                                                    Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="60" @if(old('cst_cofins') == "60") selected @endif>60 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                                <option value="61" @if(old('cst_cofins') == "61") selected @endif>61 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                                </option>
                                                <option value="62" @if(old('cst_cofins') == "62") selected @endif>62 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada Exclusivamente a Receita de Exportação</option>
                                                <option value="63" @if(old('cst_cofins') == "63") selected @endif>63 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                                </option>
                                                <option value="64" @if(old('cst_cofins') == "64") selected @endif>64 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                                </option>
                                                <option value="65" @if(old('cst_cofins') == "65") selected @endif>65 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                                </option>
                                                <option value="66" @if(old('cst_cofins') == "66") selected @endif>66 - Crédito Presumido – Operação de Aquisição
                                                    Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                    de Exportação</option>
                                                <option value="67" @if(old('cst_cofins') == "67") selected @endif>67 - Crédito Presumido – Outras Operações</option>
                                                <option value="70" @if(old('cst_cofins') == "70") selected @endif>70 - Operação de Aquisição sem Direito a Crédito
                                                </option>
                                                <option value="71" @if(old('cst_cofins') == "71") selected @endif>71 - Operação de Aquisição com Isenção</option>
                                                <option value="72" @if(old('cst_cofins') == "72") selected @endif>72 - Operação de Aquisição com Suspensão</option>
                                                <option value="73" @if(old('cst_cofins') == "73") selected @endif>73 - Operação de Aquisição a Alíquota Zero</option>
                                                <option value="74" @if(old('cst_cofins') == "74") selected @endif>74 - Operação de Aquisição sem Incidência da
                                                    Contribuição</option>
                                                <option value="75" @if(old('cst_cofins') == "75") selected @endif>75 - Operação de Aquisição por Substituição
                                                    Tributária</option>
                                                <option value="98" @if(old('cst_cofins') == "98") selected @endif>98 - Outras Operações de Entrada</option>
                                                <option value="99" @if(old('cst_cofins') == "99") selected @endif>99 - Outras Operações</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cofins">COFINS</label>
                                            <input class="form-control" type="text" name="cofins" id="cofins"
                                                value="{{ old('cofins') ?? 00 }}" required placeholder="Confins...">
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="icms">ICMS</label>
                                            <input class="form-control" type="text" name="icms" id="icms"
                                                value="{{ old('icms') ?? 17 }}" required placeholder="Icms...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_csosn">CST/CSOSN</label>
                                            <select class="form-control" name="cst_csosn" id="cst_csosn" required>
                                                <option value="">--Selecione o CST/CSOSN--</option>
                                                <option value="101" @if(old('cst_csosn') == "101") selected @endif>101 - Tributada pelo Simples Nacional com permissão
                                                    de crédito</option>
                                                <option value="102" @if(old('cst_csosn') == "102") selected @endif>102 - Tributada pelo Simples Nacional sem permissão
                                                    de crédito</option>
                                                <option value="103" @if(old('cst_csosn') == "103") selected @endif>103 - Isenção do ICMS no Simples Nacional para faixa
                                                    de receita bruta</option>
                                                <option value="201" @if(old('cst_csosn') == "201") selected @endif>201 - Tributada pelo Simples Nacional com permissão
                                                    de crédito e com cobrança do ICMS por substituição tributária</option>
                                                <option value="202" @if(old('cst_csosn') == "202") selected @endif>202 - Tributada pelo Simples Nacional sem permissão
                                                    de crédito e com cobrança do ICMS por substituição tributária</option>
                                                <option value="203" @if(old('cst_csosn') == "203") selected @endif>203 - Isenção do ICMS no Simples Nacional para faixa
                                                    de receita bruta e com cobrança do ICMS por substituição tributária
                                                </option>
                                                <option value="300" @if(old('cst_csosn') == "300") selected @endif>300 - Imune</option>
                                                <option value="400" @if(old('cst_csosn') == "400") selected @endif>400 - Não tributada pelo Simples Nacional</option>
                                                <option value="500" @if(old('cst_csosn') == "500") selected @endif>500 - ICMS cobrado anteriormente por substituição
                                                    tributária (substituído) ou por antecipação</option>
                                                <option value="900" @if(old('cst_csosn') == "900") selected @endif>900 - Outros</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="pis">PIS</label>
                                            <input class="form-control" type="text" name="pis" id="pis"
                                                value="{{ old('pis') ?? 00 }}" required placeholder="Pis...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="ipi">IPI</label>
                                            <input type="text" class="form-control" name="ipi" id="ipi"
                                                value="{{ old('ipi') ?? 00 }}" required placeholder="Ipi...">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="tpVeic" class="col-sm-6 col-form-label">Tipo de Veículo</label>
                                            <select class="form-control" name="tpVeic" id="tpVeic">
                                                <option value="02" @if(old('tpVeic') == "02") selected @endif>CICLOMOTOR</option>
                                                <option value="03" @if(old('tpVeic') == "03") selected @endif>MOTONETA</option>
                                                <option value="04" @if(old('tpVeic') == "04") selected @endif>MOTOCICLO</option>
                                                <option value="05" @if(old('tpVeic') == "05") selected @endif>TRICICLO</option>
                                                <option value="06" @if(old('tpVeic') == "06") selected @endif>AUTOMÓVEL</option>
                                                <option value="07" @if(old('tpVeic') == "07") selected @endif>MICROÔNIBUS</option>
                                                <option value="08" @if(old('tpVeic') == "07") selected @endif>ÔNIBUS</option>
                                                <option value="10" @if(old('tpVeic') == "10") selected @endif>REBOQUE</option>
                                                <option value="11" @if(old('tpVeic') == "11") selected @endif>SEMIREBOQUE</option>
                                                <option value="13" @if(old('tpVeic') == "13") selected @endif>CAMINHONETA</option>
                                                <option value="14" @if(old('tpVeic') == "14") selected @endif>CAMINHÃO</option>
                                                <option value="17" @if(old('tpVeic') == "17") selected @endif>C.TRATOR</option>
                                                <option value="22" @if(old('tpVeic') == "22") selected @endif>ESP/ÔNIBUS</option>
                                                <option value="23" @if(old('tpVeic') == "23") selected @endif>MISTO/CAM</option>
                                                <option value="24" @if(old('tpVeic') == "24") selected @endif>CARGA/CAM</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="chassiVeic" class="col-sm-2 col-form-label">Chassi</label>
                                            <input type="text" class="form-control" id="chassiVeic" name="chassiVeic"
                                                oninput="this.value = this.value.toUpperCase()" value="{{ old('chassVeic') }}"
                                                placeholder="Chassi...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="renavanVeic" class="col-sm-4 col-form-label">Renavan</label>
                                            <input type="text" class="form-control" id="renavanVeic"
                                                name="renavanVeic" value="{{ old('renavanVeic') }}" placeholder="Renavan...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="anoFabVeic" class="col-sm-6 col-form-label">Ano de
                                                Fabricação</label>
                                            <input type="number" class="form-control" id="anoFabVeic" name="anoFabVeic"
                                                min="1950" max="{{ date('Y') }}" value="{{ old('anoFabVeic') ?? date('Y') }}"
                                                placeholder="Ano de Fabricação...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="anoModVeic" class="col-sm-6 col-form-label">Ano de Modelo</label>
                                            <input type="number" class="form-control" id="anoModVeic" name="anoModVeic"
                                                min="1950" max="{{ date('Y') + 1 }}" value="{{ old('anoModVeic') ?? date('Y') }}"
                                                placeholder="Ano de Modelo...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="pesoLVeic" class="col-sm-4 col-form-label">Peso Líquido</label>
                                            <input type="number" class="form-control" step="0.01" id="pesoLVeic"
                                                name="pesoLVeic" value="{{ old('pesoLVeic') }}" placeholder="Peso Líquido...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pesoBVeic" class="col-sm-4 col-form-label">Peso Bruto</label>
                                            <input type="number" class="form-control" id="pesoBVeic" name="pesoBVeic"
                                                value="{{ old('pesoBVeic') }}" placeholder="Peso Bruto...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="distVeic" class="col-sm-4 col-form-label">Distância</label>
                                            <input type="text" class="form-control" id="distVeic" name="distVeic"
                                                value="{{ old('distVeic') }}" placeholder="Distância...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label for="combVeic" class="col-sm-4 col-form-label">Combustível</label>
                                            <select class="form-control" name="combVeic" id="combVeic">
                                                <option value="1" @if(old('combVeic') == "1") selected @endif>ÁLCOOL</option>
                                                <option value="2" @if(old('combVeic') == "2") selected @endif>GASOLINA</option>
                                                <option value="3" @if(old('combVeic') == "3") selected @endif>DIESEL</option>
                                                <option value="16" @if(old('combVeic') == "16") selected @endif>ÁLCOOL/GASOLINA</option>
                                                <option value="17" @if(old('combVeic') == "17") selected @endif>GASOLINA/ÁLCOOL/GNV</option>
                                                <option value="18" @if(old('combVeic') == "18") selected @endif>GASOLINA/ELÉTRICO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-7">
                                            <label for="nMotorVeic" class="col-sm-4 col-form-label">Número do
                                                Motor</label>
                                            <input type="text" class="form-control" id="nMotorVeic" name="nMotorVeic"
                                                value="{{ old('nMotorVeic') }}" placeholder="Número do Motor..." oninput="this.value = this.value.toUpperCase()">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="cvVeic" class="col-sm-4 col-form-label">Potência</label>
                                            <input type="number" step="0.01" class="form-control" id="cvVeic"
                                                name="cvVeic" value="{{ old('cvVeic') }}" placeholder="Potência...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cm3Veic" class="col-sm-6 col-form-label">Cilindradas</label>
                                            <input type="number" step="0.01" class="form-control" id="cm3Veic"
                                                name="cm3Veic" value="{{ old('cm3Veic') }}" placeholder="Cilindradas...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="serieVeic" class="col-sm-4 col-form-label">Série</label>
                                            <input type="text" class="form-control" id="serieVeic" name="serieVeic"
                                                value="{{ old('serieVeic') }}" placeholder="Série...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="tpPVeic" class="col-sm-6 col-form-label">Tipo de Pintura</label>
                                            <input type="text" class="form-control" id="tpPVeic" name="tpPVeic"
                                                value="{{ old('tpPVeic') }}" placeholder="Tipo de Pintura...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="corVeic" class="col-sm-2 col-form-label">Cor</label>
                                            <input type="text" class="form-control" id="corVeic" name="corVeic"
                                                oninput="this.value = this.value.toUpperCase()" value="{{ old('corVeic') }}" placeholder="Cor...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cCorVeic" class="col-sm-6 col-form-label">Código de Cor</label>
                                            <select class="form-control" name="cCorVeic" id="cCorVeic">
                                                <option value="01" @if(old('cCorVeic') == "01") selected @endif>AMARELO</option>
                                                <option value="02" @if(old('cCorVeic') == "02") selected @endif>AZUL</option>
                                                <option value="03" @if(old('cCorVeic') == "03") selected @endif>BEGE</option>
                                                <option value="04" @if(old('cCorVeic') == "04") selected @endif>BRANCA</option>
                                                <option value="05" @if(old('cCorVeic') == "05") selected @endif>CINZA</option>
                                                <option value="06" @if(old('cCorVeic') == "06") selected @endif>DOURADA</option>
                                                <option value="07" @if(old('cCorVeic') == "07") selected @endif>GRENAR</option>
                                                <option value="08" @if(old('cCorVeic') == "08") selected @endif>LARANJA</option>
                                                <option value="09" @if(old('cCorVeic') == "09") selected @endif>MARROM</option>
                                                <option value="10" @if(old('cCorVeic') == "10") selected @endif>PRATA</option>
                                                <option value="11" @if(old('cCorVeic') == "11") selected @endif>PRETA</option>
                                                <option value="12" @if(old('cCorVeic') == "12") selected @endif>ROSA</option>
                                                <option value="13" @if(old('cCorVeic') == "13") selected @endif>ROXA</option>
                                                <option value="14" @if(old('cCorVeic') == "14") selected @endif>VERDE</option>
                                                <option value="15" @if(old('cCorVeic') == "15") selected @endif>VERMELHA</option>
                                                <option value="16" @if(old('cCorVeic') == "16") selected @endif>FANTASIA</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="cCorMontVeic" class="col-sm-8 col-form-label">Código de Cor
                                                Montadora</label>
                                            <input type="text" class="form-control" id="cCorMontVeic"
                                                name="cCorMontVeic" value="{{ old('cCorMontVeic') }}"
                                                placeholder="Código de Cor Montadora...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cMarcaVeic" class="col-sm-8 col-form-label">Código da
                                                Marca</label>
                                            <input type="text" class="form-control" id="cMarcaVeic" name="cMarcaVeic"
                                                value="{{ old('cMarcaVeic') }}" placeholder="Código da Marca...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="condVeic" class="col-sm-8 col-form-label">Condição do
                                                Veículo</label>
                                            <select class="form-control" name="condVeic" id="condVeic">
                                                <option value="0" @if(old('condVeic') == "0") selected @endif>ACABADO</option>
                                                <option value="1" @if(old('condVeic') == "1") selected @endif>INACABADO</option>
                                                <option value="2" @if(old('condVeic') == "2") selected @endif>SEMIACABO</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="espVeic" class="col-sm-8 col-form-label">Especificação do
                                                Veículo</label>
                                            <select class="form-control" name="espVeic" id="espVeic">
                                                <option value="1" @if(old('espVeic') == "1") selected @endif>PASSAGEIRO</option>
                                                <option value="2" @if(old('espVeic') == "2") selected @endif>CARGA</option>
                                                <option value="3" @if(old('espVeic') == "3") selected @endif>MISTO</option>
                                                <option value="4" @if(old('espVeic') == "4") selected @endif>CORRIDA</option>
                                                <option value="5" @if(old('espVeic') == "5") selected @endif>TRAÇÃO</option>
                                                <option value="6" @if(old('espVeic') == "6") selected @endif>ESPECIAL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="vinVeic" class="col-sm-6 col-form-label">VIN do Veículo</label>
                                            <select class="form-control" name="vinVeic" id="vinVeic">
                                                <option value="N" @if(old('vinVeic') == "N") selected @endif>NORMAL</option>
                                                <option value="R" @if(old('vinVeic') == "R") selected @endif>REMARCADO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="lotVeic" class="col-sm-6 col-form-label">Lotação Máxima</label>
                                            <input type="text" class="form-control" id="lotVeic" name="lotVeic"
                                                value="{{ old('lotVeic') }}" placeholder="Lotação Máxima...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label for="restriVeic" class="col-sm-6 col-form-label">Restrição do
                                                Veículo</label>
                                            <select class="form-control" name="restriVeic" id="restriVeic">
                                                <option value="0" @if(old('restriVeic') == "0") selected @endif>NÃO HÁ</option>
                                                <option value="1" @if(old('restriVeic') == "1") selected @endif>ALIENAÇÃO FIDUNCIÁRIA</option>
                                                <option value="2" @if(old('restriVeic') == "2") selected @endif>ARRENDAMENTO MERCANTIL</option>
                                                <option value="3" @if(old('restriVeic') == "3") selected @endif>RESERVA DE DOMÍNIO</option>
                                                <option value="4" @if(old('restriVeic') == "4") selected @endif>PENHOR DE VEÍCULOS</option>
                                                <option value="9" @if(old('restriVeic') == "9") selected @endif>OUTRAS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cargaVeic" class="col-sm-12 col-form-label">Carga Máxima</label>
                                            <input type="number" class="form-control" id="cargaVeic" name="cargaVeic"
                                                value="{{ old('cargaVeic') }}" placeholder="Carga Máxima...">
                                        </div>
                                        <div class="col-md-5">
                                            <label for="operVeic" class="col-sm-8 col-form-label">Tipo de Operação</label>
                                            <select class="form-control" name="operVeic" id="operVeic">
                                                <option value="1" @if(old('operVeic') == "1") selected @endif>VENDA CONCERSSIONÁRIA</option>
                                                <option value="2" @if(old('operVeic') == "2") selected @endif>FATURAMENTO DIRETO PARA CONSUMIDOR FINAL</option>
                                                <option value="3" @if(old('operVeic') == "3") selected @endif>VENDA DIRETO PARA GRANDES CONSUMIDORES</option>
                                                <option value="0" @if(old('operVeic') == "0") selected @endif>OUTRAS</option>
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
            var campos = document.querySelectorAll('.form-control')
            if (checkbox.checked) {
                campos.forEach(function(campo) {
                    campo.required = true
                });
                document.getElementById('veicTab').style.display = 'block'
            } else {
                campos.forEach(function(campo) {
                    campo.required = false
                });
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
