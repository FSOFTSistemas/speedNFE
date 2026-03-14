@extends('adminlte::page')

@section('title', 'Visualizar Estoque')

@push('css')
<style>
    /* Estilos importados para consistência */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --label-color: #495057;
    }
    body {
        font-family: 'Poppins', sans-serif;
    }
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    .custom-btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    
    /* Estilo para a visualização dos dados */
    .data-item {
        margin-bottom: 1.5rem;
    }
    .data-label {
        font-weight: 600;
        color: var(--label-color);
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
    }
    .data-value {
        font-size: 1.1rem;
        color: var(--text-dark);
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        word-wrap: break-word;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Visualizar Estoque</h1>
            <small>{{ $estoque->produto->produto }}</small>
        </div>
        <div class="col-md-2 text-md-right mt-2 mt-md-0">
            <a href="{{ route('estoque.index') }}" class="btn custom-btn-secondary btn-block">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 data-item">
                    <span class="data-label">Estoque Atual</span>
                    <p class="data-value">{{ $estoque->estoque_atual }}</p>
                </div>
                <div class="col-md-6 data-item">
                    <span class="data-label">Estoque Anterior</span>
                    <p class="data-value">{{ $estoque->estoque_anterior }}</p>
                </div>
            </div>
             <div class="row">
                <div class="col-md-6 data-item">
                    <span class="data-label">Total de Entradas</span>
                    <p class="data-value">{{ $estoque->entradas }}</p>
                </div>
                <div class="col-md-6 data-item">
                    <span class="data-label">Total de Saídas</span>
                    <p class="data-value">{{ $estoque->saidas }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
