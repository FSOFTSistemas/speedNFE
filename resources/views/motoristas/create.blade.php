@extends('adminlte::page')

@section('title', 'Cadastrar Motorista')

@push('css')
<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --success-color: #28a745;
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
    
    .custom-btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn:hover {
        transform: translateY(-2px);
    }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .custom-btn-secondary { background-color: #6c757d !important; border-color: #6c757d !important; color: #fff !important; }

    .header-buttons .btn { display: block; margin-bottom: 8px; }
    @media (min-width: 992px) {
        .header-buttons .btn { display: inline-block; margin-bottom: 0; }
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Cadastrar Novo Motorista</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-secondary" href="{{ route('motorista.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card card-main">
            <div class="card-body">
                <form action="{{ route('motorista.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input class="form-control" type="text" name="nome" id="nome" required value="{{ old('nome') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input class="form-control" onblur="this.value = formatarCpf(this.value);" maxlength="14" type="text" name="cpf" id="cpf" required value="{{ old('cpf') }}">
                        </div>
                    </div>

                    <div class="row">
                        @if (Auth::user()->empresa_id == 1)
                            <div class="col-md-12 mb-3">
                                <label for="empresaId" class="form-label">Empresa</label>
                                <select class="form-control" name="empresaId" id="empresaId" required>
                                    <option value="">-- Selecione uma Empresa --</option>
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" @if (old('empresaId') == $empresa->id) selected @endif>
                                            {{ $empresa->fantasia }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-lg custom-btn custom-btn-success" type="submit">
                            <i class="fas fa-save mr-2"></i> Salvar Motorista
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function formatarCpf(valor) {
            valor = valor.replace(/\D/g, '');
            if (valor.length === 11) {
                return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else {
                // Retorna o valor sem formatação se não for um CPF completo
                return valor;
            }
        }
    </script>
@stop