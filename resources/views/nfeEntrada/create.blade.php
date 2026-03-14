@extends('adminlte::page')

@section('title', 'Importar NFe')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h4 class="text-dark">Importar nota de entrada</h4>
        </div>
    </div>
    <div class="row">
        <div class="col" style="text-align: end">
            <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </div>
@stop

@section('content')
    <main>
        <section>
            @livewire('import-products', ['data' => $data])
        </section>
    </main>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
@stop
