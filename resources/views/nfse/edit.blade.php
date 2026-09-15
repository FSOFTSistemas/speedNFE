@extends('adminlte::page')

@section('title', 'Editar NFS-e')

@section('content_header')
    <h1 class="m-0 text-dark">Editar NFS-e #{{ $nfse->id }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('nfse.update', [$nfse->id]) }}" method="POST">
            @csrf
            @method('PUT')
            @include('nfse._form', ['nfse' => $nfse])
        </form>
    </div>
</div>
@stop
