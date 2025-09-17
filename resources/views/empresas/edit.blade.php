@extends('adminlte::page')

@section('title', 'Editar Configurações da Empresa')

@push('css')
    <style>
        /* Estilos do Padrão Visual Definido */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #00033a;
            --card-bg: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.08);
            --border-color: #dee2e6;
            --text-dark: #343a40;
            --text-light: #6c757d;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .card-main {
            background: var(--card-bg);
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px var(--shadow-color);
            padding: 30px;
        }

        .custom-btn {
            font-weight: 500;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .custom-btn:hover {
            transform: translateY(-2px);
        }

        .custom-btn-warning {
            background-color: var(--warning-color) !important;
            border-color: var(--warning-color) !important;
            color: #212529 !important;
        }

        .custom-btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }

        .custom-btn-info {
            background-color: var(--info-color) !important;
            border-color: var(--info-color) !important;
            color: #fff !important;
        }

        .header-buttons .btn {
            display: block;
            margin-bottom: 8px;
        }

        @media (min-width: 992px) {
            .header-buttons .btn {
                display: inline-block;
                margin-bottom: 0;
                margin-left: 8px;
            }
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: .5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            height: 48px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
        }

        textarea.form-control {
            height: auto;
        }

        .form-control[disabled],
        .form-control[readonly] {
            background-color: #e9ecef;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--text-light);
            font-weight: 500;
        }

        .nav-tabs .nav-link.active,
        .nav-tabs .nav-item.show .nav-link {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background-color: transparent;
        }

        .input-group-append .btn {
            border-radius: 0 8px 8px 0 !important;
        }
    </style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Edição de Configurações</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            @if (auth()->user()->can('master') && $empresa->id == 1)
                <a class="btn custom-btn custom-btn-info" href="{{ route('empresa.show') }}">Empresas</a>
            @endif
            @if (Auth::user()->cargo == 'master' || Auth::user()->tipo == 'admin')
                <a class="btn custom-btn custom-btn-info" href="{{ route('index_usuario') }}">Usuários</a>
            @endif
            @if (auth()->user()->can('master') && $empresa->id != 1)
                <a class="btn custom-btn custom-btn-secondary" href="{{ route('empresa.show') }}">Voltar</a>
            @endif
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body">
            <ul class="nav nav-tabs" id="tab" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="home-tab" data-toggle="pill"
                        href="#home"><b>Empresa</b></a></li>
                <li class="nav-item"><a class="nav-link" id="profile-tab" data-toggle="pill"
                        href="#profile"><b>Endereço</b></a></li>
                <li class="nav-item"><a class="nav-link" id="fiscal-tab" data-toggle="pill" href="#fiscal"><b>Fiscal</b></a>
                </li>
                @can('master')
                    <li class="nav-item"><a class="nav-link" id="limite-tab" data-toggle="pill"
                            href="#limite"><b>Limites</b></a></li>
                @endcan
            </ul>

            <form class="needs-validation mt-4" novalidate action="{{ route('update_empresa', [$empresa->id]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="tab-content" id="tabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">CPF ou CNPJ</label>
                                <input class="form-control" type="text" value="{{ $empresa->cpf_cnpj }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 mb-3"><label for="nome" class="form-label">Razão Social</label><input
                                    required class="form-control" type="text" id="nome" name="nome"
                                    value="{{ $empresa->razao }}"></div>
                            <div class="col-md-5 mb-3"><label for="fantasia" class="form-label">Nome Fantasia</label><input
                                    required class="form-control" type="text" id="fantasia" name="fantasia"
                                    value="{{ $empresa->fantasia }}"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3"><label for="rg_ie" class="form-label">RG ou IE</label><input
                                    class="form-control" type="text" id="rg_ie" name="rg_ie"
                                    value="{{ $empresa->rg_ie }}"></div>
                            <div class="col-md-4 mb-3"><label for="telefone" class="form-label">Celular</label><input
                                    required class="form-control" type="text" id="telefone" name="telefone"
                                    maxlength="15" onkeyup="handlePhone(event)" value="{{ $empresa->celular }}"></div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="profile" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">CEP</label>
                                <div class="input-group"><input required class="form-control" type="text" id="cep"
                                        name="cep" value="{{ $empresa->endereco->cep }}">
                                    <div class="input-group-append"><button class="btn btn-dark" type="button"
                                            id="cep_button"><i class="fa fa-search"></i></button></div>
                                </div>
                            </div>
                            <div class="col-md-8 mb-3"><label class="form-label">Rua</label><input required
                                    class="form-control" type="text" id="rua" name="rua"
                                    value="{{ $empresa->endereco->rua }}"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">Número</label><input required
                                    class="form-control" type="text" id="numero" name="numero"
                                    value="{{ $empresa->endereco->numero }}"></div>
                            <div class="col-md-5 mb-3"><label class="form-label">Bairro</label><input required
                                    class="form-control" type="text" id="bairro" name="bairro"
                                    value="{{ $empresa->endereco->bairro }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Cidade</label><input
                                    class="form-control" type="text" name="cidade" id="cidade"
                                    value="{{ $empresa->endereco->cidade }}" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">UF</label><select class="form-control"
                                    id="uf" name="uf" required>
                                    @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}"
                                            @if ($empresa->endereco->uf == $uf) selected @endif>{{ $uf }}</option>
                                    @endforeach
                                </select></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Código IBGE</label><input
                                    class="form-control" type="number" id="ibge" name="ibge"
                                    value="{{ $empresa->endereco->codigoIBGE }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Complemento</label>
                                <textarea class="form-control" name="complemento" id="complemento" rows="1">{{ $empresa->endereco->complemento }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="fiscal" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Nº da Última NFe</label><input required
                                    class="form-control" type="number" name="nfe" id="nfe"
                                    value="{{ $empresa->ultimaNFe }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Nº da Última NFCe</label><input required
                                    class="form-control" type="number" name="nfce" id="nfce"
                                    value="{{ $empresa->ultimaNFCe }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Nº da Última MDFe</label><input required
                                    class="form-control" type="number" name="mdfe" id="mdfe"
                                    value="{{ $empresa->ultimaMDFe }}"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mb-3"><label class="form-label">Série</label><input required
                                    class="form-control" type="number" name="serie" id="serie"
                                    value="{{ $empresa->serie }}"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Ambiente</label><select required
                                    class="form-control" name="ambiente" id="ambiente">
                                    <option value="1" @if ($empresa->ambiente == 1) selected @endif>Produção
                                    </option>
                                    <option value="2" @if ($empresa->ambiente == 2) selected @endif>Homologação
                                    </option>
                                </select></div>
                            <div class="col-md-7 mb-3"><label class="form-label">E-mail do Contador</label><input
                                    class="form-control" type="email" name="contador" id="contador"
                                    value="{{ $empresa->contador }}"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">Senha do Certificado</label><input
                                    class="form-control" type="password" name="senha" id="senha"
                                    value="{{ $empresa->senhaCertificado }}"></div>
                            <div class="col-md-9 mb-3"><label class="form-label">Alterar Certificado (.pfx)</label><input
                                    accept=".pfx" type="file" name="certificado" id="certificado"
                                    class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3"><label class="form-label">CSC</label><input required
                                    class="form-control" type="text" name="csc" id="csc"
                                    value="{{ $empresa->csc }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">ID Token CSC</label><input required
                                    class="form-control" type="text" name="idCsc" id="idCsc"
                                    value="{{ $empresa->idCsc }}"></div>
                        </div>
                    </div>

                    @can('master')
                        <div class="tab-pane fade" id="limite" role="tabpanel">
                            <div class="row">
                                <div class="col-md-3 mb-3"><label class="form-label">Limite de Clientes</label><input required
                                        class="form-control" type="number" name="clientes" id="clientes"
                                        value="{{ $empresa->limClientes }}"></div>
                                <div class="col-md-3 mb-3"><label class="form-label">Limite de Produtos</label><input required
                                        class="form-control" type="number" name="produtos" id="produtos"
                                        value="{{ $empresa->limProdutos }}"></div>
                                <div class="col-md-2 mb-3"><label class="form-label">Limite de NFe</label><input required
                                        class="form-control" type="number" name="nfes" id="nfes"
                                        value="{{ $empresa->limNFes }}"></div>
                                <div class="col-md-2 mb-3"><label class="form-label">Limite de NFCe</label><input required
                                        class="form-control" type="number" name="nfces" id="nfces"
                                        value="{{ $empresa->limNFCes }}"></div>
                                <div class="col-md-2 mb-3"><label class="form-label">Limite de MDFe</label><input required
                                        class="form-control" type="number" name="mdfes" id="mdfes"
                                        value="{{ $empresa->limMDFes }}"></div>
                            </div>
                        </div>
                    @endcan
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-lg custom-btn custom-btn-warning" type="submit">
                        <i class="fas fa-save mr-2"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

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
