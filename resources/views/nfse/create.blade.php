@extends('adminlte::page')

@section('title', 'Emitir NFS-e')

@section('content_header')
    <h1 class="m-0 text-dark">Emitir NFS-e</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('nfse.store') }}" method="POST">
            @csrf
            @include('nfse._form')
        </form>
    </div>
</div>
@stop
