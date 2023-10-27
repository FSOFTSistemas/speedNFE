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
                                            <select class="form-control" name="categoria" id="categoria">
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
                                            <input class="form-control" type="text" name="codigo" id="codigo" required>
                                        </div>
                                        <div class="col">
                                            <label for="produto">Produto</label>
                                            <input class="form-control" type="text" name="produto" id="produto">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="ncm">NCM</label>
                                            <input class="form-control" type="text" name="ncm" id="ncm">
                                        </div>
                                        <div class="col">
                                            <label for="precocusto">Preço Custo</label>
                                            <input class="form-control" type="text" name="precocusto" id="precocusto">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="precovenda">Preço de Venda</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="precovenda"
                                                        id="precovenda">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="un">Unidade</label>
                                            <select name="un" id="un" class="form-control">
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

                                    <br>
                                    <div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-success form-control">Salvar
                                                Cliente</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">


                                    <div class="row">
                                        <div class="col">
                                            <label for="cfopinterno">CFOP Interno</label>
                                            <input class="form-control" type="text" name="cfopinterno"
                                                id="cfopinterno" value="5102">
                                        </div>
                                        <div class="col">
                                            <label for="cfopexterno">CFOP Externo</label>
                                            <input class="form-control" type="text" name="cfopexterno"
                                                id="cfopexterno" value="6102">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="cst">CST</label>
                                            <input class="form-control" type="text" name="cst" id="cst"
                                                value="000">
                                        </div>
                                        <div class="col">
                                            <label for="cst_pis">CST/PIS</label>
                                            <input class="form-control" type="text" name="cst_pis" id="cst_pis"
                                                value="0">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="cst_cofins">CST/COFINS</label>
                                            <input class="form-control" type="text" name="cst_cofins" id="cst_cofins"
                                                value="0">
                                        </div>
                                        <div class="col">
                                            <label for="cofins">COFINS</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cofins"
                                                        id="cofins" value="0">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="icms">ICMS</label>
                                            <input class="form-control" type="text" name="icms" id="icms"
                                                value="17">
                                        </div>
                                        <div class="col">
                                            <label for="cst_csosn">CST/CSOSN</label>
                                            <input type="text" class="form-control" name="cst_csosn" id="cst_csosn"
                                                value="102">
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="pis">PIS</label>
                                            <input class="form-control" type="text" name="pis" id="pis"
                                                value="0">
                                        </div>
                                        <div class="col">
                                            <label for="ipi">IPI</label>
                                            <input type="text" class="form-control" name="ipi" id="ipi"
                                                value="0">
                                        </div>
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
    <script>
        function liberarProdutos(empresa) {
            if (empresa == 1) {

                var input, filter, ul, li, a, i, txtValue;
                input = document.getElementById('empresa');
                filter = input.options[input.selectedIndex].value;
                select = document.getElementById("categoria");
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
                document.getElementById('categoria').removeAttribute('disabled');
            } else {
                document.getElementById('categoria').removeAttribute('disabled');
            }

        }
    </script>
@endsection
