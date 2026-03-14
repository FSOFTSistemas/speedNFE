@extends('adminlte::master')

{{-- Busca a URL do painel principal a partir da configuração do AdminLTE, garantindo que funcione corretamente --}}
@php($home_url = config('adminlte.dashboard_url', 'home'))

{{-- Injeta os estilos CSS customizados para a nova página de login --}}
@section('adminlte_css')
<style>
    /* Importa uma fonte moderna do Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a; /* Azul Marinho profundo */
        --secondary-color: #f8f9fa; /* Branco suave */
        --accent-color: #3498db; /* Azul mais claro para links e foco */
        --text-color: #333;
        --light-text-color: #777;
        --border-color: #dee2e6;
    }

    /* O corpo da página agora é o container principal do efeito */
    .auth-page {
        font-family: 'Poppins', sans-serif;
        background-color: var(--secondary-color);
        overflow: hidden;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        width: 100vw;
    }

    /* Formas geométricas para o efeito de fundo 3D */
    .bg-shape {
        position: fixed;
        border-radius: 50%;
        filter: blur(150px);
        z-index: 0;
    }

    .shape1 {
        width: 400px;
        height: 400px;
        background: rgba(52, 152, 219, 0.3); /* Tom de azul */
        top: -100px;
        left: -100px;
        animation: animateShape 20s infinite alternate;
    }

    .shape2 {
        width: 300px;
        height: 300px;
        background: rgba(10, 37, 64, 0.4); /* Tom de azul marinho */
        bottom: -50px;
        right: -50px;
        animation: animateShape 25s infinite alternate;
    }

    @keyframes animateShape {
        from {
            transform: translate(0, 0) rotate(0deg);
        }
        to {
            transform: translate(100px, 50px) rotate(180deg);
        }
    }

    /* Container Principal do Login */
    .login-container {
        display: flex;
        width: 100%;
        max-width: 1200px;
        min-height: 700px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
        z-index: 1;
        margin: 2rem;
    }

    /* Painel da Imagem (Metade Esquerda) */
    .image-panel {
        flex: 1;
        background-image: url('https://placehold.co/1080x1920/00033a/ffffff?text=SPEED');
        /* IMPORTANTE: Troque a URL acima pela imagem desejada */
        background-size: cover;
        background-position: center;
        transition: all 0.5s ease-in-out;
    }

    /* Painel do Formulário (Metade Direita) */
    .form-panel {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
        background-color: var(--secondary-color);
        transition: all 0.5s ease-in-out;
    }

    .form-content {
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .login-logo {
        margin-bottom: 2rem;
    }

    .login-logo a {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        text-decoration: none;
    }

    .login-box-msg {
        color: var(--light-text-color);
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
    }

    /* Estilo dos Inputs */
    .input-group .form-control {
        border-radius: 8px !important;
        border: 1px solid var(--border-color);
        height: 50px;
        padding-left: 15px;
        transition: all 0.3s;
    }

    .input-group .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        border-color: var(--accent-color);
    }

    .input-group .input-group-text {
        border-radius: 8px !important;
        border: 1px solid var(--border-color);
        background-color: #fff;
    }

    /* Botão de Login */
    .btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        border-radius: 8px;
        padding: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(10, 37, 64, 0.2);
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: #0d3055 !important;
        border-color: #0d3055 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(10, 37, 64, 0.3);
    }

    /* Links e Checkbox */
    .icheck-primary > input:first-child:checked + label::before {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .auth-links {
        margin-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .auth-links a {
        color: var(--light-text-color);
        font-size: 0.9rem;
        text-decoration: none;
        transition: color 0.3s;
    }

    .auth-links a:hover {
        color: var(--primary-color);
        text-decoration: underline;
    }

    /* Mensagens de erro */
    .invalid-feedback {
        text-align: left;
    }

    /* Responsividade para Mobile */
    @media (max-width: 992px) {
        .login-container {
            flex-direction: column;
            min-height: 0;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
            width: 100%;
            height: 100vh;
        }

        .image-panel {
            display: none;
        }

        .form-panel {
            flex: 1;
            justify-content: center;
            padding: 20px;
            background-color: transparent;
        }

        .form-content {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px 20px;
            border-radius: 15px;
        }

        .shape1 { animation: none; top: -50px; left: -150px; }
        .shape2 { animation: none; bottom: -80px; right: -120px; }
    }
</style>
@stop

{{-- Define a classe CSS para o <body> da página --}}
@section('classes_body', 'auth-page')

{{-- Substitui todo o conteúdo do <body> pelo novo layout de login --}}
@section('body')
    <div class="bg-shape shape1"></div>
    <div class="bg-shape shape2"></div>

    <div class="login-container">

        <div class="image-panel">
            {{-- A imagem de fundo é definida via CSS --}}
        </div>

        <div class="form-panel">
            <div class="form-content">

                <div class="login-logo">
                    {{-- Usa a URL do painel principal definida via configuração --}}
                    <a href="{{ route($home_url) }}">
                        <b>Speed</b>NFE
                    </a>
                </div>

                <p class="login-box-msg">Bem-vindo de volta! Acesse sua conta.</p>

                <form action="{{ route('login') }}" method="post">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="E-mail" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="Senha">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                Entrar
                            </button>
                        </div>
                    </div>

                    <div class="auth-links">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Lembrar-me</label>
                        </div>
                        @if (Route::has('password.request'))
                            <p class="mb-0">
                                <a href="{{ route('password.request') }}">
                                    Esqueci minha senha
                                </a>
                            </p>
                        @endif
                    </div>

                </form>

            </div>
        </div>
    </div>
@stop

