@extends('adminlte::page')

@section('title', 'Cadastrar Categoria')

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
        color: #fff;
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
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 10px 15px;
        height: auto;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow);
    }
    
    /* Estilos customizados para o Select2 */
    .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
        height: calc(1.5em + .75rem + 12px) !important;
        padding: 8px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem + 10px) !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--input-focus-border) !important;
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow) !important;
    }
    .select2-dropdown {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        line-height: normal !important;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Cadastro de Categoria</h1>
        </div>
        <div class="col-md-2 text-md-right mt-2 mt-md-0">
            <a href="{{ route('categoria.index') }}" class="btn custom-btn-secondary btn-block">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body">
            <form class="needs-validation" novalidate action="{{ route('salvar_categoria') }}" method="post">
                @csrf

                @if ($empresa == 1)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                           <label for="empresa" class="form-label">Empresa</label>
                           <select class="form-select select2-basic" name="empresa" id="empresa" required>
                               <option value="" disabled selected>Selecione uma Empresa</option>
                               @foreach ($empresas as $emp)
                                   <option value="{{ $emp->id }}" @if (old('empresa') == $emp->id) selected @endif>{{ $emp->fantasia }}</option>
                               @endforeach
                           </select>
                           <div class="invalid-feedback">
                               Informe uma empresa.
                           </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="descricao" class="form-label">Descrição da Categoria</label>
                        <textarea class="form-control" name="descricao" id="descricao" rows="4" required placeholder="Digite o nome da categoria">{{ old('descricao') }}</textarea>
                        <div class="invalid-feedback">
                            Informe uma descrição válida.
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mx-auto text-center">
                        <button type="submit" class="btn custom-btn-primary btn-block">Salvar Categoria</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa o Select2
            $('.select2-basic').select2({
                placeholder: "Selecione uma opção",
                allowClear: true,
                width: '100%'
            });
        });

        // Validação Bootstrap
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
