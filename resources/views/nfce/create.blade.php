@extends('adminlte::page')

@section('title', 'Emitir NFCe')

@section('content_header')
    <div class="row" style="text-align: end">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('cupom.index') }}">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <main>
        <section>
            @livewire('n-f-ce')
        </section>
    </main>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
@stop
