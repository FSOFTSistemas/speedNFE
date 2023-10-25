@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Produtos</h1>
@stop

@section('content')

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
                    <form method="POST" action="{{ route('update_produto',['id'=>$produto->id]) }}">
                        @csrf

                        <div class="tab-content" id="tabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">
                                <div class="row">
                                    <div class="col">
                                        @if ($empresa == 1)
                                            <label>Empresa</label>
                                            <select onchange="javascript:liberarProdutos({{ $empresa }})"
                                                class="form-control" name="empresa" id="empresa">
                                                <option value="{{$produto->empresa_id}}">{{$empresaAnterior->razao}}</option>
                                                @foreach ($empresas as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input onload="javascript:liberarProdutos({{ $empresa }})"
                                                type="hidden" value="{{ $empresa }}">
                                        @endif
                                    </div>

                                  <div class="col">
                                    <label>Categoria</label>
                                    @if ($empresa == 1)
                                        <select  class="form-control" name="categoria" id="categoria">
                                    @else
                                            <select class="form-control" name="categoria" id="categoria">
                                    @endif
                                    <option value="{{$produto->categoria_id}}">{{$categoriaAnterior->descricao}}</option>
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
                                        <input class="form-control" type="text" name="codigo" id="codigo" value="{{$produto->codigo}}">
                                    </div>
                                    <div class="col">
                                        <label for="produto">Produto</label>
                                        <input class="form-control" type="text" name="produto" id="produto" value="{{$produto->produto}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="ncm">NCM</label>
                                        <input class="form-control" type="text" name="ncm" id="ncm" value="{{$produto->ncm}}">
                                    </div>
                                    <div class="col">
                                        <label for="precocusto">Preço Custo</label>
                                        <input class="form-control" type="text" name="precocusto" id="precocusto" value="{{$produto->precocusto}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="precovenda">Preço de Venda</label>
                                        <div class="row">
                                            <div class="col">
                                                <input class="form-control" type="text" name="precovenda"
                                                    id="precovenda" value="{{$produto->precovenda}}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label for="un">Unidade</label>
                                        <select name="un" id="un" class="form-control">
                                            <option value="{{$produto->un}}">{{$produto->un}}</option>
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
                                        <input class="form-control" type="text" name="cfopinterno" id="cfopinterno" value="{{$produto->cfop_interno}}">
                                    </div>
                                    <div class="col">
                                        <label for="cfopexterno">CFOP Externo</label>
                                        <input class="form-control" type="text" name="cfopexterno" id="cfopexterno" value="{{$produto->cfop_externo}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="cst">CST</label>
                                        <input class="form-control" type="text" name="cst" id="cst" value="{{$produto->cst}}">
                                    </div>
                                    <div class="col">
                                        <label for="cst_pis">CST/PIS</label>
                                        <input class="form-control" type="text" name="cst_pis" id="cst_pis" value="{{$produto->cst_pis}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="cst_cofins">CST/COFINS</label>
                                        <input class="form-control" type="text" name="cst_cofins" id="cst_cofins" value="{{$produto->cst_cofins}}">
                                    </div>
                                    <div class="col">
                                        <label for="cofins">COFINS</label>
                                        <div class="row">
                                            <div class="col">
                                                <input class="form-control" type="text" name="cofins"
                                                    id="cofins" value="{{$produto->cofins}}">
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col">
                                        <label for="icms">ICMS</label>
                                        <input class="form-control" type="text" name="icms" id="icms" value="{{$produto->icms}}">
                                    </div>
                                    <div class="col">
                                        <label for="cst_csosn">CST/CSOSN</label>
                                        <input type="text" class="form-control" name="cst_csosn" id="cst_csosn" value="{{$produto->cst_csosn}}">
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col">
                                        <label for="pis">PIS</label>
                                        <input class="form-control" type="text" name="pis" id="pis" value="{{$produto->pis}}">
                                    </div>
                                    <div class="col">
                                        <label for="ipi">IPI</label>
                                        <input type="text" class="form-control" name="ipi" id="ipi" value="{{$produto->ipi}}">
                                    </div>
                                </div>
                            </div>

                            <!-- /.card -->
                        </div>

                    </form>
                </div>

            </div>

        </div>

    </div>


</div>

@endsection