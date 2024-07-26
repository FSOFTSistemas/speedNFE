@extends('adminlte::page')

@section('title', 'Cadastro de Produtos')

@section('content_header')
    <div class="text-center">
        <h3 class="m-0 text-dark">Cadastro de Produtos</h3>
    </div>
@stop

@section('content')
    <div class="text-right">
        <a class="btn btn-secondary mb-3" href="{{ route('produto.index') }}">Voltar</a>
    </div>

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
                <li class="nav-item" style="display: none" id="veicTab">
                    <a class="nav-link" id="veic-tab" data-toggle="pill" href="#veic" role="tab" aria-controls="veic"
                        aria-selected="false"><b>Informações de Veículo</b></a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <form class="needs-validation" novalidate method="POST" action="{{ route('salvar_produto') }}">
                @csrf

                <div class="tab-content" id="tabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">
                            <div class="col-md-6 col-xs-10">
                                @if ($user->empresa_id == 1)
                                    <div class="input-group has-validation mb-2">
                                        <div class="form-floating">
                                            <select onchange="javascript:liberarProdutos({{ $user->empresa_id }})"
                                                class="form-select" name="empresa" id="empresa" required>
                                                <option value="">Selecione uma Empresa</option>
                                                @foreach ($empresas as $emp)
                                                    <option value="{{ $emp->id }}"
                                                        @if (old('empresa') == $emp->id) selected @endif>
                                                        {{ $emp->fantasia }}</option>
                                                @endforeach
                                            </select>
                                            <label>Empresa</label>
                                            <div class="invalid-feedback">
                                                Informe um tipo válido.
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <input onload="javascript:liberarProdutos({{ $user->empresa_id }})" type="hidden"
                                        value="{{ $user->empresa_id }}">
                                @endif
                            </div>

                            <div class="col-md-6 col-xs-10">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="categoria" id="categoria" required>
                                            <option value="">Selecione uma Categoria</option>
                                            @foreach ($categorias as $categoria)
                                                <option id="{{ $categoria->empresa_id }}" value="{{ $categoria->id }}"
                                                    @if (old('categoria') == $categoria->id) selected @endif>
                                                    {{ $categoria->descricao }}</option>
                                            @endforeach
                                        </select>
                                        <label>Categoria</label>
                                        <div class="invalid-feedback">
                                            Informe uma categoria válida.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 col-8">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="text" name="codigo" id="codigo"
                                            placeholder=" " oninput="this.value = this.value.toUpperCase()"
                                            value="{{ old('codigo') }}">
                                        <label for="codigo">Código de Barras</label>
                                        <div class="invalid-feedback">
                                            Informe um código de barras válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-4">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="number" name="estoque" id="estoque"
                                            placeholder="Estoque..." value="{{ old('estoque') ?? 1 }}">
                                        <label>Estoque</label>
                                        <div class="invalid-feedback">
                                            Informe um estoque válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="text" name="produto" id="produto" required
                                            placeholder="Produto..." oninput="this.value = this.value.toUpperCase()"
                                            value="{{ old('produto') }}">
                                        <label for="produto">Produto</label>
                                        <div class="invalid-feedback">
                                            Informe um nome de produto válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-xs-10">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="text" name="ncm" id="ncm"
                                            required placeholder="Ncm..." value="{{ old('ncm') }}">
                                        <label for="ncm">NCM</label>
                                        <div class="invalid-feedback">
                                            Informe um NCM válido.
                                        </div>
                                    </div>
                                    <button title="Buscar Ncm" class="btn btn-dark" type="button" data-toggle="modal"
                                        data-target="#NcmModal"><i class="fa fa-search"></i></button>
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-10">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="number" name="precocusto" id="precocusto"
                                            step="0.01" required placeholder="Preço Custo..."
                                            value="{{ old('precocusto') }}">
                                        <label for="precocusto">Preço de Custo</label>
                                        <div class="invalid-feedback">
                                            Informe um preço de custo válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="number" name="precovenda" step="0.01"
                                            id="precovenda" required placeholder="Preço Venda..."
                                            value="{{ old('precovenda') }}">
                                        <label for="precovenda">Preço de Venda</label>
                                        <div class="invalid-feedback">
                                            Informe um preço de venda válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select name="un" id="un" class="form-select" required>
                                            <option value="">Selecione uma Unidade</option>
                                            <option value="un" @if (old('un') == 'un') selected @endif>UN
                                            </option>
                                            <option value="cx" @if (old('un') == 'cx') selected @endif>CX
                                            </option>
                                            <option value="kg" @if (old('un') == 'kg') selected @endif>KG
                                            </option>
                                            <option value="l" @if (old('un') == 'l') selected @endif>L
                                            </option>
                                            <option value="ml" @if (old('un') == 'ml') selected @endif>ML
                                            </option>
                                            <option value="m" @if (old('un') == 'm') selected @endif>M
                                            </option>
                                            <option value="cm" @if (old('un') == 'cm') selected @endif>CM
                                            </option>
                                        </select>
                                        <label for="un">Unidade</label>
                                        <div class="invalid-feedback">
                                            Informe uma unidade válida.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-check p-3">
                                        <input type="checkbox" name="tpProd" id="tpProd" onclick="checkVeic(this)"
                                            @if (old('tpProd')) checked @endif>
                                        <label class="form-check-label text-bold" for="veic">
                                            Veículo?
                                        </label>
                                    </div>
                                    <div class="invalid-feedback">
                                        Informe se é veículo.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="cfopinterno" id="cfopinterno" required>
                                            <option value="">Selecione o CFOP Interno</option>
                                            @foreach ($cfops as $cfop)
                                                <option value="{{ $cfop->cfop }}"
                                                    @if (old('cfopinterno') == $cfop->cfop) selected @endif>
                                                    {{ $cfop->cfop }} -
                                                    {{ $cfop->natureza }}</option>
                                            @endforeach
                                        </select>
                                        <label for="cfopinterno">CFOP Interno</label>
                                        <div class="invalid-feedback">
                                            Informe um CFOP interno válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="cfopexterno" id="cfopexterno" required>
                                            <option value="">Selecione o CFOP Externo</option>
                                            @foreach ($cfops as $cfop)
                                                <option value="{{ $cfop->cfop }}"
                                                    @if (old('cfopexterno') == $cfop->cfop) selected @endif>
                                                    {{ $cfop->cfop }} -
                                                    {{ $cfop->natureza }}</option>
                                            @endforeach
                                        </select>
                                        <label for="cfopexterno">CFOP Externo</label>
                                        <div class="invalid-feedback">
                                            Informe um CFOP externo válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-xs-10">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="cst" id="cst" required>
                                            <option value="">Selecione o CST</option>
                                            <option value="00" @if (old('cst') == '00') selected @endif>
                                                00 - Tributação integral</option>
                                            <option value="10" @if (old('cst') == '10') selected @endif>
                                                10 - Tributação com ICMS e acréscimo de ST</option>
                                            <option value="20" @if (old('cst') == '20') selected @endif>
                                                20 - Tributação com ICMS e acréscimo de ST com
                                                direito a crédito</option>
                                            <option value="30" @if (old('cst') == '30') selected @endif>
                                                30 - Tributação simplificada (sem direito a crédito)
                                            </option>
                                            <option value="40" @if (old('cst') == '40') selected @endif>
                                                40 - Tributação simplificada com acréscimo de ST
                                            </option>
                                            <option value="41" @if (old('cst') == '41') selected @endif>
                                                41 - Tributação com ICMS e acréscimo de ST por
                                                Substituição Tributária</option>
                                            <option value="50" @if (old('cst') == '50') selected @endif>
                                                50 - Tributação com ICMS e acréscimo de ST por
                                                Substituição Tributária com direito a crédito</option>
                                            <option value="51" @if (old('cst') == '51') selected @endif>
                                                51 - Tributação com ICMS e acréscimo de ST por
                                                Substituição Tributária sem direito a crédito</option>
                                            <option value="60" @if (old('cst') == '60') selected @endif>
                                                60 - Tributação com ICMS e acréscimo de ST por
                                                Substituição Tributária com acréscimo</option>
                                            <option value="70" @if (old('cst') == '70') selected @endif>
                                                70 - Redução de base de cálculo e cobrança do ICMS
                                                por substituição tributária</option>
                                            <option value="90" @if (old('cst') == '90') selected @endif>
                                                90 - Outras operações</option>
                                        </select>
                                        <label for="cst">CST</label>
                                        <div class="invalid-feedback">
                                            Informe um CST válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-10">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="cst_pis" id="cst_pis" required>
                                            <option value="">Selecione o CST/PIS</option>
                                            <option value="1" @if (old('cst_pis') == '1') selected @endif>
                                                1 - Operação Tributável com Alíquota Básica</option>
                                            <option value="2" @if (old('cst_pis') == '2') selected @endif>
                                                2 - Operação Tributável com Alíquota Diferenciada
                                            </option>
                                            <option value="3" @if (old('cst_pis') == '3') selected @endif>
                                                3 - Operação Tributável com Alíquota por Unidade de
                                                Medida de Produto</option>
                                            <option value="4" @if (old('cst_pis') == '4') selected @endif>
                                                4 - Operação Tributável Monofásica – Revenda a
                                                Alíquota Zero</option>
                                            <option value="5" @if (old('cst_pis') == '5') selected @endif>
                                                5 - Operação Tributável por Substituição Tributária
                                            </option>
                                            <option value="6" @if (old('cst_pis') == '6') selected @endif>
                                                6 - Operação Tributável a Alíquota Zero</option>
                                            <option value="7" @if (old('cst_pis') == '7') selected @endif>
                                                7 - Operação Isenta da Contribuição</option>
                                            <option value="8" @if (old('cst_pis') == '8') selected @endif>
                                                8 - Operação sem Incidência da Contribuição</option>
                                            <option value="9" @if (old('cst_pis') == '9') selected @endif>
                                                9 - Operação com Suspensão da Contribuição</option>
                                            <option value="49" @if (old('cst_pis') == '49') selected @endif>
                                                49 - Outras Operações de Saída</option>
                                            <option value="50" @if (old('cst_pis') == '50') selected @endif>
                                                50 - Operação com Direito a Crédito – Vinculada
                                                Exclusivamente a Receita Tributada no Mercado Interno</option>
                                            <option value="51" @if (old('cst_pis') == '51') selected @endif>
                                                51 - Operação com Direito a Crédito – Vinculada
                                                Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                            <option value="52" @if (old('cst_pis') == '52') selected @endif>
                                                52 - Operação com Direito a Crédito – Vinculada
                                                Exclusivamente a Receita de Exportação</option>
                                            <option value="53" @if (old('cst_pis') == '53') selected @endif>
                                                53 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                            <option value="54" @if (old('cst_pis') == '54') selected @endif>
                                                54 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Tributadas no Mercado Interno e de Exportação</option>
                                            <option value="55" @if (old('cst_pis') == '55') selected @endif>
                                                55 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                            <option value="56" @if (old('cst_pis') == '56') selected @endif>
                                                56 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                            </option>
                                            <option value="60" @if (old('cst_pis') == '60') selected @endif>
                                                60 - Crédito Presumido – Operação de Aquisição
                                                Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                            <option value="61" @if (old('cst_pis') == '61') selected @endif>
                                                61 - Crédito Presumido – Operação de Aquisição
                                                Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                            </option>
                                            <option value="62" @if (old('cst_pis') == '62') selected @endif>
                                                62 - Crédito Presumido – Operação de Aquisição
                                                Vinculada Exclusivamente a Receita de Exportação</option>
                                            <option value="63" @if (old('cst_pis') == '63') selected @endif>
                                                63 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                            </option>
                                            <option value="64" @if (old('cst_pis') == '64') selected @endif>
                                                64 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                            </option>
                                            <option value="65" @if (old('cst_pis') == '65') selected @endif>
                                                65 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                            </option>
                                            <option value="66" @if (old('cst_pis') == '66') selected @endif>
                                                66 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                de Exportação</option>
                                            <option value="67" @if (old('cst_pis') == '67') selected @endif>
                                                67 - Crédito Presumido – Outras Operações</option>
                                            <option value="70" @if (old('cst_pis') == '70') selected @endif>
                                                70 - Operação de Aquisição sem Direito a Crédito
                                            </option>
                                            <option value="71" @if (old('cst_pis') == '71') selected @endif>
                                                71 - Operação de Aquisição com Isenção</option>
                                            <option value="72" @if (old('cst_pis') == '72') selected @endif>
                                                72 - Operação de Aquisição com Suspensão</option>
                                            <option value="73" @if (old('cst_pis') == '73') selected @endif>
                                                73 - Operação de Aquisição a Alíquota Zero</option>
                                            <option value="74" @if (old('cst_pis') == '74') selected @endif>
                                                74 - Operação de Aquisição sem Incidência da
                                                Contribuição</option>
                                            <option value="75" @if (old('cst_pis') == '75') selected @endif>
                                                75 - Operação de Aquisição por Substituição
                                                Tributária</option>
                                            <option value="98" @if (old('cst_pis') == '98') selected @endif>
                                                98 - Outras Operações de Entrada</option>
                                            <option value="99" @if (old('cst_pis') == '99') selected @endif>
                                                99 - Outras Operações</option>
                                        </select>
                                        <label for="cst_pis">CST/PIS</label>
                                        <div class="invalid-feedback">
                                            Informe um CST/PIS válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-xs-10">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="cst_cofins" id="cst_cofins" required>
                                            <option value="">Selecione o CST/COFINS</option>
                                            <option value="1" @if (old('cst_cofins') == '1') selected @endif>
                                                1 - Operação Tributável com Alíquota Básica</option>
                                            <option value="2" @if (old('cst_cofins') == '2') selected @endif>
                                                2 - Operação Tributável com Alíquota Diferenciada
                                            </option>
                                            <option value="3" @if (old('cst_cofins') == '3') selected @endif>
                                                3 - Operação Tributável com Alíquota por Unidade de
                                                Medida de Produto</option>
                                            <option value="4" @if (old('cst_cofins') == '4') selected @endif>
                                                4 - Operação Tributável Monofásica – Revenda a
                                                Alíquota Zero</option>
                                            <option value="5" @if (old('cst_cofins') == '5') selected @endif>
                                                5 - Operação Tributável por Substituição Tributária
                                            </option>
                                            <option value="6" @if (old('cst_cofins') == '6') selected @endif>
                                                6 - Operação Tributável a Alíquota Zero</option>
                                            <option value="7" @if (old('cst_cofins') == '7') selected @endif>
                                                7 - Operação Isenta da Contribuição</option>
                                            <option value="8" @if (old('cst_cofins') == '8') selected @endif>
                                                8 - Operação sem Incidência da Contribuição</option>
                                            <option value="9" @if (old('cst_cofins') == '9') selected @endif>
                                                9 - Operação com Suspensão da Contribuição</option>
                                            <option value="49" @if (old('cst_cofins') == '49') selected @endif>
                                                49 - Outras Operações de Saída</option>
                                            <option value="50" @if (old('cst_cofins') == '50') selected @endif>
                                                50 - Operação com Direito a Crédito – Vinculada
                                                Exclusivamente a Receita Tributada no Mercado Interno</option>
                                            <option value="51" @if (old('cst_cofins') == '51') selected @endif>
                                                51 - Operação com Direito a Crédito – Vinculada
                                                Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                            <option value="52" @if (old('cst_cofins') == '52') selected @endif>
                                                52 - Operação com Direito a Crédito – Vinculada
                                                Exclusivamente a Receita de Exportação</option>
                                            <option value="53" @if (old('cst_cofins') == '53') selected @endif>
                                                53 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                            <option value="54" @if (old('cst_cofins') == '54') selected @endif>
                                                54 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Tributadas no Mercado Interno e de Exportação</option>
                                            <option value="55" @if (old('cst_cofins') == '55') selected @endif>
                                                55 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                            <option value="56" @if (old('cst_cofins') == '56') selected @endif>
                                                56 - Operação com Direito a Crédito – Vinculada a
                                                Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação
                                            </option>
                                            <option value="60" @if (old('cst_cofins') == '60') selected @endif>
                                                60 - Crédito Presumido – Operação de Aquisição
                                                Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                            <option value="61" @if (old('cst_cofins') == '61') selected @endif>
                                                61 - Crédito Presumido – Operação de Aquisição
                                                Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
                                            </option>
                                            <option value="62" @if (old('cst_cofins') == '62') selected @endif>
                                                62 - Crédito Presumido – Operação de Aquisição
                                                Vinculada Exclusivamente a Receita de Exportação</option>
                                            <option value="63" @if (old('cst_cofins') == '63') selected @endif>
                                                63 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
                                            </option>
                                            <option value="64" @if (old('cst_cofins') == '64') selected @endif>
                                                64 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
                                            </option>
                                            <option value="65" @if (old('cst_cofins') == '65') selected @endif>
                                                65 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Não-Tributadas no Mercado Interno and de Exportação
                                            </option>
                                            <option value="66" @if (old('cst_cofins') == '66') selected @endif>
                                                66 - Crédito Presumido – Operação de Aquisição
                                                Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno and
                                                de Exportação</option>
                                            <option value="67" @if (old('cst_cofins') == '67') selected @endif>
                                                67 - Crédito Presumido – Outras Operações</option>
                                            <option value="70" @if (old('cst_cofins') == '70') selected @endif>
                                                70 - Operação de Aquisição sem Direito a Crédito
                                            </option>
                                            <option value="71" @if (old('cst_cofins') == '71') selected @endif>
                                                71 - Operação de Aquisição com Isenção</option>
                                            <option value="72" @if (old('cst_cofins') == '72') selected @endif>
                                                72 - Operação de Aquisição com Suspensão</option>
                                            <option value="73" @if (old('cst_cofins') == '73') selected @endif>
                                                73 - Operação de Aquisição a Alíquota Zero</option>
                                            <option value="74" @if (old('cst_cofins') == '74') selected @endif>
                                                74 - Operação de Aquisição sem Incidência da
                                                Contribuição</option>
                                            <option value="75" @if (old('cst_cofins') == '75') selected @endif>
                                                75 - Operação de Aquisição por Substituição
                                                Tributária</option>
                                            <option value="98" @if (old('cst_cofins') == '98') selected @endif>
                                                98 - Outras Operações de Entrada</option>
                                            <option value="99" @if (old('cst_cofins') == '99') selected @endif>
                                                99 - Outras Operações</option>
                                        </select>
                                        <label for="cst_cofins">CST/COFINS</label>
                                        <div class="invalid-feedback">
                                            Informe um CST/COFINS válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-10">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="cst_csosn" id="cst_csosn" required>
                                            <option value="">Selecione o CST/CSOSN</option>
                                            <option value="101" @if (old('cst_csosn') == '101') selected @endif>
                                                101 - Tributada pelo Simples Nacional com permissão
                                                de crédito</option>
                                            <option value="102" @if (old('cst_csosn') == '102') selected @endif>
                                                102 - Tributada pelo Simples Nacional sem permissão
                                                de crédito</option>
                                            <option value="103" @if (old('cst_csosn') == '103') selected @endif>
                                                103 - Isenção do ICMS no Simples Nacional para faixa
                                                de receita bruta</option>
                                            <option value="201" @if (old('cst_csosn') == '201') selected @endif>
                                                201 - Tributada pelo Simples Nacional com permissão
                                                de crédito e com cobrança do ICMS por substituição tributária</option>
                                            <option value="202" @if (old('cst_csosn') == '202') selected @endif>
                                                202 - Tributada pelo Simples Nacional sem permissão
                                                de crédito e com cobrança do ICMS por substituição tributária</option>
                                            <option value="203" @if (old('cst_csosn') == '203') selected @endif>
                                                203 - Isenção do ICMS no Simples Nacional para faixa
                                                de receita bruta e com cobrança do ICMS por substituição tributária
                                            </option>
                                            <option value="300" @if (old('cst_csosn') == '300') selected @endif>
                                                300 - Imune</option>
                                            <option value="400" @if (old('cst_csosn') == '400') selected @endif>
                                                400 - Não tributada pelo Simples Nacional</option>
                                            <option value="500" @if (old('cst_csosn') == '500') selected @endif>
                                                500 - ICMS cobrado anteriormente por substituição
                                                tributária (substituído) ou por antecipação</option>
                                            <option value="900" @if (old('cst_csosn') == '900') selected @endif>
                                                900 - Outros</option>
                                        </select>
                                        <label for="cst_csosn">CST/CSOSN</label>
                                        <div class="invalid-feedback">
                                            Informe um CST/CSOSN válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="number" name="icms" id="icms"
                                            value="{{ old('icms') ?? 20.5 }}" required placeholder="Icms...">
                                        <label for="icms">ICMS</label>
                                        <div class="invalid-feedback">
                                            Informe um ICMS válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="text" name="cofins" id="cofins"
                                            value="{{ old('cofins') ?? '00' }}" required placeholder="Confins...">
                                        <label for="cofins">COFINS</label>
                                        <div class="invalid-feedback">
                                            Informe um COFINS válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input class="form-control" type="text" name="pis" id="pis"
                                            value="{{ old('pis') ?? '00' }}" required placeholder="Pis...">
                                        <label for="pis">PIS</label>
                                        <div class="invalid-feedback">
                                            Informe um PIS válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="ipi" id="ipi"
                                            value="{{ old('ipi') ?? '00' }}" required placeholder="Ipi...">
                                        <label for="ipi">IPI</label>
                                        <div class="invalid-feedback">
                                            Informe um IPI válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="tpVeic" id="tpVeic">
                                            <option value="">Selecione um Tipo de Veículo</option>
                                            <option value="02" @if (old('tpVeic') == '02') selected @endif>02
                                                - CICLOMOTOR
                                            </option>
                                            <option value="03" @if (old('tpVeic') == '03') selected @endif>03
                                                - MOTONETA
                                            </option>
                                            <option value="04" @if (old('tpVeic') == '04') selected @endif>04
                                                - MOTOCICLO
                                            </option>
                                            <option value="05" @if (old('tpVeic') == '05') selected @endif>05
                                                - TRICICLO
                                            </option>
                                            <option value="06" @if (old('tpVeic') == '06') selected @endif>06
                                                - AUTOMÓVEL
                                            </option>
                                            <option value="07" @if (old('tpVeic') == '07') selected @endif>07
                                                - MICROÔNIBUS
                                            </option>
                                            <option value="08" @if (old('tpVeic') == '07') selected @endif>07
                                                - ÔNIBUS
                                            </option>
                                            <option value="10" @if (old('tpVeic') == '10') selected @endif>10
                                                - REBOQUE
                                            </option>
                                            <option value="11" @if (old('tpVeic') == '11') selected @endif>11
                                                - SEMIREBOQUE
                                            </option>
                                            <option value="13" @if (old('tpVeic') == '13') selected @endif>13
                                                - CAMINHONETA
                                            </option>
                                            <option value="14" @if (old('tpVeic') == '14') selected @endif>14
                                                - CAMINHÃO
                                            </option>
                                            <option value="17" @if (old('tpVeic') == '17') selected @endif>17
                                                - C.TRATOR
                                            </option>
                                            <option value="22" @if (old('tpVeic') == '22') selected @endif>22
                                                - ESP/ÔNIBUS
                                            </option>
                                            <option value="23" @if (old('tpVeic') == '23') selected @endif>23
                                                - MISTO/CAM
                                            </option>
                                            <option value="24" @if (old('tpVeic') == '24') selected @endif>24
                                                - CARGA/CAM
                                            </option>
                                        </select>
                                        <label for="tpVeic">Tp Veículo</label>
                                        <div class="invalid-feedback">
                                            Informe um tipo de veículo válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="chassiVeic" name="chassiVeic"
                                            oninput="this.value = this.value.toUpperCase()"
                                            value="{{ old('chassVeic') }}" placeholder="Chassi...">
                                        <label for="chassiVeic" class="col-sm-2 col-form-label">Chassi</label>
                                        <div class="invalid-feedback">
                                            Informe um chassi válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="renavanVeic" name="renavanVeic"
                                            value="{{ old('renavanVeic') ?? '000000000' }}" placeholder="Renavan...">
                                        <label for="renavanVeic">Renavan</label>
                                        <div class="invalid-feedback">
                                            Informe um renavan válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="anoFabVeic" name="anoFabVeic"
                                            min="1950" max="{{ date('Y') }}"
                                            value="{{ old('anoFabVeic') ?? date('Y') }}"
                                            placeholder="Ano de Fabricação...">
                                        <label for="anoFabVeic">Ano de
                                            Fab.</label>
                                        <div class="invalid-feedback">
                                            Informe um ano de fabricação válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="anoModVeic" name="anoModVeic"
                                            min="1950" max="{{ date('Y') + 1 }}"
                                            value="{{ old('anoModVeic') ?? date('Y') }}" placeholder="Ano de Modelo...">
                                        <label for="anoModVeic">Ano de Mod.</label>
                                        <div class="invalid-feedback">
                                            Informe um ano de modelo válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-4">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" step="0.01" id="pesoLVeic"
                                            name="pesoLVeic" value="{{ old('pesoLVeic') }}"
                                            placeholder="Peso Líquido...">
                                        <label for="pesoLVeic">Peso Líqui.</label>
                                        <div class="invalid-feedback">
                                            Informe um peso líq. válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-4">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="pesoBVeic" name="pesoBVeic"
                                            value="{{ old('pesoBVeic') }}" placeholder="Peso Bruto...">
                                        <label for="pesoBVeic">Peso Bruto</label>
                                        <div class="invalid-feedback">
                                            Informe um peso bru. válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-4">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="distVeic" name="distVeic"
                                            value="{{ old('distVeic') }}" placeholder="Distância...">
                                        <label for="distVeic">Distância</label>
                                        <div class="invalid-feedback">
                                            Informe uma distância válida.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="combVeic" id="combVeic">
                                            <option value="">Selecione um Combustível</option>
                                            <option value="1" @if (old('combVeic') == '1') selected @endif>
                                                ÁLCOOL</option>
                                            <option value="2" @if (old('combVeic') == '2') selected @endif>
                                                GASOLINA</option>
                                            <option value="3" @if (old('combVeic') == '3') selected @endif>
                                                DIESEL</option>
                                            <option value="16" @if (old('combVeic') == '16') selected @endif>
                                                ÁLCOOL/GASOLINA
                                            </option>
                                            <option value="17" @if (old('combVeic') == '17') selected @endif>
                                                GASOLINA/ÁLCOOL/GNV</option>
                                            <option value="18" @if (old('combVeic') == '18') selected @endif>
                                                GASOLINA/ELÉTRICO
                                            </option>
                                        </select>
                                        <label for="combVeic">Combustível</label>
                                        <div class="invalid-feedback">
                                            Informe qual tipo de combustível.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nMotorVeic" name="nMotorVeic"
                                            value="{{ old('nMotorVeic') }}" placeholder="Número do Motor..."
                                            oninput="this.value = this.value.toUpperCase()">
                                        <label for="nMotorVeic">Nº do Motor</label>
                                        <div class="invalid-feedback">
                                            Informe um nº de motor válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="number" step="0.01" class="form-control" id="cvVeic"
                                            name="cvVeic" value="{{ old('cvVeic') }}" placeholder="Potência...">
                                        <label for="cvVeic">Potência</label>
                                        <div class="invalid-feedback">
                                            Informe uma potência válida.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="number" step="0.01" class="form-control" id="cm3Veic"
                                            name="cm3Veic" value="{{ old('cm3Veic') }}" placeholder="Cilindradas...">
                                        <label for="cm3Veic">Cilindradas</label>
                                        <div class="invalid-feedback">
                                            Informe uma cilindrada válida.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="serieVeic" name="serieVeic"
                                            value="{{ old('serieVeic') }}" placeholder="Série...">
                                        <label for="serieVeic">Série</label>
                                        <div class="invalid-feedback">
                                            Informe uma série válida.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="tpPVeic" name="tpPVeic"
                                            value="{{ old('tpPVeic') }}" placeholder="Tipo de Pintura...">
                                        <label for="tpPVeic">Tipo de Pintura</label>
                                        <div class="invalid-feedback">
                                            Informe um tipo de pintura válida.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="corVeic" name="corVeic"
                                            oninput="this.value = this.value.toUpperCase()" value="{{ old('corVeic') }}"
                                            placeholder="Cor...">
                                        <label for="corVeic">Cor</label>
                                        <div class="invalid-feedback">
                                            Informe uma cor válida.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="cCorVeic" id="cCorVeic">
                                            <option value="">Selecione um Código de Cor</option>
                                            <option value="01" @if (old('cCorVeic') == '01') selected @endif>01
                                                - AMARELO
                                            </option>
                                            <option value="02" @if (old('cCorVeic') == '02') selected @endif>02
                                                - AZUL</option>
                                            <option value="03" @if (old('cCorVeic') == '03') selected @endif>03
                                                - BEGE</option>
                                            <option value="04" @if (old('cCorVeic') == '04') selected @endif>04
                                                - BRANCA
                                            </option>
                                            <option value="05" @if (old('cCorVeic') == '05') selected @endif>05
                                                - CINZA
                                            </option>
                                            <option value="06" @if (old('cCorVeic') == '06') selected @endif>06
                                                - DOURADA
                                            </option>
                                            <option value="07" @if (old('cCorVeic') == '07') selected @endif>07
                                                - GRENAR
                                            </option>
                                            <option value="08" @if (old('cCorVeic') == '08') selected @endif>08
                                                - LARANJA
                                            </option>
                                            <option value="09" @if (old('cCorVeic') == '09') selected @endif>09
                                                - MARROM
                                            </option>
                                            <option value="10" @if (old('cCorVeic') == '10') selected @endif>10
                                                - PRATA
                                            </option>
                                            <option value="11" @if (old('cCorVeic') == '11') selected @endif>11
                                                - PRETA
                                            </option>
                                            <option value="12" @if (old('cCorVeic') == '12') selected @endif>12
                                                - ROSA</option>
                                            <option value="13" @if (old('cCorVeic') == '13') selected @endif>13
                                                - ROXA</option>
                                            <option value="14" @if (old('cCorVeic') == '14') selected @endif>14
                                                - VERDE
                                            </option>
                                            <option value="15" @if (old('cCorVeic') == '15') selected @endif>15
                                                - VERMELHA
                                            </option>
                                            <option value="16" @if (old('cCorVeic') == '16') selected @endif>16
                                                - FANTASIA
                                            </option>
                                        </select>
                                        <label for="cCorVeic">Código de Cor</label>
                                        <div class="invalid-feedback">
                                            Informe uma cor válida.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cCorMontVeic" name="cCorMontVeic"
                                            value="{{ old('cCorMontVeic') }}" placeholder="Código de Cor Montadora...">
                                        <label for="cCorMontVeic">Cód. de Cor
                                            Mont.</label>
                                        <div class="invalid-feedback">
                                            Informe um código de cor válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-6">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cMarcaVeic" name="cMarcaVeic"
                                            value="{{ old('cMarcaVeic') }}" placeholder="Código da Marca...">
                                        <label for="cMarcaVeic">Código da
                                            Marca</label>
                                        <div class="invalid-feedback">
                                            Informe um código de marca válido.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="condVeic" id="condVeic">
                                            <option value="">Selecione uma Condição</option>
                                            <option value="1" @if (old('condVeic') == '1') selected @endif>
                                                ACABADO</option>
                                            <option value="2" @if (old('condVeic') == '2') selected @endif>
                                                INACABADO</option>
                                            <option value="3" @if (old('condVeic') == '3') selected @endif>
                                                SEMIACABO</option>
                                        </select>
                                        <label for="condVeic">Condição do
                                            Veículo</label>
                                        <div class="invalid-feedback">
                                            Informe a condição do veículo.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="espVeic" id="espVeic">
                                            <option value="">Selecione uma Especificação</option>
                                            <option value="1" @if (old('espVeic') == '1') selected @endif>
                                                PASSAGEIRO
                                            </option>
                                            <option value="2" @if (old('espVeic') == '2') selected @endif>
                                                CARGA</option>
                                            <option value="3" @if (old('espVeic') == '3') selected @endif>
                                                MISTO</option>
                                            <option value="4" @if (old('espVeic') == '4') selected @endif>
                                                CORRIDA</option>
                                            <option value="5" @if (old('espVeic') == '5') selected @endif>
                                                TRAÇÃO</option>
                                            <option value="6" @if (old('espVeic') == '6') selected @endif>
                                                ESPECIAL</option>
                                        </select>
                                        <label for="espVeic">Especificação do
                                            Veículo</label>
                                        <div class="invalid-feedback">
                                            Informe a especificação do veículo.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="vinVeic" id="vinVeic">
                                            <option value="">Selecione o VIN</option>
                                            <option value="N" @if (old('vinVeic') == 'N') selected @endif>
                                                NORMAL</option>
                                            <option value="R" @if (old('vinVeic') == 'R') selected @endif>
                                                REMARCADO</option>
                                        </select>
                                        <label for="vinVeic">VIN do Veículo</label>
                                        <div class="invalid-feedback">
                                            Informe o VIN do veículo.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="lotVeic" name="lotVeic"
                                            value="{{ old('lotVeic') }}" placeholder="Lotação Máxima...">
                                        <label for="lotVeic">Lotação Máxima</label>
                                        <div class="invalid-feedback">
                                            Informe a lotação máxima do veículo.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="restriVeic" id="restriVeic">
                                            <option value="">Selecione uma Restrição</option>
                                            <option value="0" @if (old('restriVeic') == '0') selected @endif>
                                                NÃO HÁ</option>
                                            <option value="1" @if (old('restriVeic') == '1') selected @endif>
                                                ALIENAÇÃO
                                                FIDUNCIÁRIA</option>
                                            <option value="2" @if (old('restriVeic') == '2') selected @endif>
                                                ARRENDAMENTO
                                                MERCANTIL</option>
                                            <option value="3" @if (old('restriVeic') == '3') selected @endif>
                                                RESERVA DE DOMÍNIO
                                            </option>
                                            <option value="4" @if (old('restriVeic') == '4') selected @endif>
                                                PENHOR DE VEÍCULOS
                                            </option>
                                            <option value="9" @if (old('restriVeic') == '9') selected @endif>
                                                OUTRAS</option>
                                        </select>
                                        <label for="restriVeic">Restrição do
                                            Veículo</label>
                                        <div class="invalid-feedback">
                                            Informe a restrição do veículo.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="cargaVeic" name="cargaVeic"
                                            value="{{ old('cargaVeic') }}" placeholder="Carga Máxima...">
                                        <label for="cargaVeic">Carga Máxima</label>
                                        <div class="invalid-feedback">
                                            Informe a carga máxima do veículo.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 col-12">
                                <div class="input-group has-validation mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" name="operVeic" id="operVeic">
                                            <option value="">Selecione um Tipo de Operação</option>
                                            <option value="1" @if (old('operVeic') == '1') selected @endif>
                                                VENDA
                                                CONCERSSIONÁRIA</option>
                                            <option value="2" @if (old('operVeic') == '2') selected @endif>
                                                FATURAMENTO DIRETO
                                                PARA CONSUMIDOR FINAL</option>
                                            <option value="3" @if (old('operVeic') == '3') selected @endif>
                                                VENDA DIRETO PARA
                                                GRANDES CONSUMIDORES</option>
                                            <option value="0" @if (old('operVeic') == '0') selected @endif>
                                                OUTRAS</option>
                                        </select>
                                        <label for="operVeic">Tipo de Operação</label>
                                        <div class="invalid-feedback">
                                            Informe tipo de operação do veículo.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-outline-success w-25">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @component('components.modal', [
        'modalId' => 'NcmModal',
        'modalTitle' => 'Selecione NCM',
        'sizeModal' => 'modal-lg',
    ])
        @component('components.dataTable', [
            'responsive' => [
                [
                    'responsivePriority' => 1,
                    'targets' => 0,
                ],
                [
                    'responsivePriority' => 2,
                    'targets' => 1,
                ],
            ],
            'searching' => true,
            'lengthChange' => true,
            'pageLength' => 10,
            'ordering' => true,
            'showFooter' => false,
        ])
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
        @endcomponent
    @endcomponent
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
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
        })()
    </script>
    <script>
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
