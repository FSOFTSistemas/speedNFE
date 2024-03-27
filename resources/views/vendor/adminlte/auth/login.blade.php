<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset("css/Telas_Principais/login.css") }}">
    <title>NFE Online - Login</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif; /* Define a fonte padrão */
        }

        .background-image {
            position: relative;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        .background-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            object-fit: cover; /* Garante que a imagem cubra todo o contêiner */
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Ajuste a opacidade conforme necessário */
        }

        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center; /* Centraliza o conteúdo horizontalmente */
        }

        .login-card {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px; /* Define a largura máxima do formulário */
            margin: 0 auto; /* Centraliza o formulário horizontalmente */
        }

        .login-card h2 {
            margin-bottom: 20px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; /* Define uma fonte estilizada */
            color: #333; /* Cor do texto */
        }

        .login-card button {
            background-color: #007bff; /* Cor de fundo azul */
            color: #fff; /* Cor do texto branco */
            font-size: 18px; /* Tamanho da fonte */
            padding: 10px 20px; /* Espaçamento interno */
            border: none; /* Remove a borda */
            border-radius: 5px; /* Borda arredondada */
            cursor: pointer; /* Cursor ao passar */
            transition: background-color 0.3s; /* Transição suave da cor de fundo */
        }

        .login-card button:hover {
            background-color: #0056b3; /* Cor de fundo azul mais escura no hover */
        }

        @media (max-width: 768px) {
            .background-image img {
                content: url("{{ asset('css/images/graficcos.jpg') }}");
                height: 100vh; /* Define a altura da imagem como 100% da altura da tela */
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="{{ asset("css/logo.png") }}" alt="Logo">
            </div>
            <nav>
                <a href="{{ route('homePage') }}">Home</a>
                <a href="{{ route('login') }}">Login</a>
            </nav>
        </div>
    </header>

    <div class="background-image">
        <div class="overlay"></div>
        <img src="{{ asset('css/Mulher-no-Computador.jpg') }}" alt="Background">
    </div>

    <div class="content">
        <div class="login-card">
            <div class="card-header">
                <h2>Login</h2>
                <form action="{{ isset($login_url) ? $login_url : '' }}" method="post">
                    @csrf

                    <!-- Seu formulário de login aqui -->

                    <div class="input-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="email" autofocus>
                        <div class="input-group-append">
                            <span class="fas fa-envelope"></span>
                        </div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
<br><br>
                    <div class="input-group">
                        <label for="password">Senha:</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="senha">
                        <div class="input-group-append">
                            <span class="fas fa-lock"></span>
                        </div>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Lembre-se de mim</label>
                    </div>
<br><br><br>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-sign-in-alt"></span> Entrar
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
