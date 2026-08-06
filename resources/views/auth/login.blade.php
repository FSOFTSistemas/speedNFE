@extends('adminlte::master')

@section('title', 'Entrar | SpeedNFE')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/login-modern.css') }}">
@stop

@section('classes_body', 'speed-login-page')

@section('body')
    <main class="login-shell">
        <div class="login-ambient" aria-hidden="true">
            <span class="ambient-glow"></span>
        </div>

        <a href="{{ route('homePage') }}" class="login-back-link">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Voltar para o site
        </a>

        <section class="login-experience" aria-label="Acesso ao SpeedNFE">
            <aside class="login-story">
                <div class="login-brand">
                    <img src="{{ asset('site/img/logo.png') }}" alt="">
                    <span>SpeedNFE</span>
                </div>

                <div class="login-story-copy">
                    <span class="login-eyebrow">Gestão fiscal simplificada</span>
                    <h1>Sua empresa.<br><em>Em movimento.</em></h1>
                    <p>Entre para continuar sua operação de onde parou.</p>
                </div>

                <div class="login-story-proof">
                    <strong>+10 anos</strong>
                    <span>simplificando rotinas fiscais</span>
                </div>
            </aside>

            <div class="login-form-panel">
                <div class="login-form-content">
                    <div class="login-mobile-brand">
                        <img src="{{ asset('site/img/logo.png') }}" alt="SpeedNFE">
                    </div>

                    <h2>Bem-vindo de volta</h2>
                    <p class="form-intro">Informe seus dados para acessar o painel.</p>

                    @if (session('status'))
                        <div class="login-alert login-alert-success" role="status">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="post" class="login-form">
                        @csrf

                        <div class="login-field @error('email') has-error @enderror">
                            <label for="email">E-mail</label>
                            <div class="login-input-wrap">
                                <i class="far fa-envelope" aria-hidden="true"></i>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                       placeholder="voce@empresa.com.br" autocomplete="email" required autofocus>
                            </div>
                            @error('email')
                                <span class="login-error" role="alert"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="login-field @error('password') has-error @enderror">
                            <div class="login-label-row">
                                <label for="password">Senha</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}">Esqueci minha senha</a>
                                @endif
                            </div>
                            <div class="login-input-wrap">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                <input id="password" type="password" name="password" placeholder="Sua senha"
                                       autocomplete="current-password" required>
                                <button type="button" class="password-toggle" aria-label="Mostrar senha" aria-pressed="false">
                                    <i class="far fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="login-error" role="alert"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <label class="remember-control" for="remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span aria-hidden="true"><i class="fas fa-check"></i></span>
                            Manter conectado neste dispositivo
                        </label>

                        <button type="submit" class="login-submit">
                            <span>Entrar no SpeedNFE</span>
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>

                    <p class="login-support">Problemas para acessar?
                        <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Preciso+de+ajuda+para+acessar+o+SpeedNFE."
                           target="_blank" rel="noopener">Fale com o suporte</a>
                    </p>
                </div>
            </div>
        </section>
    </main>
@stop

@section('adminlte_js')
    <script>
        (() => {
            const toggle = document.querySelector('.password-toggle');
            const password = document.getElementById('password');

            toggle?.addEventListener('click', () => {
                const showing = password.type === 'text';
                password.type = showing ? 'password' : 'text';
                toggle.setAttribute('aria-pressed', showing ? 'false' : 'true');
                toggle.setAttribute('aria-label', showing ? 'Mostrar senha' : 'Ocultar senha');
                toggle.innerHTML = showing
                    ? '<i class="far fa-eye" aria-hidden="true"></i>'
                    : '<i class="far fa-eye-slash" aria-hidden="true"></i>';
            });

        })();
    </script>
@stop
