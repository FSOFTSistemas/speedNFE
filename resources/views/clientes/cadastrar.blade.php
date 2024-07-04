@extends('adminlte::page')

@section('title', 'Cadastrar Cliente')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3 class="m-0 text-dark">Cadastro de Cliente</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="text-right mb-3">
        <a class="btn btn-secondary" href="{{ route('cliente.index') }}">Voltar</a>
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
                        <form class="row g-3 needs-validation" novalidate action="{{ route('criar_cliente') }}"
                            method="POST">
                            @csrf
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">

                                    <div class="row">
                                        <div class="col-md-4 col-8">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <select class="form-select" name="tipo" id="tipo" required>
                                                        <option value="">Selecione um Tipo de Certificação</option>
                                                        <option value="2"
                                                            @if (old('tipo') == '2') selected @endif>
                                                            Pessoa Jurídica</option>
                                                        <option value="1"
                                                            @if (old('tipo') == '1') selected @endif>
                                                            Pessoa Física</option>
                                                    </select>
                                                    <label for="tipo">Tipo</label>
                                                    <div class="invalid-feedback">
                                                        Informe um tipo válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="cpf_cnpj"
                                                        name="cpf_cnpj" placeholder=" " required
                                                        onblur="this.value = formatarCpfCnpj(this.value);" maxlength="14"
                                                        value="{{ old('cpf_cnpj') }}">
                                                    <label for="cpf_cnpj">CPF/CNPJ</label>
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
                                        <div class="col-md-7 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="nome" id="nome"
                                                        placeholder="Nome de cliente..." value="{{ old('nome') }}"
                                                        required>
                                                    <label for="nome">Nome</label>
                                                    <div class="invalid-feedback">
                                                        Informe um nome válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5 col-12">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="apelido" id="apelido"
                                                        placeholder="Apelido de cliente..." value="{{ old('apelido') }}"
                                                        required>
                                                    <label for="apelido">Apelido/Fantasia</label>
                                                    <div class="invalid-feedback">
                                                        Informe um apelido válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-6">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="limite"
                                                        id="limite" placeholder="Limite..."
                                                        value="{{ old('limite') ?? 0 }}" required>
                                                    <label for="limite">Limite</label>
                                                    <div class="invalid-feedback">
                                                        Informe um limite válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-6">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="rg_ie"
                                                        id="rg_ie" required placeholder="RG/IE..."
                                                        value="{{ old('rg_ie') }}">
                                                    <label for="rg_ie">RG/IE</label>
                                                    <div class="invalid-feedback">
                                                        Informe um RG/IE válido.
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
                                                        class="form-control" placeholder="Telefone..." maxlength="15"
                                                        onkeyup="handlePhone(event)" value="{{ old('telefone') }}"
                                                        required>
                                                    <label for="telefone">Telefone</label>
                                                    <div class="invalid-feedback">
                                                        Informe um telefone válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            @if ($user->cargo == 'master')
                                                <div class="input-group has-validation mb-2">
                                                    <div class="form-floating">
                                                        <select name="empresa" id="empresa" class="form-select"
                                                            required>
                                                            <option value="">Selecione uma Empresa</option>
                                                            @foreach ($empresas as $empresa)
                                                                <option value="{{ $empresa->id }}"
                                                                    @if (old('empresa') == $empresa->id) selected @endif>
                                                                    {{ $empresa->razao }}</option>
                                                            @endforeach
                                                        </select>
                                                        <label for="empresa">Empresa</label>
                                                        <div class="invalid-feedback">
                                                            Informe uma empresa válida.
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <input type="hidden" name="empresa" id="empresa"
                                                    value="{{ $user->empresa_id }}">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-xs-10">
                                        <label for="codigo" hidden>Código Gerencial</label>
                                        <input class="form-control" type="text" name="codigo" id="codigo"
                                            placeholder="Código gerencial..." value="0" hidden>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-md-4 col-7">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input placeholder="Cep..." class="form-control" type="text"
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

                                        <div class="col-md-4 col-5">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input placeholder="Código IBGE..." class="form-control"
                                                        type="number" id="ibge" name="ibge"
                                                        value="{{ old('ibge') }}" />
                                                    <label for="cod_ibge">Cód. IBGE</label>
                                                    <div class="invalid-feedback">
                                                        Informe um código IBGE válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-9 col-9">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="rua"
                                                        id="rua" value="{{ old('rua') }}" placeholder="Rua...">
                                                    <label for="rua">Rua</label>
                                                    <div class="invalid-feedback">
                                                        Informe uma rua válida.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-3">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="numero"
                                                        id="numero" value="{{ old('numero') }}" placeholder="Nº...">
                                                    <label for="numero">Nº</label>
                                                    <div class="invalid-feedback">
                                                        Informe um nº válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5 col-8">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="bairro"
                                                        id="bairro" value="{{ old('bairro') }}"
                                                        placeholder="Bairro...">
                                                    <label for="bairro">Bairro</label>
                                                    <div class="invalid-feedback">
                                                        Informe um bairro válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5 col-8">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <input class="form-control" type="text" name="cidade"
                                                        id="cidade" value="{{ old('cidade') }}"
                                                        placeholder="Cidade...">
                                                    <label for="cidade">Cidade</label>
                                                    <div class="invalid-feedback">
                                                        Informe uma cidade válida.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-4">
                                            <div class="input-group has-validation mb-2">
                                                <div class="form-floating">
                                                    <select name="uf" id="uf" class="form-select">
                                                        <option value="">Selecione um Estado</option>
                                                        <option value="AL"
                                                            @if (old('uf') == 'AL') selected @endif>AL
                                                        </option>
                                                        <option value="AM"
                                                            @if (old('uf') == 'AM') selected @endif>AM
                                                        </option>
                                                        <option value="AP"
                                                            @if (old('uf') == 'AP') selected @endif>AP
                                                        </option>
                                                        <option value="BA"
                                                            @if (old('uf') == 'BA') selected @endif>BA
                                                        </option>
                                                        <option value="CE"
                                                            @if (old('uf') == 'CE') selected @endif>CE
                                                        </option>
                                                        <option value="DF"
                                                            @if (old('uf') == 'DF') selected @endif>DF
                                                        </option>
                                                        <option value="ES"
                                                            @if (old('uf') == 'ES') selected @endif>ES
                                                        </option>
                                                        <option value="GO"
                                                            @if (old('uf') == 'GO') selected @endif>
                                                            GO</option>
                                                        <option value="MA"
                                                            @if (old('uf') == 'MA') selected @endif>
                                                            MA</option>
                                                        <option value="MG"
                                                            @if (old('uf') == 'MG') selected @endif>
                                                            MG</option>
                                                        <option value="MS"
                                                            @if (old('uf') == 'MS') selected @endif>
                                                            MS</option>
                                                        <option value="MT"
                                                            @if (old('uf') == 'MT') selected @endif>
                                                            MT</option>
                                                        <option value="PA"
                                                            @if (old('uf') == 'PA') selected @endif>
                                                            PA</option>
                                                        <option value="PB"
                                                            @if (old('uf') == 'PB') selected @endif>
                                                            PB</option>
                                                        <option value="PE"
                                                            @if (old('uf') == 'PE') selected @endif>
                                                            PE</option>
                                                        <option value="PI"
                                                            @if (old('uf') == 'PI') selected @endif>
                                                            PI</option>
                                                        <option value="PR"
                                                            @if (old('uf') == 'PR') selected @endif>
                                                            PR</option>
                                                        <option value="RJ"
                                                            @if (old('uf') == 'RJ') selected @endif>
                                                            RJ</option>
                                                        <option value="RN"
                                                            @if (old('uf') == 'RN') selected @endif>
                                                            RN</option>
                                                        <option value="RO"
                                                            @if (old('uf') == 'RO') selected @endif>
                                                            RO</option>
                                                        <option value="RR"
                                                            @if (old('uf') == 'RR') selected @endif>
                                                            RR</option>
                                                        <option value="RS"
                                                            @if (old('uf') == 'RS') selected @endif>
                                                            RS</option>
                                                        <option value="SC"
                                                            @if (old('uf') == 'SC') selected @endif>
                                                            SC</option>
                                                        <option value="SE"
                                                            @if (old('uf') == 'SE') selected @endif>
                                                            SE</option>
                                                        <option value="SP"
                                                            @if (old('uf') == 'SP') selected @endif>
                                                            SP</option>
                                                        <option value="TO"
                                                            @if (old('uf') == 'TO') selected @endif>
                                                            TO</option>
                                                    </select>
                                                    <label for="estado">Estado</label>
                                                    <div class="invalid-feedback">
                                                        Informe um estado válido.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-outline-success w-25">Salvar</button>
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
            const tipo = document.getElementById('tipo').value;

            if (tipo == 2) {
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
                            document.getElementById('apelido').value = resultado.fantasia;
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

            } else {
                alert("Tipo deve ser Pessoa Jurídica!");
            }
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
