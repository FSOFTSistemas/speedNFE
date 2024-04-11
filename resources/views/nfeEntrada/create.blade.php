@extends('adminlte::page')

@section('title', 'Importar NFe')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h4 class="text-dark">Importar NFe</h4>
        </div>
    </div>
    <div class="row">
        <div class="col" style="text-align: end">
            <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <main>
        <section>
            <form action="" method="POST" enctype="multipart/form-data">
                
            </form>
        </section>
    </main>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
@stop
