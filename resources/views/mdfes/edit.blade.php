@extends('adminlte::page')

@section('title', 'Editar MDFe')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col">
            <a href="{{ route('mdfe.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>

    <div class="container">
        @livewire('edit-m-d-fe', ['MDFe' => $nota])
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')

@stop
