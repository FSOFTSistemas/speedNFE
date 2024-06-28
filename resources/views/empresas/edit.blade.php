@extends('adminlte::page')

@section('title', 'Editar Congigurações')

@section('content_header')
    <div class="text-center">
        <h3 class="m-0 text-dark">Edição de Configurações</h3>
    </div>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col">
            <a class="btn btn-info text-light" href="{{ route('empresa.show') }}">Empresas</a>
            <a class="btn btn-info text-light" href="{{ route('index_usuario') }}">Usuários</a>
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
                            @can('master')
                                <li class="nav-item">
                                    <a class="nav-link" id="limite-tab" data-toggle="pill" href="#limite" role="tab"
                                        aria-controls="limite" aria-selected="false"><b>Limites</b></a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 needs-validation" novalidate
                            action="{{ route('update_empresa', [$empresa->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

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
                                                    <input required placeholder="RG/IE..." class="form-control"
                                                        type="text" id="rg_ie" name="rg_ie"
                                                        value="{{ $empresa->rg_ie }}" />
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
                                                    <input required placeholder="Celular..." class="form-control"
                                                        type="text" id="telefone" name="telefone" maxlength="15"
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
                                                    <input required placeholder="Cep..." class="form-control"
                                                        type="text" id="cep" name="cep"
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
                                                    <input required placeholder="Nº..." class="form-control"
                                                        type="text" id="numero" name="numero"
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
                                                    <input required placeholder="Rua..." class="form-control"
                                                        type="text" id="rua" name="rua"
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
                                                    <input class="form-control" type="text" name="cidade"
                                                        id="cidade" value="{{ $empresa->endereco->cidade }}"
                                                        placeholder="Cidade..." required>
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
                                                    <input required placeholder="Nº Última NFe..." class=form-control
                                                        type="number" name="nfe" id="nfe"
                                                        value="{{ $empresa->ultimaNFe }}" />
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
                                                        value="{{ $empresa->ultimaMDFe }}" />
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
                                                        value="{{ $empresa->ultimaNFCe }}" />
                                                    <label>Nº da Última NFCe</label>
                                                    <div class="invalid-feedback">
                                                        Informe o nº da última NFCe.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-4">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input required placeholder="Série..." class=form-control
                                                        type="number" name="serie" id="serie"
                                                        value="{{ $empresa->serie }}" />
                                                    <label>Série</label>
                                                    <div class="invalid-feedback">
                                                        Informe uma série válida.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-8">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <select required class="form-select" name="ambiente" id="ambiente">
                                                        <option value="{{ $empresa->ambiente }}">
                                                            {{ $empresa->ambiente == 1 ? 'Produção' : 'Homologação' }}
                                                        </option>
                                                        <option value="1">Produção</option>
                                                        <option value="2">Homologação</option>
                                                    </select>
                                                    <label>Ambiente</label>
                                                    <div class="invalid-feedback">
                                                        Informe o ambiente válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-5">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input placeholder="Senha Certificado..." class=form-control
                                                        type="text" name="senha" id="senha"
                                                        value="{{ $empresa->senhaCertificado }}" />
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
                                                        value="{{ $empresa->certificado }}">
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
                                                        name="csc" id="csc" value="{{ $empresa->csc }}">
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
                                                        name="idCsc" id="idCsc" value="{{ $empresa->idCsc }}">
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
                                                        name="nfces" id="nfces"
                                                        value="{{ $empresa->limNFCes }}" />
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
                            </div>

                            <div class="text-center mt-3">
                                <button class="btn btn-outline-success w-25" type="submit">Salvar</button>
                            </div>
                        </form>
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
            // Remove qualquer caracter que não seja número
            valor = valor.replace(/\D/g, '');

            // Verifica se é CPF (11 dígitos)
            if (valor.length === 11) {
                // Formata o CPF ###.###.###-##
                return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            }

            // Verifica se é CNPJ (14 dígitos)
            else if (valor.length === 14) {
                // Formata o CNPJ ##.###.###/####-##
                return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            }

            // Não é CPF nem CNPJ
            else {
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
                    document.getElementById("cidade").value = data.localidade;
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
                        document.getElementById("bairro").value = resultado.bairro;
                        document.getElementById("cidade").value = resultado.municipio;
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
