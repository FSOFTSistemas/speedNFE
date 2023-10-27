@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Visualizar Cliente</h1>
        </div>
    </div>
@stop

@section('content')
    <a class="btn btn-secondary" style="margin-bottom: 2%" href="{{ route('index') }}">Voltar</a>

    <div class="content">
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12" style="width: 100%">

                <div class="card card-primary card-outline card-tabs">

                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Informações do Cliente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Endereço</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">

                                    <div class="row">
                                        <div class="col">
                                            <label for="nome">Nome</label>
                                            <input class="form-control" type="text" name="nome" id="nome"
                                                required placeholder="Nome de cliente..." value="{{ $cliente->nome }}">
                                        </div>
                                        <div class="col">
                                            <label for="apelido">Apelido/Fantasia</label>
                                            <input class="form-control" type="text" name="apelido" id="apelido"
                                                required placeholder="Apelido de cliente..."
                                                value="{{ $cliente->apelido }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="codigo">Código Gerencial</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                required placeholder="Código gerencial..." value="{{ $cliente->codigo }}">
                                        </div>
                                        <div class="col">
                                            <label for="limite">Limite</label>
                                            <input class="form-control" type="text" name="limite" id="limite"
                                                required placeholder="Limite..." value="{{ $cliente->limite }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="cpf_cnpj">CPF/CNPJ</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cpf_cnpj"
                                                        id="cpf_cnpj" onblur="this.value = formatarCpfCnpj(this.value);"
                                                        required placeholder="CPF/CNPJ..." maxlength="14"
                                                        value="{{ $cliente->cpf_cnpj }}">
                                                </div>
                                                <button id="cnpj_button" type="button" class="btn btn-light"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="rg_ie">RG ou IE</label>
                                            <input class="form-control" type="text" name="rg_ie" id="rg_ie"
                                                required placeholder="RG/IE..." value="{{ $cliente->rg_ie }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="tipo">Tipo</label>
                                            <select class="form-control" name="tipo" id="tipo" required>
                                                <option value="{{ $cliente->tipo }}">
                                                    {{ $cliente->tipo == '1' ? 'Pessoa Física' : 'Pessoa Jurídica' }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="telefone">Telefone</label>
                                            <input type="text" id="telefone" name="telefone" class="form-control"
                                                required placeholder="Telefone..." value="{{ $cliente->telefone }}">
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="empresa">Empresa</label>
                                            <select name="empresa" id="empresa" class="form-control" required>
                                                <option value="{{ $cliente->empresa_id }}">{{ $cliente->razao }}</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">


                                    <div class="row">
                                        <div class="col">
                                            <label for="rua">Rua</label>
                                            <input class="form-control" type="text" name="rua" id="rua"
                                                placeholder="Rua..." value="{{ $cliente->rua }}" required>
                                        </div>
                                        <div class="col">
                                            <label for="numero">Número</label>
                                            <input class="form-control" type="text" name="numero" id="numero"
                                                placeholder="Nº..." value="{{ $cliente->numero }}" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="bairro">Bairro</label>
                                            <input class="form-control" type="text" name="bairro" id="bairro"
                                                placeholder="Bairro..." value="{{ $cliente->bairro }}" required>
                                        </div>
                                        <div class="col">
                                            <label for="cidade">Cidade</label>
                                            <input class="form-control" type="text" name="cidade" id="cidade"
                                                placeholder="Cidade..." value="{{ $cliente->cidade }}" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="estado">Estado</label>
                                            <select name="uf" id="uf" class="form-control" required>
                                                <option value="{{ $cliente->uf }}">{{ $cliente->uf }}</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="cep">CEP</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cep"
                                                        placeholder="Cep..." id="cep" value="{{ $cliente->cep }}" required>
                                                </div>
                                                <button class="btn btn-light" type="button" id="cep_button"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="cod_ibge">Cód. IBGE</label>
                                            <select class="form-control" name="ibge" id="ibge"
                                                style="width: 100%">
                                                <option value="{{ $cliente->codigoIBGE }}">{{ $cliente->codigoIBGE }}</option>
                                            </select>
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

@section('js')
@stop
