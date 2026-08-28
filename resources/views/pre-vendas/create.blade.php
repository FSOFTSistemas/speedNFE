@extends('adminlte::page')

@section('title', 'Nova pré-venda')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="text-uppercase text-muted small font-weight-bold">Comercial</div>
            <h1 class="m-0 text-dark font-weight-bold">Nova pré-venda</h1>
            <p class="mb-0 text-muted">Informe o cliente, os itens e a previsão de pagamento.</p>
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
            <a href="{{ route('pre-vendas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
        </div>
    </div>
@stop

@section('content')
    <form method="POST" action="{{ route('pre-vendas.store') }}" id="preVendaForm">
        @csrf
        @include('pre-vendas._form')
    </form>
@stop
