@extends('adminlte::page')

@section('title', 'Editar Veículo')

@section('content_header')
    <h3>Editar Veículo</h3>
@stop

@section('content')
<div class="row" style="margin-bottom: 1%; padding-top: 1%;">
    <div class="col">
        <a class="btn btn-secondary" href="{{ route('veiculos.index') }}">Voltar</a>
    </div>
</div>

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
            <form action="{{ route('veiculos.update', [$veiculo->id]) }}" method="post" class="control-form" name="veiculo">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        <div class="form-group">
                            <label for="placa">Placa *</label>
                            <input type="text" class="form-control" required placeholder="Placa..." name="placa"
                                id="placa"  maxlength="8" value="{{ $veiculo->placa }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="form-group">
                            <label for="capacidade">Capacidade (Kg) *</label>
                            <input type="number" class="form-control" step="0.1" required
                                placeholder="Capacidade (Kg)..." name="capacidade" id="capacidade" value="{{ $veiculo->capacidade }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="form-group">
                            <label for="renavan">Renavan *</label>
                            <input type="text" class="form-control" required placeholder="Renavan..." name="renavan"
                                id="renavan" maxlength="9" value="{{ $veiculo->renavan }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-xs-3">
                        <div class="form-group">
                            <label for="float">Tara (kg) *</label>
                            <input type="number" class="form-control" step="0.1" required placeholder="Tara..."
                                name="tara" id="tara" value="{{ $veiculo->tara }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-3">
                        <div class="form-group">
                            <label for="capacidade_m3">Capacidade (M³) *</label>
                            <input type="number" class="form-control" step="0.1" required
                                placeholder="Capacidade (M³)..." name="capacidade_m3" id="capacidade_m3" value="{{ $veiculo->capacidade_m3 }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-3">
                        <div class="form-group">
                            <label for="tipo_carroceria">Tipo de Carroceria *</label>
                            <select class="form-control" required name="tipo_carroceria" id="tipo_carroceria">
                                <option value="{{ $veiculo->tipo_carroceria }}">{{ $veiculo->tipo_carroceria }}</option>
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
                                <option value="{{ $veiculo->tipo_veiculo }}">{{ $veiculo->tipo_veiculo }}</option>
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
                                <option value="{{ $veiculo->tipo_rodado }}">{{ $veiculo->tipo_rodado }}</option>
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
                                <option value="{{ $veiculo->uf_veiculo }}">{{ $veiculo->uf_veiculo }}</option>
                                @foreach ($ufs as $uf)
                                    <option value="{{ $uf }}">{{ $uf->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-3">
                        <div class="form-group">
                            <label for="float">Tipo Propriedade *</label>
                            <select class="form-control" required name="tipo_propriedade" id="tipo_propriedade">
                                <option value="{{ $veiculo->tipo_propriedade }}">{{ $veiculo->tipo_propriedade }}</option>
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
                    @endif
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
    <script> console.log('Hi!'); </script>
@stop