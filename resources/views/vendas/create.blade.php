@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-8 text-center text-lg-left mb-3 mb-lg-0">
            <div class="page-eyebrow">Vendas</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">Emitir Nova NFe</h1>
            <div class="page-subtitle">Preencha os dados da venda e adicione os itens</div>
        </div>
        <div class="col-lg-4 text-center text-lg-right">
            <a class="btn custom-btn btn-outline-secondary" href="{{ route('vendas.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
        </div>
    </div>
@stop

@section('content')
    @livewire('pedido')
@endsection
