@extends('adminlte::page')

@section('title', 'Cadastrar Veículo')

@section('content_header')
    <h3>Cadastro Veículo</h3>
@stop

@section('content')
    <div class="container">
        <form action="{{route('veiculos.salvar')}}" method="post" class="control-form">
        <section>
            <div class="row">  
                <div class="col">
                    <div class="form-group">
                        <label for="placa">Placa *</label>
                        <input type="text" class="form-control" required name="placa" id="placa">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="capacidade">capacidade *</label>
                        <input type="text" class="form-control" required name="capacidade" id="capacidade">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="renavam">renavam *</label>
                        <input type="text" class="form-control" required name="renavam" id="renavam">
                    </div>
                </div>        
            </div>
            <div class="row">  
                <div class="col">
                    <div class="form-group">
                        <label for="float">Tara (kg)</label>
                        <input type="text" class="form-control" required name="tara" id="tara">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="capacidade_m3">Capacidade (M³)</label>
                        <input type="text" class="form-control" required name="capacidade_m3" id="capacidade_m3">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="tipo_carroceiria">Tipo carroceiria</label>
                        <input type="text" class="form-control" required name="tipo_carroceiria" id="tipo_carroceiria">
                    </div>
                </div>        
            </div>

        </section>
        </form>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop