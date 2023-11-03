@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Cadastro de Clientes</h1>
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
                                    aria-controls="home" aria-selected="true">Informações do Cliente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Endereço</a>
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
                                        <div class="col">
                                            <label for="nome">Nome</label>
                                            <input class="form-control" type="text" name="nome" id="nome"
                                                placeholder="Nome de cliente..." required>
                                        </div>
                                        <div class="col">
                                            <label for="apelido">Apelido/Fantasia</label>
                                            <input class="form-control" type="text" name="apelido" id="apelido"
                                                placeholder="Apelido de cliente..." required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="codigo">Código Gerencial</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                placeholder="Código gerencial..." required>
                                        </div>
                                        <div class="col">
                                            <label for="limite">Limite</label>
                                            <input class="form-control" type="text" name="limite" id="limite"
                                                placeholder="Limite..." required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="cpf_cnpj">CPF/CNPJ</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input class="form-control" type="text" name="cpf_cnpj"
                                                        id="cpf_cnpj" placeholder="CPF/CNPJ..."
                                                        onblur="this.value = formatarCpfCnpj(this.value);" maxlength="14"
                                                        required>
                                                </div>
                                                <button id="cnpj_button" type="button" class="btn btn-light"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="rg_ie">RG ou IE</label>
                                            <input class="form-control" type="text" name="rg_ie" id="rg_ie"
                                                required placeholder="RG/IE...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="tipo">Tipo</label>
                                            <select class="form-control" name="tipo" id="tipo" required>
                                                <option value="">Selecione um Tipo de Certificação</option>
                                                <option value="2">Pessoa Jurídica</option>
                                                <option value="1">Pessoa Física</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="telefone">Telefone</label>
                                            <input type="text" id="telefone" name="telefone" class="form-control"
                                                placeholder="Telefone..." maxlength="15" onkeyup="handlePhone(event)"
                                                required>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <label for="empresa">Empresa</label>
                                            <select name="empresa" id="empresa" class="form-control" required>
                                                <option value="">Selecione uma Empresa</option>
                                                @foreach ($empresas as $empresa)
                                                    <option value="{{ $empresa->id }}">{{ $empresa->razao }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                    <div class="row">
                                        <div class="col">
                                            <label for="rua">Rua</label>
                                            <input class="form-control" type="text" name="rua" id="rua"
                                                placeholder="Rua...">
                                        </div>
                                        <div class="col">
                                            <label for="numero">Número</label>
                                            <input class="form-control" type="text" name="numero" id="numero"
                                                placeholder="Nº...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="bairro">Bairro</label>
                                            <input class="form-control" type="text" name="bairro" id="bairro"
                                                placeholder="Bairro...">
                                        </div>
                                        <div class="col">
                                            <label for="cidade">Cidade</label>
                                            <input class="form-control" type="text" name="cidade" id="cidade"
                                                placeholder="Cidade...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="estado">Estado</label>
                                            <select name="uf" id="uf" class="form-control">
                                                <option value="">Selecione um Estado</option>
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
                                                        id="cep" placeholder="Cep...">
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
                                                <option value="">Selecione uma Cidade</option>
                                                @foreach ($cidades as $cidade)
                                                    <option value="{{ $cidade->ibge }}">{{ $cidade->ibge }} :
                                                        {{ $cidade->cidade }} - {{ $cidade->uf }}</option>
                                                @endforeach
                                            </select>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#ibge').select2();
        });

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
                    console.log(data);
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
                            document.getElementById("ibge").value = resultado.ibge;
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
