@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Cadastro de Empresa</h1>
        </div>
    </div>
@stop

@section('content')
    <a href="{{ route('empresa.index') }}" class="btn btn-secondary" style="margin-bottom: 2%">Voltar</a>

    <div class="content">
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12" style="width: 100%">

                <div class="card card-primary card-outline card-tabs">

                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Empresa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Endereço</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="fiscal-tab" data-toggle="pill" href="#fiscal" role="tab"
                                    aria-controls="fiscal" aria-selected="false">Fiscal</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="limite-tab" data-toggle="pill" href="#limite" role="tab"
                                    aria-controls="limite" aria-selected="false">Limites</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="user-tab" data-toggle="pill" href="#user" role="tab"
                                    aria-controls="user" aria-selected="false">Usuário</a>
                            </li>
                        </ul>
                    </div>
                    <form action="{{ route('salvar_empresa') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">

                                    <input type='hidden' name="action" id="action" value="new" />

                                    <div class="row">
                                        <div class="col">
                                            <label>Razão Social</label>
                                            <input required placeholder="Razão Social..." class="form-control"
                                                type="text" id="nome" name="nome" />
                                        </div>

                                        <div class="col">
                                            <label>Nome Fantasia</label>
                                            <input required placeholder="Nome Fantasia..." class="form-control"
                                                type="text" id="fantasia" name="fantasia" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>CPF ou CNPJ</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input required placeholder="CPF/CNPJ..." class="form-control"
                                                        type="text" id="cpf_cnpj" name="cpf_cnpj"
                                                        onblur="this.value = formatarCpfCnpj(this.value);" maxlength="14" />
                                                </div>
                                                <button id="cnpj_button" type="button" class="btn btn-light"><i
                                                        class="fa fa-search"></i></button>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>RG ou IE</label>
                                            <input required placeholder="Razão RG/IE..." class="form-control"
                                                type="text" id="rg_ie" name="rg_ie" />
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col">
                                            <label>Celular</label>
                                            <input required placeholder="Celular..." class="form-control" type="text"
                                                id="telefone" name="telefone" maxlength="15"
                                                onkeyup="handlePhone(event)" />
                                        </div>

                                    </div>

                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9">
                                                <label>Rua</label>
                                                <input required placeholder="Rua..." class="form-control" type="text"
                                                    id="rua" name="rua" />
                                            </div>
                                            <div class="col-3">
                                                <label>Número</label>
                                                <input required placeholder="Nº..." class="form-control" type="text"
                                                    id="numero" name="numero" />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-8">
                                                <label>Bairro</label>
                                                <input required placeholder="Bairro..." class="form-control"
                                                    type="text" id="bairro" name="bairro" />
                                            </div>
                                            <div class="col">
                                                <label>CEP</label>
                                                <div class="row">
                                                    <div class="col">

                                                        <input required placeholder="Cep..." class="form-control"
                                                            type="text" id="cep" name="cep" />
                                                    </div>
                                                    <button class="btn btn-light" type="button" id="cep_button"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <label>Cidade</label>
                                                <select class="form-control" name="cidade" id="cidade" required>
                                                    <option value="">Selecionar</option>
                                                    @foreach ($cidades as $cidade)
                                                        <option value="{{ $cidade->cidade }}">{{ $cidade->cidade }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label>UF</label>
                                                <select class="form-control" id="uf" name="uf" required
                                                    onchange="updateCities(this.value)">
                                                    <option value="">-- Escolha uma Unidade Federativa --</option>
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

                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col">
                                                <label>Complemento</label>
                                                <input placeholder="Complemento..." class="form-control" type="text"
                                                    id="complemento" name="complemento" />

                                            </div>
                                            <div class="col">

                                                <label>Código IBGE</label>
                                                <input required placeholder="Código IBGE..." class="form-control"
                                                    type="number" id="ibge" name="ibge" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="fiscal" role="tabpanel" aria-labelledby="fiscal-tab">

                                    <div class="row">
                                        <div class="col">
                                            <label>Nº da Última NFe</label>
                                            <input required placeholder="Nº Última NFe..." class=form-control
                                                type="number" name="nfe" id="nfe" />

                                        </div>
                                        <div class="col">
                                            <label>Nº da Última MDFe</label>
                                            <input required placeholder="Nº Última MDFe..." class=form-control
                                                type="number" name="mdfe" id="mdfe" />

                                        </div>
                                        <div class="col">
                                            <label>Série</label>
                                            <input required placeholder="Série..." class=form-control type="number"
                                                name="serie" id="serie" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>Certificado</label><br> <!-- inserir arquivo pfx -->
                                            <input required placeholder="Certificado..." accept=".pfx" type="file"
                                                name="certificado" id="certificado" class="file-upload-default">

                                        </div>
                                        <div class="col">
                                            <label>Senha Certificado</label>
                                            <input required placeholder="Senha Certificado..." class=form-control
                                                type="text" name="senha" id="senha" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>CSC</label>
                                            <input required placeholder="Csc..." class="form-control" type="text"
                                                name="csc" id="csc">


                                        </div>
                                        <div class="col">
                                            <label>Id Token CSC</label>
                                            <input required placeholder="Id Token Csc..." class="form-control"
                                                type="text" name="idCsc" id="idCsc">

                                        </div>
                                        <div class="col">
                                            <label>Ambiente</label>
                                            <select required class="form-control" name="ambiente" id="ambiente">
                                                <option value="">--Selecione um Ambiente--</option>
                                                <option value="1">Produção</option>
                                                <option value="2">Homologação</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="limite" role="tabpanel" aria-labelledby="limite-tab">

                                    <label>Limite de Clientes</label>
                                    <input required placeholder="Limite de Clientes..." class=form-control type="number"
                                        name="clientes" id="clientes" />

                                    <label>Limite de Produtos</label>
                                    <input required placeholder="Limite de Produtos..." class=form-control type="number"
                                        name="produtos" id="produtos" />

                                    <label>Limite de Notas (NFe)</label>
                                    <input required placeholder="Limite de NFes..." class=form-control type="number"
                                        name="nfes" id="nfes" />

                                    <label>Limite de Notas (MDFe)</label>
                                    <input required placeholder="Limite de MDFes..." class=form-control type="number"
                                        name="mdfes" id="mdfes" />

                                </div>

                                <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">

                                    <label>Nome</label>
                                    <input required placeholder="Nome..." class=form-control type="text"
                                        name="name" id="name" />

                                    <label>Email</label>
                                    <input required placeholder="Email..." class=form-control type="text"
                                        name="email" id="email" />

                                    <label>Confirmação de Email</label>
                                    <input required placeholder="Confirmação Email..." class=form-control type="text"
                                        name="confirm_email" id="confirm_email" />

                                    <label>Senha</label>
                                    <input required placeholder="Senha..." class=form-control type="text"
                                        name="password" id="password" />

                                    <label>Confirmação de Senha</label>
                                    <input required placeholder="Confirmação Senha..." class=form-control type="text"
                                        name="confirm_password" id="confirm_password" />
                                </div>
                                <br>
                                <div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-success form-control">Salvar
                                            Empresa</button>
                                    </div>
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
        function updateCities(uf) {
            $.ajax({
                type: "GET",
                url: uf + "/atualizar-cidades",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resultado) {
                    if (resultado.length > 0) {
                        var selectCidades = $("#cidade");
                        selectCidades.empty();
                        selectCidades.append('<option value="">Selecionar</option>');
                        resultado.forEach(function(cidade) {
                            selectCidades.append('<option value="' + cidade.cidade + '">' + cidade
                                .cidade + '</option>');
                        });
                    } else {
                        alert("UF inválido, informe um UF válido!");
                    }
                }
            });
        }

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
                    // console.log(data);
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
                        // console.log(resultado);
                        document.getElementById('nome').value = resultado.nome;
                        document.getElementById('fantasia').value = resultado.fantasia;
                        document.getElementById("bairro").value = resultado.bairro;
                        document.getElementById("cidade").value = resultado.municipio.toUpperCase();
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
