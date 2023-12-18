@extends('adminlte::page')

@section('title', 'Cadastrar de Veículo')

@section('content_header')

@stop

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row" style="text-align: center">
                    <div class="col">
                        <strong>
                            <h5>Informações do Veículo</h5>
                        </strong>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('veiculos.salvar') }}" method="post" class="control-form" name="veiculo">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="placa">Placa *</label>
                                <input type="text" class="form-control" required placeholder="Placa..." name="placa"
                                    id="placa" onkeyup="validarPlaca(this, event)" maxlength="8">
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="capacidade">Capacidade (Kg) *</label>
                                <input type="number" class="form-control" step="0.1" required min="0"
                                    placeholder="Capacidade (Kg)..." name="capacidade" id="capacidade">
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="renavan">Renavan *</label>
                                <input type="text" class="form-control" required placeholder="Renavan..." name="renavan"
                                    id="renavan" maxlength="9">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="float">Tara (kg) *</label>
                                <input type="number" class="form-control" step="0.1" required placeholder="Tara..."
                                    min="0" name="tara" id="tara">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="capacidade_m3">Capacidade (M³) *</label>
                                <input type="number" class="form-control" step="0.1" required
                                    placeholder="Capacidade (M³)..." name="capacidade_m3" id="capacidade_m3" min="0">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="tipo_carroceria">Tipo de Carroceria *</label>
                                <select class="form-control" required name="tipo_carroceria" id="tipo_carroceria">
                                    <option value="">-- Selecione um tipo de carroceria --</option>
                                    @foreach ($tiposCarrocerias as $tipoCarroceria)
                                        <option value="{{ $tipoCarroceria }}">{{ $tipoCarroceria->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="float">Tipo de Veículo *</label>
                                <select class="form-control" required name="tipo_veiculo" id="tipo_veiculo">
                                    <option value="">-- Selecione um tipo de veículo --</option>
                                    @foreach ($tiposVeiculos as $tipoVeiculo)
                                        <option value="{{ $tipoVeiculo }}">{{ $tipoVeiculo->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Tipo Rodado *</label>
                                <select class="form-control" required name="tipo_rodado" id="tipo_rodado">
                                    <option value="">-- Selecione um tipo rodado --</option>
                                    @foreach ($tiposRodados as $tipo_rodado)
                                        <option value="{{ $tipo_rodado }}">{{ $tipo_rodado->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="tipo_carroceria">UF do Veículo *</label>
                                <select class="form-control" required name="uf_veiculo" id="uf_veiculo">
                                    <option value="">-- Selecione o UF do Veículo --</option>
                                    @foreach ($ufs as $uf)
                                        <option value="{{ $uf }}">{{ $uf->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="float">Tipo Propriedade *</label>
                                <select class="form-control" required name="tipo_propriedade" id="tipo_propriedade"
                                    onchange="tipoProp(this.value)">
                                    <option value="">-- Selecione um tipo de propriedade --</option>
                                    @foreach ($tiposPropriedades as $tipoPropriedade)
                                        <option value="{{ $tipoPropriedade }}">{{ $tipoPropriedade->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (Auth::user()->empresa_id == 1)
                            <div class="col-md-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Empresa</label>
                                    <select class="form-control" name="empresaId" id="empresaId" required>
                                        <option value="">-- Selecione uma Empresa --</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->fantasia }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <input type="text" name="empresaId" id="empresaId"
                                value="{{ Auth::user()->empresa_id }}">
                        @endif
                    </div>

                    <div class="row" id="proprietario" style="display: none">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <label for="">CPF/CNPJ</label>
                                    <input class="form-control" type="text" name="cpf_cnpj" id="cpf_cnpj"
                                        onblur="this.value = formatarCpfCnpj(this.value);" maxlength="14"
                                        placeholder="CPF/CNPJ...">
                                </div>
                                <div class="col">
                                    <label for="">Inscrição Estadual</label>
                                    <input class="form-control" type="text" name="ie" id="ie"
                                        placeholder="Inscrição estadual...">
                                </div>
                                <div class="col">
                                    <label for="">Isento</label>
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" name="isento" id="isento">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="">Nome Proprietário</label>
                                    <input class="form-control" type="text" name="nome" id="nome"
                                        placeholder="Nome do proprietário...">
                                </div>
                                <div class="col">
                                    <label for="">UF proprietário</label>
                                    <select class="form-control" name="uf_prop" id="uf_prop">
                                        <option value="">Selecionar</option>
                                        @foreach ($ufs as $uf)
                                            <option value="{{ $uf }}">{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">RNTRC</label>
                                    <input class="form-control" type="text" name="rntrc" id="rntrc"
                                        placeholder="RNTRC...">
                                </div>
                                <div class="col">
                                    <label for="">Tipo propritário</label>
                                    <select class="form-control" name="tipo_proprietario" id="tipo_proprietario">
                                        <option value="">Selecionar</option>
                                        @foreach ($tipoProprietarios as $tpProp)
                                            <option value="{{ $tpProp }}">{{ $tpProp }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">Tipo transportador</label>
                                    <select class="form-control" name="tipo_transportador" id="tipo_transportador">
                                        <option value="">Selecionar</option>
                                        @foreach ($tipoTransportadores as $tpTransp)
                                            <option value="{{ $tpTransp }}">{{ $tpTransp }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group">
                                <label for="">Descrição</label>
                                <textarea class="form-control" maxlength="512" placeholder="Descrição..." name="descricao" id="descricao"
                                    cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="text-align: center">
                        <div class="col">
                            <a class="btn btn-secondary" href="{{ route('veiculos.index') }}">Cancelar</a>
                            <button class="btn btn-success" style="width: 25%" type="submit">Salvar</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function validarPlaca(entradaDoUsuario, tecla) {
            if (event.keyCode != 8) {
                var placa = entradaDoUsuario.value;
                placaMaiuscula = placa.toUpperCase();
                document.forms['veiculo']['placa'].value = placaMaiuscula;
                if (placa.length === 3) {
                    placa += "-";
                    document.forms['veiculo']['placa'].value = placa;
                    return true;
                }
            }
        }

        function tipoProp(value) {
            var prop = document.getElementById('proprietario');
            var inputs = prop.querySelectorAll('input, select, textarea');
            if (value === "Terceiro") {
                prop.style.display = 'block';
                inputs.forEach(function(input) {
                    if (input.type != 'checkbox') {
                        input.required = true;
                    }
                });
            } else {
                prop.style.display = 'none';
                inputs.forEach(function(input) {
                    input.required = false;
                });
            }
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
    </script>
@stop
