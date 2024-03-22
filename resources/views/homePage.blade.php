<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>speedNFE</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/Telas_Principais/homePage.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-md">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('css/logo.png') }}" alt="Logo da Empresa" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto" style="text-align: right">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ route('homePage') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        {{-- <a class="nav-link" href="{{ route('Planos') }}">Planos</a> --}}
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="background">
        <div class="container-1" id="text-home">
            <div class="row">
                <div class="col-md-6 col-xs-10">
                    <img id="laptop" src="{{ asset("css/Nota-fiscal-eletronica-Saiba-como-emitir-removebg-preview.png")}}" class="img-fluid">
                </div>
                <div class="col-md-6 col-xs-10" >
                    <div style="text-align: center">
                        <p id='titulo'>Emissor de Nota Fiscal Eletrônica e WEB DANFE Online</p>
                        <p id='subtitulo'>Conheça os benefícios de ter um Certificado Digital</p>
                        <a href="{{ route('Planos') }}" class="d-block mx-auto mx-md-0"><button class="botão">Conheça nossos Planos</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="hero">
        <div class="container">
            <h2 class="text-center">Nossas Vantagens</h2>
            <div class="card-deck">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">EMISSÃO DE NOTAS GRATUITAS</h3>
                        <ul class="checklist">
                            <li><i class="fas fa-check"></i> TESTE DE EMISSÃO GRATUITO</li>
                            <li><i class="fas fa-check"></i> ATUALIZAÇÕES E MELHORIAS CONSTANTES SEM CUSTO ADICIONAL</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">PLANO PROFISSIONAL</h3>
                        <ul class="checklist">
                            <li><i class="fas fa-check"></i> ENVIO ILIMITADO DO SEU PLANO PROFISSIONAL</li>
                            <li><i class="fas fa-check"></i> ENVIO DE E-MAIL COM O ARQUIVO XML E DANFE</li>
                            <li><i class="fas fa-check"></i> EMISSÃO A PARTIR DE QUALQUER COMPUTADOR, SEM PRECISAR BAIXAR NADA.</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">COMPROMETIMENTO E SEGURANÇA</h3>
                        <ul class="checklist">
                            <li><i class="fas fa-check"></i> ARMAZENAMENTO DOS ARQUIVOS EM SEGURANÇA</li>
                            <li><i class="fas fa-check"></i> RESPONSABILIDADE COM A LEGISLAÇÃO</li>
                            <li><i class="fas fa-check"></i> CÓPIAS DE SEGURANÇA AUTOMÁTICAS</li>
                        </ul>
                    </div>
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

    <!-- Bootstrap JS, jQuery and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
