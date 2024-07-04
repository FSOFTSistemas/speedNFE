@extends('adminlte::page')

@section('title', 'Visualizar Cliente')

@section('content_header')
    <div class="row text-center">
        <div class="col">
            <h3 class="m-0">Visualização de Cliente</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col text-right">
            <a class="btn btn-secondary" href="{{ route('cliente.index') }}">Voltar</a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12" style="width: 100%">

                <div class="card card-primary card-outline card-tabs">

                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true"><b>Informações do Cliente</b></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false"><b>Endereço</b></a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="tabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">

                                <div class="row">
                                    <div class="col-md-4 col-8">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <select class="form-select" name="tipo" id="tipo" required>
                                                    <option value="{{ $cliente->tipo }}">
                                                        {{ $cliente->tipo == '1' ? 'Pessoa Física' : 'Pessoa Jurídica' }}
                                                    </option>
                                                    <option value="2">Pessoa Jurídica</option>
                                                    <option value="1">Pessoa Física</option>
                                                </select>
                                                <label for="tipo">Tipo</label>
                                                <div class="invalid-feedback">
                                                    Informe um tipo.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="CPF/CNPJ..." class="form-control"
                                                    type="text" id="cpf_cnpj" name="cpf_cnpj"
                                                    onblur="this.value = formatarCpfCnpj(this.value);" maxlength="14"
                                                    value="{{ $cliente->cpf_cnpj }}" />
                                                <label for="cpf_cnpj">CPF/CNPJ</label>
                                                <div class="invalid-feedback">
                                                    Informe um cpf/cnpj válido.
                                                </div>
                                            </div>
                                            <div class="input-group-append">
                                                <button id="cnpj_button" type="button" class="btn btn-dark"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-7 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="nome" id="nome"
                                                    required placeholder="Nome de cliente..." value="{{ $cliente->nome }}">
                                                <label for="nome">Nome</label>
                                                <div class="invalid-feedback">
                                                    Informe um nome.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="apelido" id="apelido"
                                                    required placeholder="Apelido de cliente..."
                                                    value="{{ $cliente->apelido }}">
                                                <label for="apelido">Apelido/Fantasia</label>
                                                <div class="invalid-feedback">
                                                    Informe um apelido/fantasia.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 col-6">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="limite" id="limite"
                                                    required placeholder="Limite..." value="{{ $cliente->limite }}">
                                                <label for="limite">Limite</label>
                                                <div class="invalid-feedback">
                                                    Informe um limite.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-6">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="rg_ie" id="rg_ie"
                                                    required placeholder=" " value="{{ $cliente->rg_ie }}">
                                                <label for="rg_ie">RG ou IE</label>
                                                <div class="invalid-feedback">
                                                    Informe um rg/ie.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input type="text" id="telefone" name="telefone"
                                                    class="form-control" placeholder=" " maxlength="15"
                                                    onkeyup="handlePhone(event)" value="{{ $cliente->telefone }}"
                                                    required>
                                                <label for="telefone">Telefone</label>
                                                <div class="invalid-feedback">
                                                    Informe um telefone.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <select name="empresa" id="empresa" class="form-select" required>
                                                    <option value="{{ $cliente->empresa_id }}">{{ $cliente->razao }}
                                                    </option>
                                                </select>
                                                <label for="empresa">Empresa</label>
                                                <div class="invalid-feedback">
                                                    Informe uma empresa.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input class="form-control" type="text" name="codigo" id="codigo" required
                                value="{{ $cliente->codigo }}" value="0" hidden>

                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                <div class="row">
                                    <div class="col-md-4 col-7">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input placeholder="Cep..." class="form-control" type="text"
                                                    id="cep" name="cep" value="{{ $cliente->cep }}" />
                                                <label for="cep">CEP</label>
                                                <div class="invalid-feedback">
                                                    Informe um cep.
                                                </div>
                                            </div>
                                            <div class="input-group-append">
                                                <button class="btn btn-dark" type="button" id="cep_button"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-5">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input placeholder="Código IBGE..." class="form-control" type="number"
                                                    id="ibge" name="ibge" value="{{ $cliente->codigoIBGE }}" />
                                                <label for="cod_ibge">Cód. IBGE</label>
                                                <div class="invalid-feedback">
                                                    Informe um código IBGE.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-9 col-9">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="rua" id="rua"
                                                    placeholder="Rua..." value="{{ $cliente->rua }}" required>
                                                <label for="rua">Rua</label>
                                                <div class="invalid-feedback">
                                                    Informe uma rua.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-3">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="numero" id="numero"
                                                    placeholder=" " value="{{ $cliente->numero }}" required>
                                                <label for="numero">Nº</label>
                                                <div class="invalid-feedback">
                                                    Informe um número.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-5 col-8">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="bairro" id="bairro"
                                                    placeholder=" " value="{{ $cliente->bairro }}" required>
                                                <label for="bairro">Bairro</label>
                                                <div class="invalid-feedback">
                                                    Informe um bairro.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5 col-8">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="cidade" id="cidade"
                                                    placeholder="Cidade..." value="{{ $cliente->cidade }}" required>
                                                <label for="cidade">Cidade</label>
                                                <div class="invalid-feedback">
                                                    Informe uma cidade.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-4">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <select name="uf" id="uf" class="form-select" required>
                                                    <option value="{{ $cliente->uf }}">{{ $cliente->uf }}</option>
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
                                                <label for="estado">Estado</label>
                                                <div class="invalid-feedback">
                                                    Informe um estado.
                                                </div>
                                            </div>
                                        </div>
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

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
