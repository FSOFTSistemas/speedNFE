@extends('adminlte::page')

@section('title', 'Cadastro de Empresa')

@section('content_header')
    <div class="row text-center">
        <div class="col">
            <h3 class="m-0 text-dark">Registrar Empresa</h3>
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
                    <form class="row g-3 needs-validation" novalidate action="{{ route('salvar_empresa') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
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
                                                        value="{{ old('cpf_cnpj') }}" required />
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
                                                    <input required placeholder="RG/IE..." class="form-control"
                                                        type="text" id="rg_ie" name="rg_ie"
                                                        value="{{ old('rg_ie') }}" />
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
                                                    <input required placeholder=" " class="form-control" type="text"
                                                        id="telefone" name="telefone" maxlength="15"
                                                        value="{{ old('telefone') }}" onkeyup="handlePhone(event)" />
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
                                                        value="{{ old('nome') }}" />
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
                                                    <input required placeholder=" " class="form-control" type="text"
                                                        id="fantasia" name="fantasia" value="{{ old('fantasia') }}" />
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
                                                    <input required placeholder=" " class="form-control" type="text"
                                                        id="cep" name="cep" value="{{ old('cep') }}" />
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
                                                    <input required placeholder="Nº..." class="form-control"
                                                        type="text" id="numero" name="numero"
                                                        value="{{ old('numero') }}" />
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
                                                        <option value="">Selecione um Estado</option>
                                                        <option value='RO'
                                                            @if (old('uf') == 'RO') selected @endif>RO</option>
                                                        <option value='AC'
                                                            @if (old('uf') == 'AC') selected @endif>AC</option>
                                                        <option value='AM'
                                                            @if (old('uf') == 'AM') selected @endif>AM</option>
                                                        <option value='RR'
                                                            @if (old('uf') == 'RR') selected @endif>RR</option>
                                                        <option value='PA'
                                                            @if (old('uf') == 'PA') selected @endif>PA</option>
                                                        <option value='AP'
                                                            @if (old('uf') == 'AP') selected @endif>AP</option>
                                                        <option value='TO'
                                                            @if (old('uf') == 'TO') selected @endif>TO</option>
                                                        <option value='MA'
                                                            @if (old('uf') == 'MA') selected @endif>MA</option>
                                                        <option value='PI'
                                                            @if (old('uf') == 'PI') selected @endif>PI</option>
                                                        <option value='CE'
                                                            @if (old('uf') == 'CE') selected @endif>CE</option>
                                                        <option value='RN'
                                                            @if (old('uf') == 'RN') selected @endif>RN</option>
                                                        <option value='PB'
                                                            @if (old('uf') == 'PB') selected @endif>PB</option>
                                                        <option value='PE'
                                                            @if (old('uf') == 'PE') selected @endif>PE</option>
                                                        <option value='AL'
                                                            @if (old('uf') == 'AL') selected @endif>AL</option>
                                                        <option value='SE'
                                                            @if (old('uf') == 'SE') selected @endif>SE</option>
                                                        <option value='BA'
                                                            @if (old('uf') == 'BA') selected @endif>BA</option>
                                                        <option value='MG'
                                                            @if (old('uf') == 'MG') selected @endif>MG</option>
                                                        <option value='ES'
                                                            @if (old('uf') == 'ES') selected @endif>ES</option>
                                                        <option value='RJ'
                                                            @if (old('uf') == 'RJ') selected @endif>RJ</option>
                                                        <option value='SP'
                                                            @if (old('uf') == 'SP') selected @endif>SP</option>
                                                        <option value='PR'
                                                            @if (old('uf') == 'PR') selected @endif>PR</option>
                                                        <option value='SC'
                                                            @if (old('uf') == 'SC') selected @endif>SC</option>
                                                        <option value='RS'
                                                            @if (old('uf') == 'RS') selected @endif>RS</option>
                                                        <option value='MS'
                                                            @if (old('uf') == 'MS') selected @endif>MS</option>
                                                        <option value='MT'
                                                            @if (old('uf') == 'MT') selected @endif>MT</option>
                                                        <option value='GO'
                                                            @if (old('uf') == 'GO') selected @endif>GO</option>
                                                        <option value='DF'
                                                            @if (old('uf') == 'DF') selected @endif>DF</option>
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
                                                        value="{{ old('ibge') }}" />
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
                                                    <input required placeholder="Rua..." class="form-control"
                                                        type="text" id="rua" name="rua"
                                                        value="{{ old('rua') }}" />
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
                                                        value="{{ old('bairro') }}" />
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
                                                    <input class="form-control" type="text" name="cidade"
                                                        id="cidade" placeholder=" " required
                                                        value="{{ old('cidade') }}">
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
                                                    <textarea class="form-control" placeholder=" " name="complemento" id="complemento" style="height: 100px;">{{ old('complemento') }}</textarea>
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
                                                    <input required placeholder="Nº Última NFe..." class=form-control
                                                        type="number" name="nfe" id="nfe"
                                                        value="{{ old('nfe') }}" />
                                                    <label>Nº da Última NFe</label>
                                                    <div class="invalid-feedback">
                                                        Informe o nº da última NFe.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder="Nº Última MDFe..." class=form-control
                                                        type="number" name="mdfe" id="mdfe"
                                                        value="{{ old('mdfe') }}" />
                                                    <label>Nº da Última MDFe</label>
                                                    <div class="invalid-feedback">
                                                        Informe o nº da última MDFe.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder="Nº Última NFCe..." class=form-control
                                                        type="number" name="nfce" id="nfce"
                                                        value="{{ old('nfce') }}" />
                                                    <label>Nº da Última NFCe</label>
                                                    <div class="invalid-feedback">
                                                        Informe o nº da última NFCe.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2 col-4">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder="Série..." class=form-control
                                                        type="number" name="serie" id="serie"
                                                        value="{{ old('serie') }}" />
                                                    <label>Série</label>
                                                    <div class="invalid-feedback">
                                                        Informe uma série válida.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-8">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <select required class="form-select" name="ambiente" id="ambiente">
                                                        <option value="">Selecione o Ambiente</option>
                                                        <option value="1"
                                                            @if (old('ambiente') == '1') selected @endif>Produção
                                                        </option>
                                                        <option value="2"
                                                            @if (old('ambiente') == '2') selected @endif>Homologação
                                                        </option>
                                                    </select>
                                                    <label>Ambiente</label>
                                                    <div class="invalid-feedback">
                                                        Informe o ambiente válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input placeholder=" " class=form-control type="email"
                                                        name="contador" id="contador" value="{{ old('email') }}" />
                                                    <label>Contador</label>
                                                    <div class="invalid-feedback">
                                                        Informe o contador.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-5">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input placeholder=" " class=form-control type="text"
                                                        name="senha" id="senha" required
                                                        value="{{ old('senha') }}" />
                                                    <label>Senha Cert.</label>
                                                    <div class="invalid-feedback">
                                                        Informe uma senha válida.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7 col-7">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input placeholder=" " accept=".pfx" type="file"
                                                        name="certificado" id="certificado" class="form-control"
                                                        required>
                                                    <label>Certificado</label>
                                                    <div class="invalid-feedback">
                                                        Informe um certificado válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder=" " class="form-control" type="text"
                                                        name="csc" id="csc" value="{{ old('csc') }}">
                                                    <label>CSC</label>
                                                    <div class="invalid-feedback">
                                                        Informe um CSC.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-5">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder=" " class="form-control" type="text"
                                                        value="{{ old('idCsc') }}" name="idCsc" id="idCsc">
                                                    <label>Id Token CSC</label>
                                                    <div class="invalid-feedback">
                                                        Informe um id token CSC.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="limite" role="tabpanel" aria-labelledby="limite-tab">
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder="" class=form-control type="number"
                                                        name="clientes" id="clientes" value="{{ old('clientes') }}" />
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
                                                        name="produtos" id="produtos" value="{{ old('produtos') }}" />
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
                                                        name="nfes" id="nfes" value="{{ old('nfes') }}" />
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
                                                        name="nfces" id="nfces" value="{{ old('nfces') }}" />
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
                                                    <input required placeholder=" " class="form-control" type="number"
                                                        name="mdfes" id="mdfes" value="{{ old('mdfes') }}" />
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
                                                    <input class=form-control type="text" name="name"
                                                        placeholder=" " id="name" required
                                                        value="{{ old('name') }}" />
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
                                                    <input class=form-control type="email" name="email"
                                                        placeholder=" " id="email" required
                                                        value="{{ old('email') }}" />
                                                    <label>Email</label>
                                                    <div class="invalid-feedback">
                                                        Informe um email.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder=" " class=form-control type="text"
                                                        name="confirm_email" id="confirm_email"
                                                        value="{{ old('confirm_email') }}" />
                                                    <label>Confirmação de Email</label>
                                                    <div class="invalid-feedback">
                                                        Informe a confirmação email válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder=" " class=form-control type="password"
                                                        name="password" id="password" value="{{ old('password') }}" />
                                                    <label>Senha</label>
                                                    <div class="invalid-feedback">
                                                        Informe a senha.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder=" " class=form-control type="password"
                                                        name="confirm_password" id="confirm_password"
                                                        value="{{ old('confirm_password') }}" />
                                                    <label>Confirmação de Senha</label>
                                                    <div class="invalid-feedback">
                                                        Informe a confirmação de senha.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <select required class="form-select" name="cargo" id="cargo">
                                                        <option value="">Selecione uma Permissão</option>
                                                        <option value="master"
                                                            @if (old('cargo') == 'master') selected @endif>master
                                                        </option>
                                                        <option value="admin"
                                                            @if (old('cargo') == 'admin') selected @endif>admin
                                                        </option>
                                                        <option value="client-NFe"
                                                            @if (old('cargo') == 'client-NFe') selected @endif>
                                                            Apenas NFe</option>
                                                        <option value="client-NFCe"
                                                            @if (old('cargo') == 'client-NFCe') selected @endif>
                                                            Apenas NFCe</option>
                                                        <option value="client-MDFe"
                                                            @if (old('cargo') == 'client-MDFe') selected @endif>
                                                            Apenas MDFe</option>
                                                        <option value="client-CTe"
                                                            @if (old('cargo') == 'client-CTe') selected @endif>
                                                            Apenas CTe</option>
                                                        <option value="client-advanced1"
                                                            @if (old('cargo') == 'client-advanced1') selected @endif>
                                                            NFe e MDFe</option>
                                                        <option value="client-advanced2"
                                                            @if (old('cargo') == 'client-advanced2') selected @endif>
                                                            NFe e NFCe</option>
                                                        <option value="client-advanced3"
                                                            @if (old('cargo') == 'client-advanced3') selected @endif>
                                                            CTe e MDFe</option>
                                                    </select>
                                                    <label>Permissões</label>
                                                    <div class="invalid-feedback">
                                                        Informe a permissão.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row text-center">
                                    <div class="col">
                                        <button class="btn btn-outline-success w-25" type="submit">Salvar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
        function formatarCpfCnpj(valor) {
            valor = valor.replace(/\D/g, '');
            if (valor.length === 11) {
                return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if (valor.length === 14) {
                return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            } else {
                return valor;
            }
        }

        function somenteNumeros(valor) {
            var numeros = valor.replace(/\D/g, "");
            return numeros;
        }

        document.getElementById("cep_button").addEventListener("click", function(event) {

            event.preventDefault();
            const cep = document.getElementById('cep').value;
            $.ajax({

                url: "https://viacep.com.br/ws/" + somenteNumeros(cep) + "/json/",
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    document.getElementById("cep").value = data.cep;
                    document.getElementById("ibge").value = data.ibge;
                    document.getElementById("rua").value = data.logradouro;
                    document.getElementById("bairro").value = data.bairro;
                    document.getElementById("uf").value = data.uf;
                    document.getElementById("cidade").value = data.localidade.toUpperCase();
                },
            });
        });

        document.getElementById("cnpj_button").addEventListener("click", function(event) {
            event.preventDefault();

            const cnpj = document.getElementById('cpf_cnpj').value;

            $.ajax({
                type: "POST",
                url: "/clientes/cnpj",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    cnpj: somenteNumeros(cnpj),
                },
                success: function(resultado) {
                    if (resultado != 0) {
                        document.getElementById('nome').value = resultado.nome;
                        document.getElementById('fantasia').value = resultado.fantasia;
                        document.getElementById('telefone').value = resultado.telefone;
                        document.getElementById("bairro").value = resultado.bairro;
                        document.getElementById("cidade").value = resultado.municipio.toUpperCase();
                        document.getElementById("rua").value = resultado.logradouro;
                        document.getElementById("uf").value = resultado.uf;
                        document.getElementById("cep").value = resultado.cep;
                        document.getElementById("numero").value = resultado.numero;
                    } else {
                        alert("Cnpj não encontrado!");
                    }
                }
            });
        });

        const handlePhone = (event) => {
            let input = event.target
            input.value = phoneMask(input.value)
        }

        const phoneMask = (value) => {
            if (!value) return ""
            value = value.replace(/\D/g, '')
            value = value.replace(/(\d{2})(\d)/, "($1) $2")
            value = value.replace(/(\d)(\d{4})$/, "$1-$2")
            return value
        }
    </script>
@endsection
