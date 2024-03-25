@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h5 class="m-0 text-dark">Visualizar produto</h5>
        </div>
    </div>
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
                                    aria-controls="home" aria-selected="true">Informações do Produto</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Informações Fiscais</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label>Empresa</label>
                                            <select class="form-control" name="empresa" id="empresa" required>
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
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="codigo">Código de Barras</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                value="{{ $produto->codigo }}">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="produto">Produto</label>
                                            <input class="form-control" type="text" name="produto" id="produto"
                                                value="{{ $produto->produto }}" required placeholder="Produto...">
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
                                        <div class="col-md-6 col-xs-10">
                                            <label for="precovenda">Preço de Venda</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="number" name="precovenda"
                                                        id="precovenda" value="{{ $produto->precovenda }}" required
                                                        placeholder="Preço Venda...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="un">Unidade</label>
                                            <select name="un" id="un" class="form-control" required>
                                                <option value="{{ $produto->un }}">{{ $produto->un }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopinterno">CFOP Interno</label>
                                            <input class="form-control" type="text" name="cfopinterno"
                                                id="cfopinterno" value="{{ $produto->cfop_interno }}" required
                                                placeholder="Cfop Interno...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopexterno">CFOP Externo</label>
                                            <input class="form-control" type="text" name="cfopexterno"
                                                id="cfopexterno" value="{{ $produto->cfop_externo }}" required
                                                placeholder="Cfop Externo...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst">CST</label>
                                            <input class="form-control" type="text" name="cst" id="cst"
                                                value="{{ $produto->cst }}" required placeholder="Cst...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_pis">CST/PIS</label>
                                            <input class="form-control" type="text" name="cst_pis" id="cst_pis"
                                                value="{{ $produto->cst_pis }}" required placeholder="Cst Pis...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_cofins">CST/COFINS</label>
                                            <input class="form-control" type="text" name="cst_cofins" id="cst_cofins"
                                                value="{{ $produto->cst_cofins }}" required placeholder="Cst Cofins...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cofins">COFINS</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cofins"
                                                        id="cofins" value="{{ $produto->cofins }}" required
                                                        placeholder="Cofins...">
                                                </div>

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
                                            <input type="text" class="form-control" name="cst_csosn" id="cst_csosn"
                                                value="{{ $produto->cst_csosn }}" required placeholder="Cst Csosn...">
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
                            </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
