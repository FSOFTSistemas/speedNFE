@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Visualizar de Empresa</h1>
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
                                        <div class="col">
                                            <label>Razão Social</label>
                                            <input required class="form-control" type="text" id="nome"
                                                name="nome" value="{{ $empresa->razao }}"/>
                                        </div>

                                        <div class="col">
                                            <label>Nome Fantasia</label>
                                            <input required class="form-control" type="text" id="fantasia"
                                                name="fantasia" value="{{ $empresa->fantasia }}"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>CPF ou CNPJ</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" id="cpf_cnpj"
                                                        name="cpf_cnpj" value="{{ $empresa->cpf_cnpj }}"
                                                        maxlength="14" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>RG ou IE</label>
                                            <input class="form-control" type="text" id="rg_ie" name="rg_ie" value="{{ $empresa->rg_ie }}"/>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col">
                                            <label>Celular</label>
                                            <input class="form-control" type="text" id="telefone" name="telefone" value="{{ $empresa->celular }}"/>
                                        </div>

                                    </div>

                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9">
                                                <label>Rua</label>
                                                <input class="form-control" type="text" id="rua"
                                                    name="rua" />
                                            </div>
                                            <div class="col-3">
                                                <label>Número</label>
                                                <input class="form-control" type="number" id="numero"
                                                    name="numero" />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-8">
                                                <label>Bairro</label>
                                                <input class="form-control" type="text" id="bairro"
                                                    name="bairro" />
                                            </div>
                                            <div class="col">
                                                <label>CEP</label>
                                                <div class="row">
                                                    <div class="col">

                                                        <input class="form-control" type="text" id="cep"
                                                            name="cep" />
                                                    </div>
                                                    <button class="btn btn-light" type="button" id="cep_button"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <label>Cidade</label>
                                                <input class="form-control" type="text" id="cidade"
                                                    name="cidade" />
                                            </div>
                                            <div class="col">
                                                <label>UF</label>
                                                <select class="form-control" id="uf" name="uf">
                                                    <option>-- Escolha uma Unidade Federativa --</option>
                                                    <option value='RO'>RO</option>
                                                    <option value='AC'>AC</option>
                                                    <option value='AM'>AM</option>
                                                    <option value='RR'>RR</option>
                                                    <option value='PA'>PA</option>
                                                    <option value='AP'>AP</option>
                                                    <option value='TO'>TO</option>
                                                    <option value='MA'>MA</option>
                                                    <option value='PI'>PI</option>
                                                    <option value='CE'>CE</option>
                                                    <option value='RN'>RN</option>
                                                    <option value='PB'>PB</option>
                                                    <option value='PE'>PE</option>
                                                    <option value='AL'>AL</option>
                                                    <option value='SE'>SE</option>
                                                    <option value='BA'>BA</option>
                                                    <option value='MG'>MG</option>
                                                    <option value='ES'>ES</option>
                                                    <option value='RJ'>RJ</option>
                                                    <option value='SP'>SP</option>
                                                    <option value='PR'>PR</option>
                                                    <option value='SC'>SC</option>
                                                    <option value='RS'>RS</option>
                                                    <option value='MS'>MS</option>
                                                    <option value='MT'>MT</option>
                                                    <option value='GO'>GO</option>
                                                    <option value='DF'>DF</option>
                                                </select>

                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col">
                                                <label>Complemento</label>
                                                <input class="form-control" type="text" id="complemento"
                                                    name="complemento" />

                                            </div>
                                            <div class="col">

                                                <label>Código IBGE</label>
                                                <input class="form-control" type="number" id="ibge"
                                                    name="ibge" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="fiscal" role="tabpanel" aria-labelledby="fiscal-tab">

                                    <div class="row">
                                        <div class="col">
                                            <label>Nº da Última NFe</label>
                                            <input class=form-control type="number" name="nfe" id="nfe"/>

                                        </div>
                                        <div class="col">
                                            <label>Serie</label>
                                            <input class=form-control type="number" name="serie" id="serie" value="{{ $empresa->serie }}"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>Certificado</label><br> <!-- inserir arquivo pfx -->
                                            <input accept=".pfx" type="file" name="certificado" id="certificado"
                                                class="file-upload-default">
                                        </div>

                                        <div class="col">
                                            <label>Senha Certificado</label>
                                            <input class=form-control type="text" name="senha" id="senha" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>CSC</label>
                                            <input class="form-control" type="text" name="csc" id="csc" value="{{ $empresa->csc }}">


                                        </div>
                                        <div class="col">
                                            <label>Id Token CSC</label>
                                            <input class="form-control" type="text" name="idCsc" id="idCsc" value="{{ $empresa->idCsc }}">

                                        </div>
                                        <div class="col">
                                            <label>Ambiente</label>
                                            <input class=form-control type="number" name="ambiente" id="ambiente" value="{{ $empresa->ambiente }}"/>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="limite" role="tabpanel" aria-labelledby="limite-tab">

                                    <label>Limite de Clientes</label>
                                    <input class=form-control type="number" name="clientes" id="clientes" value="{{ $empresa->limClientes }}"/>

                                    <label>Limite de Produtos</label>
                                    <input class=form-control type="number" name="produtos" id="produtos" value="{{ $empresa->limProdutos }}"/>

                                    <label>Limite de Notas</label>
                                    <input class=form-control type="number" name="notas" id="notas" value="{{ $empresa->limNotas }}"/>

                                </div>

                                <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">

                                    <label>Nome</label>
                                    <input class=form-control type="text" name="name" id="name" value="{{ $empresa->name }}"/>

                                    <label>Email</label>
                                    <input class=form-control type="text" name="email" id="email" value="{{ $empresa->email }}"/>

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
    <script>
    </script>
@endsection
