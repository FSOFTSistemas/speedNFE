@extends('adminlte::page')

@section('title', 'Atualizar Usuário')

@push('css')
{{-- Seus estilos permanecem os mesmos --}}
<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --warning-color: #ffc107;
    }
    body { font-family: 'Poppins', sans-serif; }
    .card-main { background: var(--card-bg); border: none; border-radius: 15px; box-shadow: 0 5px 20px var(--shadow-color); padding: 30px; }
    .custom-btn { font-weight: 500; border-radius: 8px; padding: 10px 20px; transition: all 0.3s ease; }
    .custom-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .custom-btn-warning { background-color: var(--warning-color) !important; border-color: var(--warning-color) !important; color: #212529 !important; }
    .custom-btn-secondary { background-color: #6c757d !important; border-color: #6c757d !important; color: #fff !important; }
    .header-buttons .btn { display: block; margin-bottom: 8px; }
    .header-buttons .btn:last-child { margin-bottom: 0; }
    @media (min-width: 992px) { .header-buttons .btn { display: inline-block; margin-bottom: 0; margin-left: 8px; } }
    .form-label { font-weight: 500; color: #495057; margin-bottom: .5rem; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid var(--border-color); height: 48px; }
    .form-control:focus, .form-select:focus { border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25); }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Atualizar Usuário</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-secondary" href="{{ route('index_usuario') }}">
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
                <form class="needs-validation" novalidate action="{{ route('usuario.update', [$user->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- CAMPOS BÁSICOS (VISÍVEIS PARA TODOS) --}}
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $user->name }}" required>
                        </div>
                    </div>

                    {{-- CAMPO TIPO (VISÍVEL PARA TODOS QUE ACESSAM A PÁGINA) --}}
                     <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select required class="form-select" name="tipo" id="tipo">
                                <option value="usuario" @if ($user->tipo == 'usuario') selected @endif>Usuário</option>
                                <option value="admin" @if ($user->tipo == 'admin') selected @endif>Admin</option>
                            </select>
                            <div class="invalid-feedback">Informe um tipo.</div>
                        </div>
                        <hr>
                    </div>
                    <p class="text-muted">Deixe os campos abaixo em branco para não alterar a senha.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" name="senha" id="senha" minlength="4">
                            <div class="invalid-feedback">A senha precisa ter no mínimo 4 caracteres.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="senha_confirmation" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" name="senha_confirmation" id="senha_confirmation">
                            <div class="invalid-feedback">As senhas não conferem.</div>
                        </div>
                    </div>
                    <hr>

                    {{-- CAMPOS EXCLUSIVOS DO MASTER --}}
                    @if (Auth::user()->cargo == 'master')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="empresa" class="form-label">Empresa</label>
                            <select class="form-select" name="empresa" id="empresa" required>
                                @foreach ($empresas as $emp)
                                    <option value="{{ $emp->id }}" @if ($user->empresa_id == $emp->id) selected @endif>{{ $emp->fantasia }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cargo" class="form-label">Permissões (Cargo)</label>
                            <select required class="form-select" name="cargo" id="cargo">
                                <option value="">Selecione uma Permissão</option>
                                {{-- A opção Master só aparece para o usuário Master --}}
                                @if (Auth::user()->cargo == 'master')
                                    <option value="master" @if ($user->cargo == 'master') selected @endif>Master</option>
                                @endif
                                <option value="admin" @if ($user->cargo == 'admin') selected @endif>Admin</option>
                                <option value="client-NFe" @if ($user->cargo == 'client-NFe') selected @endif>Apenas NFe</option>
                                <option value="client-NFCe" @if ($user->cargo == 'client-NFCe') selected @endif>Apenas NFCe</option>
                                <option value="client-MDFe" @if ($user->cargo == 'client-MDFe') selected @endif>Apenas MDFe</option>
                                <option value="client-NFSe" @if ($user->cargo == 'client-NFSe') selected @endif>Apenas NFSe</option>
                                <option value="client-NFCom" @if ($user->cargo == 'client-NFCom') selected @endif>Apenas NFCom</option>
                                <option value="client-CTe" @if ($user->cargo == 'client-CTe') selected @endif>Apenas CTe</option>
                                <option value="client-advanced1" @if ($user->cargo == 'client-advanced1') selected @endif>NFe e MDFe</option>
                                <option value="client-advanced2" @if ($user->cargo == 'client-advanced2') selected @endif>NFe e NFCe</option>
                                <option value="client-advanced3" @if ($user->cargo == 'client-advanced3') selected @endif>CTe e MDFe</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-lg custom-btn custom-btn-warning">Atualizar Usuário</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
        // Script de validação padrão do Bootstrap (mantido)
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
@stop
