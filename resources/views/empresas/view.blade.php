@extends('adminlte::page')

@section('title', 'Visualizar Empresa')

@section('content_header')
    <div class="row text-center">
        <div class="col">
            <h3 class="m-0 text-dark">Visualização de Empresa</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row text-right">
        <div class="col">
            <a href="{{ route('empresa.show') }}" class="btn btn-secondary mb-3">Voltar</a>
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
                                    aria-controls="home" aria-selected="true"><b>Empresa</b></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false"><b>Endereço</b></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="fiscal-tab" data-toggle="pill" href="#fiscal" role="tab"
                                    aria-controls="fiscal" aria-selected="false"><b>Fiscal</b></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="limite-tab" data-toggle="pill" href="#limite" role="tab"
                                    aria-controls="limite" aria-selected="false"><b>Limites</b></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="user-tab" data-toggle="pill" href="#user" role="tab"
                                    aria-controls="user" aria-selected="false"><b>Usuário</b></a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">

                                <div class="row">
                                    <div class="col-md-5 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="CPF/CNPJ..." class="form-control"
                                                    type="text" id="cpf_cnpj" name="cpf_cnpj"
                                                    onblur="this.value = formatarCpfCnpj(this.value);" maxlength="14"
                                                    value="{{ $empresa->cpf_cnpj }}" disabled />
                                                <label for="cpf_cnpj">CPF ou CNPJ</label>
                                                <div class="invalid-feedback">
                                                    Informe um CPF/CNPJ válido.
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
                                    <div class="col-md-3 col-5">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="RG/IE..." class="form-control" type="text"
                                                    id="rg_ie" name="rg_ie" value="{{ $empresa->rg_ie }}" />
                                                <label>RG ou IE</label>
                                                <div class="invalid-feedback">
                                                    Informe um RG/IE válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-7">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Celular..." class="form-control" type="text"
                                                    id="telefone" name="telefone" maxlength="15"
                                                    onkeyup="handlePhone(event)" value="{{ $empresa->celular }}" />
                                                <label>Celular</label>
                                                <div class="invalid-feedback">
                                                    Informe um celular válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Razão Social..." class="form-control"
                                                    type="text" id="nome" name="nome"
                                                    value="{{ $empresa->razao }}" />
                                                <label>Razão Social</label>
                                                <div class="invalid-feedback">
                                                    Informe uma razão social válida.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Nome Fantasia..." class="form-control"
                                                    type="text" id="fantasia" name="fantasia"
                                                    value="{{ $empresa->fantasia }}" />
                                                <label>Nome Fantasia</label>
                                                <div class="invalid-feedback">
                                                    Informe um nome fantasia válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                <div class="row">
                                    <div class="col-md-4 col-7">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Cep..." class="form-control" type="text"
                                                    id="cep" name="cep"
                                                    value="{{ $empresa->endereco->cep }}" />
                                                <label for="cep">CEP</label>
                                                <div class="invalid-feedback">
                                                    Informe um CEP válido.
                                                </div>
                                            </div>
                                            <div class="input-group-append">
                                                <button class="btn btn-dark" type="button" id="cep_button"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-5">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Nº..." class="form-control" type="text"
                                                    id="numero" name="numero"
                                                    value="{{ $empresa->endereco->numero }}" />
                                                <label>Número</label>
                                                <div class="invalid-feedback">
                                                    Informe um número válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-4">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <select class="form-select" id="uf" name="uf" required>
                                                    <option value="{{ $empresa->endereco->uf }}">
                                                        {{ $empresa->endereco->uf }}</option>
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
                                                <label>UF</label>
                                                <div class="invalid-feedback">
                                                    Informe um UF válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-8">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Código IBGE..." class="form-control"
                                                    type="number" id="ibge" name="ibge"
                                                    value="{{ $empresa->endereco->codigoIBGE }}" />
                                                <label>Código IBGE</label>
                                                <div class="invalid-feedback">
                                                    Informe um código IBGE válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-9 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Rua..." class="form-control" type="text"
                                                    id="rua" name="rua"
                                                    value="{{ $empresa->endereco->rua }}" />
                                                <label>Rua</label>
                                                <div class="invalid-feedback">
                                                    Informe uma rua válida.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Bairro..." class="form-control"
                                                    type="text" id="bairro" name="bairro"
                                                    value="{{ $empresa->endereco->bairro }}" />
                                                <label>Bairro</label>
                                                <div class="invalid-feedback">
                                                    Informe um bairro válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="cidade" id="cidade"
                                                    value="{{ $empresa->endereco->cidade }}" placeholder="Cidade..."
                                                    required>
                                                <label>Cidade</label>
                                                <div class="invalid-feedback">
                                                    Informe uma cidade válida.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="complemento" id="complemento" style="height: 100px;">{{ $empresa->endereco->complemento }}</textarea>
                                                <label>Complemento</label>
                                                <div class="invalid-feedback">
                                                    Informe um complemento válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

