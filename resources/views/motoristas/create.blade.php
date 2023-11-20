@extends('adminlte::page')

@section('title', 'Cadastrar Motorista')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Cadastrar Motorista</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%;">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('motorista.index') }}">Voltar</a>
        </div>
    </div>

    <div class="container">
        <form action="" method="POST" enctype="multipart/form-data">


        </form>
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
