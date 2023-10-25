@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastro de Clientes</h1>
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
                                    aria-controls="home" aria-selected="true">Informações do Cliente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Endereço</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('salvar_cliente',['id' => $cliente->id]) }}" method="POST">
                            @csrf
                            
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">

                                    <div class="row">
                                        <div class="col">
                                            <label for="nome">Nome</label>
                                            <input class="form-control" type="text" name="nome" id="nome" value="{{$cliente->nome}}">
                                        </div>
                                        <div class="col">
                                            <label for="apelido">Apelido/Fantasia</label>
                                            <input class="form-control" type="text" name="apelido" id="apelido" value="{{$cliente->apelido}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="codigo">Código Gerencial</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo" value="{{$cliente->codigo}}">
                                        </div>
                                        <div class="col">
                                            <label for="limite">Limite</label>
                                            <input class="form-control" type="text" name="limite" id="limite" value="{{$cliente->limite}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="cpf_cnpj">CPF/CNPJ</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cpf_cnpj"
                                                        id="cpf_cnpj" onblur="this.value = formatarCpfCnpj(this.value);"
                                                        maxlength="14" value="{{$cliente->cpf_cnpj}}">
                                                </div>
                                                <button id="cnpj_button" type="button" class="btn btn-light"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="rg_ie">RG ou IE</label>
                                            <input class="form-control" type="text" name="rg_ie" id="rg_ie" value="{{$cliente->rg_ie}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="tipo">Tipo</label>
                                            <select class="form-control" name="tipo" id="tipo">
                                                <option value="{{$cliente->tipo}}">{{$cliente->tipo == "1" ? "Pessoa Física" : "Pessoa Jurídica"}}</option>
                                                <option value="2">Pessoa Jurídica</option>
                                                <option value="1">Pessoa Física</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="telefone">Telefone</label>
                                            <input type="text" id="telefone" name="telefone" class="form-control" value="{{$cliente->telefone}}">
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="empresa">Empresa</label>
                                            <select name="empresa" id="empresa" class="form-control">
                                                <option value="{{$cliente->empresa_id}}">{{$emp->razao  }}</option>
                                                @foreach ($empresas as $empresa)
                                                    <option value="{{$empresa->id}}">{{$empresa->razao}}</option>
                                                @endforeach
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
                                            <label for="rua">Rua</label>
                                            <input class="form-control" type="text" name="rua" id="rua" value="{{$endereco->rua}}">
                                        </div>
                                        <div class="col">
                                            <label for="numero">Número</label>
                                            <input class="form-control" type="text" name="numero" id="numero" value="{{$endereco->numero}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="bairro">Bairro</label>
                                            <input class="form-control" type="text" name="bairro" id="bairro" value="{{$endereco->bairro}}">
                                        </div>
                                        <div class="col">
                                            <label for="cidade">Cidade</label>
                                            <input class="form-control" type="text" name="cidade" id="cidade" value="{{$endereco->cidade}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="estado">Estado</label>
                                            <select name="uf" id="uf" class="form-control">
                                                <option value="{{$endereco->uf}}">{{$endereco->uf}}</option>
                                                <option value="AL">AL</option>
                                                <option value="AL">AL</option>
                                                <option value="AM">AM</option>
                                                <option value="AP">AP</option>
                                                <option value="BA">BA</option>
                                                <option value="CE">CE</option>
                                                <option value="DF">DF</option>
                                                <option value="ES">ES</option>
                                                <option value="GO">GO</option>
                                                <option value="MA">MA</option>
                                                <option value="MG">MG</option>
                                                <option value="MS">MS</option>
                                                <option value="MT">MT</option>
                                                <option value="PA">PA</option>
                                                <option value="PB">PB</option>
                                                <option value="PE">PE</option>
                                                <option value="PI">PI</option>
                                                <option value="PR">PR</option>
                                                <option value="RJ">RJ</option>
                                                <option value="RN">RN</option>
                                                <option value="RO">RO</option>
                                                <option value="RR">RR</option>
                                                <option value="RS">RS</option>
                                                <option value="SC">SC</option>
                                                <option value="SE">SE</option>
                                                <option value="SP">SP</option>
                                                <option value="TO">TO</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="cep">CEP</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cep"
                                                        id="cep" value="{{$endereco->cep}}">
                                                </div>
                                                <button class="btn btn-light" type="button" id="cep_button"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="cod_ibge">Cód. IBGE</label>
                                            <input class="form-control" type="text" name="ibge" id="ibge" value="{{$endereco->ibge}}">
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