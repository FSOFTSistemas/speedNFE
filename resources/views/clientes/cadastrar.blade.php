@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h5 class="m-0 text-dark">Cadastro de Clientes</h5>
        </div>
    </div>
@stop

@section('content')
    <a href="{{ route('index') }}">
        <button class="btn btn-secondary" style="margin-bottom: 2%">Voltar</button>
    </a>
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
                        <form action="{{ route('criar_cliente') }}" method="POST">
                            @csrf
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">

                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="tipo">Tipo</label>
                                            <select class="form-control" name="tipo" id="tipo" required>
                                                <option value="">Selecione um Tipo de Certificação</option>
                                                <option value="2" @if (old('tipo') == '2') selected @endif>
                                                    Pessoa Jurídica</option>
                                                <option value="1" @if (old('tipo') == '1') selected @endif>
                                                    Pessoa Física</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-xs-10">
                                            <label for="cpf_cnpj">CPF/CNPJ</label>
                                            <div class="input-group">
                                                <input required placeholder="CPF/CNPJ..." class="form-control"
                                                    type="text" id="cpf_cnpj" name="cpf_cnpj"
                                                    onblur="this.value = formatarCpfCnpj(this.value);" maxlength="14"
                                                    value="{{ old('cpf_cnpj') }}" />
                                                <div class="input-group-append">
                                                    <button id="cnpj_button" type="button" class="btn btn-light"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="nome">Nome</label>
                                            <input class="form-control" type="text" name="nome" id="nome"
                                                placeholder="Nome de cliente..." value="{{ old('nome') }}" required>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="apelido">Apelido/Fantasia</label>
                                            <input class="form-control" type="text" name="apelido" id="apelido"
                                                placeholder="Apelido de cliente..." value="{{ old('apelido') }}" required>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="limite">Limite</label>
                                            <input class="form-control" type="text" name="limite" id="limite"
                                                placeholder="Limite..." value="{{ old('limite') ?? 0 }}" required>
                                        </div>

                                        <div class="col-md-6 col-xs-10">
                                            <label for="rg_ie">RG ou IE</label>
                                            <input class="form-control" type="text" name="rg_ie" id="rg_ie"
                                                required placeholder="RG/IE..." value="{{ old('rg_ie') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="telefone">Telefone</label>
                                            <input type="text" id="telefone" name="telefone" class="form-control"
                                                placeholder="Telefone..." maxlength="15" onkeyup="handlePhone(event)"
                                                value="{{ old('telefone') }}" required>
                                        </div>

                                        <div class="col-md-6 col-xs-10">
                                            <label for="empresa">Empresa</label>
                                            <select name="empresa" id="empresa" class="form-control" required>
                                                <option value="">Selecione uma Empresa</option>
                                                @foreach ($empresas as $empresa)
                                                    <option value="{{ $empresa->id }}"
                                                        @if (old('empresa') == $empresa->id) selected @endif>
                                                        {{ $empresa->razao }}</option>
                                                @endforeach
                                            </select>
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
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cep">CEP</label>
                                            <div class="input-group">
                                                <input placeholder="Cep..." class="form-control" type="text"
                                                    id="cep" name="cep" value="{{ old('cep') }}" />
                                                <div class="input-group-append">
                                                    <button class="btn btn-light" type="button" id="cep_button"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="rua">Rua</label>
                                            <input class="form-control" type="text" name="rua" id="rua"
                                                value="{{ old('rua') }}" placeholder="Rua...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="numero">Número</label>
                                            <input class="form-control" type="text" name="numero" id="numero"
                                                value="{{ old('numero') }}" placeholder="Nº...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="bairro">Bairro</label>
                                            <input class="form-control" type="text" name="bairro" id="bairro"
                                                value="{{ old('bairro') }}" placeholder="Bairro...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cidade">Cidade</label>
                                            <input class="form-control" type="text" name="cidade" id="cidade"
                                                value="{{ old('cidade') }}" placeholder="Cidade...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="estado">Estado</label>
                                            <select name="uf" id="uf" class="form-control">
                                                <option value="">Selecione um Estado</option>
                                                <option value="AL" @if (old('uf') == 'AL') selected @endif>AL
                                                </option>
                                                <option value="AM" @if (old('uf') == 'AM') selected @endif>AM
                                                </option>
                                                <option value="AP" @if (old('uf') == 'AP') selected @endif>AP
                                                </option>
                                                <option value="BA" @if (old('uf') == 'BA') selected @endif>BA
                                                </option>
                                                <option value="CE" @if (old('uf') == 'CE') selected @endif>CE
                                                </option>
                                                <option value="DF" @if (old('uf') == 'DF') selected @endif>DF
                                                </option>
                                                <option value="ES" @if (old('uf') == 'ES') selected @endif>ES
                                                </option>
                                                <option value="GO" @if (old('uf') == 'GO') selected @endif>
                                                    GO</option>
                                                <option value="MA" @if (old('uf') == 'MA') selected @endif>
                                                    MA</option>
                                                <option value="MG" @if (old('uf') == 'MG') selected @endif>
                                                    MG</option>
                                                <option value="MS" @if (old('uf') == 'MS') selected @endif>
                                                    MS</option>
                                                <option value="MT" @if (old('uf') == 'MT') selected @endif>
                                                    MT</option>
                                                <option value="PA" @if (old('uf') == 'PA') selected @endif>
                                                    PA</option>
                                                <option value="PB" @if (old('uf') == 'PB') selected @endif>
                                                    PB</option>
                                                <option value="PE" @if (old('uf') == 'PE') selected @endif>
                                                    PE</option>
                                                <option value="PI" @if (old('uf') == 'PI') selected @endif>
                                                    PI</option>
                                                <option value="PR" @if (old('uf') == 'PR') selected @endif>
                                                    PR</option>
                                                <option value="RJ" @if (old('uf') == 'RJ') selected @endif>
                                                    RJ</option>
                                                <option value="RN" @if (old('uf') == 'RN') selected @endif>
                                                    RN</option>
                                                <option value="RO" @if (old('uf') == 'RO') selected @endif>
                                                    RO</option>
                                                <option value="RR" @if (old('uf') == 'RR') selected @endif>
                                                    RR</option>
                                                <option value="RS" @if (old('uf') == 'RS') selected @endif>
                                                    RS</option>
                                                <option value="SC" @if (old('uf') == 'SC') selected @endif>
                                                    SC</option>
                                                <option value="SE" @if (old('uf') == 'SE') selected @endif>
                                                    SE</option>
                                                <option value="SP" @if (old('uf') == 'SP') selected @endif>
                                                    SP</option>
                                                <option value="TO" @if (old('uf') == 'TO') selected @endif>
                                                    TO</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="cod_ibge">Cód. IBGE</label>
                                            <input placeholder="Código IBGE..." class="form-control" type="number"
                                                id="ibge" name="ibge" value="{{ old('ibge') }}" />
                                        </div>
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
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@section('js')
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
