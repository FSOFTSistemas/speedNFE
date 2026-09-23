@extends('adminlte::page')

@section('title', 'Emitir NFCe')

@section('content_header')
@stop

@section('content')
    <main>
        <section>
            @livewire('n-f-ce')
        </section>
    </main>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/pdv.css') }}?v={{ filemtime(public_path('css/pdv.css')) }}">
@stop

@section('js')
    <script src="{{ asset('js/pdv.js') }}?v={{ filemtime(public_path('js/pdv.js')) }}" defer></script>
@stop
