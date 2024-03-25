@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Visualizar Empresa</h1>
        </div>
    </div>
@stop

@section('content')
    <a href="{{ route('empresa.index') }}" style="margin-bottom: 2%" class="btn btn-secondary">Voltar</a>
    <div class="content">
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12" style="width: 100%">

                <div class="card card-primary card-outline card-tabs">

                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Empresa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Endereço</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="fiscal-tab" data-toggle="pill" href="#fiscal" role="tab"
                                    aria-controls="fiscal" aria-selected="false">Fiscal</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="limite-tab" data-toggle="pill" href="#limite" role="tab"
                                    aria-controls="limite" aria-selected="false">Limites</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="user-tab" data-toggle="pill" href="#user" role="tab"
                                    aria-controls="user" aria-selected="false">Usuário</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">


                        <div class="tab-content" id="tabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">

                                <div class="row">
                                    <div class="col-md-6 col-xs-10">
                                        <label>Razão Social</label>
                                        <input required class="form-control" type="text" id="nome" name="nome"
                                            value="{{ $empresa->razao }}" />
                                    </div>

                                    <div class="col-md-6 col-xs-10">
                                        <label>Nome Fantasia</label>
                                        <input required class="form-control" type="text" id="fantasia" name="fantasia"
                                            value="{{ $empresa->fantasia }}" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-xs-10">
                                        <label>CPF ou CNPJ</label>
                                        <div class="row">
                                            <div class="col-md-6 col-xs-10">
                                                <input class="form-control" type="text" id="cpf_cnpj" name="cpf_cnpj"
                                                    value="{{ $empresa->cpf_cnpj }}" maxlength="14" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xs-10">
                                        <label>RG ou IE</label>
                                        <input class="form-control" type="text" id="rg_ie" name="rg_ie"
                                            value="{{ $empresa->rg_ie }}" />
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-6 col-xs-10">
                                        <label>Celular</label>
                                        <input class="form-control" type="text" id="telefone" name="telefone"
                                            value="{{ $empresa->celular }}" />
                                    </div>

                                </div>

                            </div>

                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="col-md-6 col-xs-10">
                                    <div class="row">
                                        <div class="col-9">
                                            <label>Rua</label>
                                            <input class="form-control" type="text" id="rua" name="rua"
                                                value="{{ $empresa->rua }}" />
                                        </div>
                                        <div class="col-3">
                                            <label>Número</label>
                                            <input class="form-control" type="number" id="numero" name="numero"
                                                value="{{ $empresa->numero }}" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>Bairro</label>
                                            <input class="form-control" type="text" id="bairro" name="bairro"
                                                value="{{ $empresa->bairro }}" />
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cep">CEP</label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="cep" name="cep" value="{{ $empresa->cep }}" />
                                                
                                            </div>
                                        </div>
                                        
                                        </div>

                                   

                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label>Cidade</label>
                                            <input class="form-control" type="text" id="cidade" name="cidade"
                                                value="{{ $empresa->cidade }}" />
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label>UF</label>
                                            <select class="form-control" id="uf" name="uf">
                                                <option value="{{ $empresa->uf }}">{{ $empresa->uf }}</option>
                                            </select>

                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label>Complemento</label>
                                            <input class="form-control" type="text" id="complemento"
                                                name="complemento" value="{{ $empresa->complemento }}" />

                                        </div>
                                        <div class="col-md-6 col-xs-10">

                                            <label>Código IBGE</label>
                                            <input class="form-control" type="number" id="ibge" name="ibge"
                                                value="{{ $empresa->codigoIBGE }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="fiscal" role="tabpanel" aria-labelledby="fiscal-tab">

                                <div class="row">
                                    <div class="col-md-6 col-xs-10">
                                        <label>Nº da Última NFe</label>
                                        <input class=form-control type="number" name="nfe" id="nfe"
                                            value="{{ $empresa->ultimaNFe }}" />

                                    </div>
                                    <div class="col-md-6 col-xs-10">
                                        <label>Serie</label>
                                        <input class=form-control type="number" name="serie" id="serie"
                                            value="{{ $empresa->serie }}" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-xs-10">
                                        <label>CSC</label>
                                        <input class="form-control" type="text" name="csc" id="csc"
                                            value="{{ $empresa->csc }}">


                                    </div>
                                    <div class="col-md-6 col-xs-10">
                                        <label>Id Token CSC</label>
                                        <input class="form-control" type="text" name="idCsc" id="idCsc"
                                            value="{{ $empresa->idCsc }}">

                                    </div>
                                    <div class="col-md-6 col-xs-10">
                                        <label>Ambiente</label>
                                        <input class=form-control type="number" name="ambiente" id="ambiente"
                                            value="{{ $empresa->ambiente }}" />
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="limite" role="tabpanel" aria-labelledby="limite-tab">

                                <label>Limite de Clientes</label>
                                <input class=form-control type="number" name="clientes" id="clientes"
                                    value="{{ $empresa->limClientes }}" />

                                <label>Limite de Produtos</label>
                                <input class=form-control type="number" name="produtos" id="produtos"
                                    value="{{ $empresa->limProdutos }}" />

                                <label>Limite de Notas</label>
                                <input class=form-control type="number" name="notas" id="notas"
                                    value="{{ $empresa->limNFes }}" />

                            </div>

                            <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">

                                <label>Nome</label>
                                <input class=form-control type="text" name="name" id="name"
                                    value="{{ $empresa->name }}" />

                                <label>Email</label>
                                <input class=form-control type="text" name="email" id="email"
                                    value="{{ $empresa->email }}" />

                            </div>

                        </div>


                    </div>
                </div>

            </div>

        </div>


    </div>
    </div>

@endsection

@section('js')
    <script></script>
@endsection
