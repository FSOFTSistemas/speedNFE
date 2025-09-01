@extends('adminlte::page')

@section('title', 'Inutilizar Nota Fiscal')

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
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .custom-btn-secondary { background-color: #6c757d !important; border-color: #6c757d !important; color: #fff !important; }
    
    .header-buttons .btn { display: block; margin-bottom: 8px; }
    .header-buttons .btn:last-child { margin-bottom: 0; }
    @media (min-width: 992px) {
        .header-buttons .btn { display: inline-block; margin-bottom: 0; margin-left: 8px; }
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
    textarea.form-control {
        height: auto;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Inutilizar Faixa de Numeração</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-secondary" href="{{ $mode == "nfce" ? route('cupom.index') : route('notas.index') }}">
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
                    <form class="needs-validation" novalidate method="POST" @if ($mode == "nfce") action="{{ route('nfce.unuse') }}" @else action="/inutilizar" @endif>
                        @csrf
                        <input type="hidden" value="{{ $empresa }}" name="empresa_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numI" class="form-label">Número Inicial</label>
                                <input type="number" class="form-control" step="1" min="1" name="numI" id="numI" required>
                                <div class="invalid-feedback">
                                    Por favor, informe o número inicial da faixa.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numF" class="form-label">Número Final</label>
                                <input type="number" class="form-control" step="1" min="1" name="numF" id="numF" required>
                                <div class="invalid-feedback">
                                    Por favor, informe o número final da faixa.
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col">
                                <label for="justificativa" class="form-label">Justificativa</label>
                                <textarea class="form-control" name="justificativa" id="justificativa" rows="5" minlength="15" required></textarea>
                                <div class="invalid-feedback">
                                    A justificativa é obrigatória e deve ter no mínimo 15 caracteres.
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 text-center">
                            <button class="btn btn-lg custom-btn custom-btn-success" type="submit">
                                <i class="fas fa-check-circle mr-2"></i>
                                Inutilizar Numeração
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
        // Script de validação padrão do Bootstrap
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')

            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@stop