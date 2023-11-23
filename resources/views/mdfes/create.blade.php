@extends('adminlte::page')

@section('title', 'Emitir MDFe')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Emitir MDFe</h3>
        </div>
    </div>
@stop

@section('content')

    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('mdfe.index') }}">Voltar</a>
        </div>
    </div>

    <div class="container">
        @livewire('m-d-fe')
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')

@stop
