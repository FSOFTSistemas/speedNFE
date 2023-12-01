@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset("css/login.css") }}">
    <title>Document</title>
</head>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset("css/login.css") }}">
    <title>Document</title>
</head>

<body>
    <div class="container" >
        <header>
            <div class="logo">
                <img src="{{ asset("css/logo.png") }}">
            </div>
            <nav>
                <a href="{{ route('homePage') }}">Home</a>
                <a href="{{ route('Planos') }}">Planos</a>
                <a href="{{ route('login') }}">Login</a>
            </nav>
        </header>
    </div>
    <div class="img-fundo">
        <div class="img-overlay"></div>
        <img src="{{ asset('css/Mulher-no-Computador.jpg') }}" alt="Logo">
    </div>

    <div class="content">
        <div class="login-card">
            <div class="card-header">
                <form action="{{ $login_url }}" method="post">
                    @csrf

                    {{-- Seu formulário aqui --}}
                    <div class="input-group mb-3">
                        <p><label for="email" class="form-label" id="email_label">Email:</label></p><br><br>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <p><label for="password" class="form-label" id="senha_label">Senha:</label></p><br><br>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="{{ __('adminlte::adminlte.password') }}">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-7">
                            <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}"
                                id="remeber">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : ''
                                    }}>
                                <label for="remember">
                                Lembre-se de mim
                                </label>
                            </div>
                            <br>
                        </div>

                        <div class="col-5">
                            <button type=submit
                                class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                                <span class="fas fa-sign-in-alt"> Entrar</span>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Register link --}}
                @if($register_url)
                <p class="my-0" id="registrar" style="padding-top: 10px;">
                    <a href="{{ route('createMedico') }}"> Registre-se </a>
                </p>
                @endif
            </div>
        </div>
    </div>
</body>

</html>


{{-- @section('auth_footer')
    Password reset link
    @if($password_reset_url)
        <p class="my-0">
            <a href="{{ $password_reset_url }}">
                Esqueci minha senha
            </a>
        </p>
    @endif --}}

    {{-- Register link --}}
    {{-- @if($register_url)
        <p class="my-0">
            <a href="{{ $register_url }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif --}}
{{-- @stop --}}
