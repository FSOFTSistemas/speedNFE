<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS" crossorigin="anonymous">
    <!-- Inclua os arquivos CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/Telas_Principais/homePage.css') }}">
    <title>speedNFE</title>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('css/logo.png') }}" alt="Logo da Empresa" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ route('homePage') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('Planos') }}">Planos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Seção de Formas Geométricas com Cores de Fundo -->
    <section class="shapes-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="shape-container">
                        <div class="shape shape1">
                            <img src="caminho/para/imagem1.jpg" alt="Imagem 1" class="shape-img">
                            <div class="shape-text">
                                <h3>Título 1</h3>
                                <p>Subtítulo 1</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shape-container">
                        <div class="shape shape2">
                            <img src="caminho/para/imagem2.jpg" alt="Imagem 2" class="shape-img">
                            <div class="shape-text">
                                <h3>Título 2</h3>
                                <p>Subtítulo 2</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="hero">
        <div class="container">
            <h2>Planos de Serviço</h2>
            <div class="card-deck">
                <div class="card">
                    <h3>Plano Básico</h3>
                    <p>Descrição do plano básico e suas vantagens.</p>
                </div>
                <div class="card">
                    <h3>Plano Padrão</h3>
                    <p>Descrição do plano padrão e suas vantagens.</p>
                </div>
                <div class="card">
                    <h3>Plano Premium</h3>
                    <p>Descrição do plano premium e suas vantagens.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <ul class="footer-links">
                        <li><a href="#">Termos de Serviço</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                        <li><a href="#">Contato</a></li>
                    </ul>
                </div>
                <div class="col-md-6 text-md-right">
                    <p>&copy; 2024 Nome da Empresa. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Inclua os arquivos JS do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