<div class="tab-pane fade" id="fiscal" role="tabpanel" aria-labelledby="fiscal-tab">

    <div class="row">
        <div class="col-md-4 col-12">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input required placeholder="Nº Última NFe..." class="form-control" type="number" name="nfe" id="nfe" value="{{ $empresa->ultimaNFe }}" />
                    <label>Nº da Última NFe</label>
                    <div class="invalid-feedback">Informe o nº da última NFe.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input required placeholder="Nº Última MDFe..." class="form-control" type="number" name="mdfe" id="mdfe" value="{{ $empresa->ultimaMDFe }}" />
                    <label>Nº da Última MDFe</label>
                    <div class="invalid-feedback">Informe o nº da última MDFe.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input required placeholder="Nº Última NFCe..." class="form-control" type="number" name="nfce" id="nfce" value="{{ $empresa->ultimaNFCe }}" />
                    <label>Nº da Última NFCe</label>
                    <div class="invalid-feedback">Informe o nº da última NFCe.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 col-4">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input required placeholder="Série..." class="form-control" type="number" name="serie" id="serie" value="{{ $empresa->serie }}" />
                    <label>Série</label>
                    <div class="invalid-feedback">Informe uma série válida.</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-8">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <select required class="form-select" name="ambiente" id="ambiente">
                        <option value="1" {{ $empresa->ambiente == 1 ? 'selected' : '' }}>Produção</option>
                        <option value="2" {{ $empresa->ambiente == 2 ? 'selected' : '' }}>Homologação</option>
                    </select>
                    <label>Ambiente</label>
                    <div class="invalid-feedback">Informe o ambiente válido.</div>
                </div>
            </div>
        </div>

        {{-- INSERÇÃO DO CAMPO CRT --}}
        <div class="col-md-3 col-12">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <select required class="form-select" name="crt" id="crt">
                        <option value="1" {{ $empresa->crt == 1 ? 'selected' : '' }}>1 - Simples Nacional</option>
                        <option value="2" {{ $empresa->crt == 2 ? 'selected' : '' }}>2 - Simples Nacional - excesso de sublimite</option>
                        <option value="3" {{ $empresa->crt == 3 ? 'selected' : '' }}>3 - Regime Normal</option>
                        <option value="4" {{ $empresa->crt == 4 ? 'selected' : '' }}>4 - Simples Nacional - MEI</option>
                    </select>
                    <label>Regime Tributário (CRT)</label>
                    <div class="invalid-feedback">Informe o CRT.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input placeholder=" " class="form-control" type="email" name="contador" id="contador" value="{{ $empresa->contador }}" />
                    <label>Contador</label>
                    <div class="invalid-feedback">Informe o contador.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-5">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input placeholder="Senha Certificado..." class="form-control" type="text" name="senha" id="senha" value="{{ $empresa->senhaCertificado }}" />
                    <label>Senha Cert.</label>
                    <div class="invalid-feedback">Informe uma senha válida.</div>
                </div>
            </div>
        </div>

        <div class="col-md-7 col-7">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input placeholder=" " type="text" name="certificado" id="certificado" class="form-control" value="{{ count(explode('/', $empresa->certificado)) > 2 ? explode('/', $empresa->certificado)[2] : '' }}">
                    <label>Certificado</label>
                    <div class="invalid-feedback">Informe um certificado válido.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-12">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input required placeholder=" " class="form-control" type="text" name="csc" id="csc" value="{{ $empresa->csc }}">
                    <label>CSC</label>
                    <div class="invalid-feedback">Informe um CSC.</div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-5">
            <div class="input-group has-validation mb-2">
                <div class="form-floating">
                    <input required placeholder=" " class="form-control" type="text" name="idCsc" id="idCsc" value="{{ $empresa->idCsc }}">
                    <label>Id Token CSC</label>
                    <div class="invalid-feedback">Informe um id token CSC.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CAMPOS HIDDEN PARA EVITAR NULL CONSTRAINTS NO BANCO --}}
    <input type="hidden" name="nfes" value="{{ $empresa->limNFes }}">
    <input type="hidden" name="nfces" value="{{ $empresa->limNFCes }}">
    <input type="hidden" name="mdfes" value="{{ $empresa->limMDFes }}">
    <input type="hidden" name="clientes" value="{{ $empresa->limClientes }}">
    <input type="hidden" name="produtos" value="{{ $empresa->limProdutos }}">
</div>

                            <div class="tab-pane fade" id="limite" role="tabpanel" aria-labelledby="limite-tab">
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="" class=form-control type="number"
                                                    name="clientes" id="clientes"
                                                    value="{{ $empresa->limClientes }}" />
                                                <label>Lim. de Clientes</label>
                                                <div class="invalid-feedback">
                                                    Informe o limite de clientes.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-6">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder=" " class="form-control" type="number"
                                                    name="produtos" id="produtos"
                                                    value="{{ $empresa->limProdutos }}" />
                                                <label>Lim. de Produtos</label>
                                                <div class="invalid-feedback">
                                                    Informe o limite de produtos.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder=" " class="form-control" type="number"
                                                    name="nfes" id="nfes" value="{{ $empresa->limNFes }}" />
                                                <label>Limite de notas (NFe)</label>
                                                <div class="invalid-feedback">
                                                    Informe o limite de notas (NFe).
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder=" " class="form-control" type="number"
                                                    name="nfces" id="nfces" value="{{ $empresa->limNFCes }}" />
                                                <label>Limite de notas (NFCe)</label>
                                                <div class="invalid-feedback">
                                                    Informe o limite de notas (NFCe).
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input required placeholder="Limite de MDFes..." class="form-control"
                                                    type="number" name="mdfes" id="mdfes"
                                                    value="{{ $empresa->limMDFes }}" />
                                                <label>Limite de notas (MDFe)</label>
                                                <div class="invalid-feedback">
                                                    Informe o limite de notas (MDFe).
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">
                                <div class="row">
                                    <div class="col-md-8 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class=form-control type="text" name="name" id="name"
                                                    value="{{ $empresa->name }}" />
                                                <label>Nome</label>
                                                <div class="invalid-feedback">
                                                    Informe um nome.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="input-group has-validation mb-2">
                                            <div class="form-floating">
                                                <input class=form-control type="email" name="email" id="email"
                                                    value="{{ $empresa->email }}" />
                                                <label>Email</label>
                                                <div class="invalid-feedback">
                                                    Informe um email.
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
