@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3 class="m-0 text-black" width="100%">Editar Nota</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('vendas.index') }}">Cancelar</a>
        </div>
    </div>

    @livewire('edit-pedido', ["pedido" => $pedido])

@endsection