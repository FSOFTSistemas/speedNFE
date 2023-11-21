@extends('adminlte::page')

@section('title', 'Cadastrar de Veículo')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Cadastro de Veículo</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('veiculos.index') }}">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row" style="text-align: center">
                    <div class="col">
                        <h2>Informações do Veículo</h2>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('veiculos.salvar') }}" method="post" class="control-form">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group">
                                <label for="">Descrição</label>
                                <textarea class="form-control" maxlength="512" placeholder="Descrição..." name="descricao" id="descricao" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="placa">Placa *</label>
                                <input type="text" class="form-control" required placeholder="Placa..." name="placa" id="placa">
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="capacidade">Capacidade (Kg) *</label>
                                <input type="number" class="form-control" step="0.1" required placeholder="Capacidade (Kg)..." name="capacidade" id="capacidade">
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="renavam">Renavam *</label>
                                <input type="text" class="form-control" required placeholder="Renavam..." name="renavam" id="renavam">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="float">Tara (kg) *</label>
                                <input type="number" class="form-control" step="0.1" required placeholder="Tara..." name="tara" id="tara">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="capacidade_m3">Capacidade (M³) *</label>
                                <input type="number" class="form-control" step="0.1" required placeholder="Capacidade (M³)..." name="capacidade_m3" id="capacidade_m3">
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
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="">Tipo Rodado *</label>
                                <select class="form-control" required name="tipo_rodado" id="tipo_rodado">
                                    <option value="">-- Selecione um tipo rodado --</option>
                                    @foreach ($tiposCarrocerias as $tipoCarroceria)
                                        <option value="{{ $tipoCarroceria }}">{{ $tipoCarroceria->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="tipo_carroceria">UF do Veículo *</label>
                                <select class="form-control" required name="uf_veiculo" id="uf_veiculo">
                                    <option value="">-- Selecione o UF do Veículo --</option>
                                    @foreach ($tiposCarrocerias as $tipoCarroceria)
                                        <option value="{{ $tipoCarroceria }}">{{ $tipoCarroceria->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="form-group">
                                <label for="float">Tipo Propriedade *</label>
                                <select class="form-control" required name="tipo_propriedade" id="tipo_propriedade">
                                    <option value="">-- Selecione um tipo de propriedade --</option>
                                    @foreach ($tiposCarrocerias as $tipoCarroceria)
                                        <option value="{{ $tipoCarroceria }}">{{ $tipoCarroceria->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="text-align: center">
                        <div class="col">
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
        console.log('Hi!');
    </script>
@stop
