@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3 class="m-0 text-black" width="100%">Visualizar Nota</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('vendas.index') }}">Voltar</a>
        </div>
    </div>

    @livewire('show-pedido', ["pedido" => $pedido])

@endsection