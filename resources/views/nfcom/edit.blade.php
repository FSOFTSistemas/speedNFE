@extends('adminlte::page')

@section('title', 'Editar NFCom')

@push('css')
<style>
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --info-color: #17a2b8;
        --success-color: #28a745;
    }
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    .item-card { border: 1px solid var(--border-color); border-radius: 10px; }
    .custom-btn { font-weight: 500; border-radius: 8px; }
    .custom-btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; color: #fff !important; }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
</style>
@endpush

@section('content_header')
    <h1 class="m-0 text-dark">Editar NFCom #{{ $nfcom->nro }}</h1>
@stop

@section('content')
<div class="card card-main">
    <div class="card-body">
        <form action="{{ route('nfcom.update', [$nfcom->id]) }}" method="POST">
            @csrf
            @method('PUT')
            @include('nfcom._form', ['clientes' => $clientes, 'nfcom' => $nfcom])
            <div class="text-right mt-3">
                <a href="{{ route('nfcom.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn custom-btn custom-btn-success">Atualizar</button>
            </div>
        </form>
    </div>
</div>
@stop
