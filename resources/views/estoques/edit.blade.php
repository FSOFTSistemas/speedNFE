@extends('adminlte::page')

@section('title', 'Editar Estoque')

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
        --input-focus-border: #80bdff;
        --input-focus-shadow: rgba(0, 3, 58, .25);
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
    .custom-btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff !important;
        font-weight: 500;
        border-radius: 8px;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-primary:hover {
        background-color: #00045e;
        border-color: #00045e;
        transform: translateY(-2px);
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
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 10px 15px;
        height: auto;
    }
    .form-control:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow);
    }
    .product-title {
        font-weight: 500;
        color: var(--text-dark);
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Edição de Estoque</h1>
        </div>
        <div class="col-md-2 text-md-right mt-2 mt-md-0">
            <a href="{{ route('estoque.index') }}" class="btn custom-btn-secondary btn-block">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body">
            <h4 class="product-title text-center mb-4">{{ $estoque->produto->produto }}</h4>
            <form class="needs-validation" novalidate action="{{ route('estoque.update', [$estoque->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="estoque" class="form-label">Estoque Atual</label>
                        <input class="form-control" name="estoque" id="estoque" value="{{ $estoque->estoque_atual }}" required>
                        <div class="invalid-feedback">Informe o estoque atual.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="estoque_anterior" class="form-label">Estoque Anterior</label>
                        <input class="form-control" name="estoque_anterior" id="estoque_anterior" value="{{ $estoque->estoque_anterior }}" required>
                         <div class="invalid-feedback">Informe o estoque anterior.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="entradas" class="form-label">Entradas</label>
                        <input class="form-control" name="entradas" id="entradas" value="{{ $estoque->entradas }}" required>
                        <div class="invalid-feedback">Informe o total de entradas.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="saidas" class="form-label">Saídas</label>
                        <input class="form-control" name="saidas" id="saidas" value="{{ $estoque->saidas }}" required>
                        <div class="invalid-feedback">Informe o total de saídas.</div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6 mx-auto text-center">
                        <button class="btn custom-btn-primary btn-block" type="submit">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@push('js')
    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endpush
