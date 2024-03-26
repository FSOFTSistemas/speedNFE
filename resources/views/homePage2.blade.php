<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header Responsivo</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('css/Telas_Principais/homePage2.css') }}">
</head>
<body>

<header class="navbar navbar-expand-lg navbar-light bg-transparent fixed-top ">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="{{ asset('css/logo.png') }}" class="img-fluid" alt="Logo"> <!-- Adicionando a classe "img-fluid" para tornar a imagem responsiva -->
      </a>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto"> 
          <li class="nav-item">
            <a class="nav-link home-link" href="{{ route('homePage2') }}">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Services</a>
          </li>
         
          </li>
        </ul>
      </div>
    </div>
</header>


<div class="fullscreen-image">
  <img src="{{ asset('css/imagem-fundo.jpg') }}" class="img-fluid" alt="Imagem de fundo"> <!-- Adicionando a classe "img-fluid" para tornar a imagem responsiva -->
  <div class="text-overlay-left">
    <h2>Emita Notas Fiscais Facilmente</h2>
    <h5>Agilize suas operações comerciais em Garanhuns com um sistema rápido e seguro.</h5>
    <button class="btn btn-primary btn-square">Saiba Mais</button> <!-- Adicionando um botão após os textos -->
  </div>
  <div class="text-overlay-right">
    <h3>Conheça Nossos Serviços e Seja um de Nossos CLientes</h3>
  </div>
</div>

<div class="container-2">
  <h2 class="text-left">Serviços</h2>
  <div class="row justify-content-around">
    <div class="col-md-4">
      <div class="card grande">
        <div class="overlay"></div>
        <img src="{{ asset('css/Mulher-no-Computador.jpg') }}" alt="Serviço 1" class="img-fluid">
        <div class="card-text">
          <h2>Qualquer coisa para exemplificar</h2>
          <p>dajwnjkanwdjnawjkdnajkwndjanwdjknawjdn</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card grande">
        <div class="overlay"></div>
        <img src="{{ asset('css/Nota-fiscal-eletronica-Saiba-como-emitir-removebg-preview.png') }}" alt="Serviço 2" class="img-fluid">
        <div class="card-text">
          <h2>Qualquer coisa para exemplificar</h2>
          <p>dajwnjkanwdjnawjkdnajkwndjanwdjknawjdn</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card grande">
        <div class="overlay"></div>
        <img src="{{ asset('css/Nota-fiscal-eletronica-Saiba-como-emitir-removebg-preview.png') }}" alt="Serviço 3" class="img-fluid">
        <div class="card-text">
          <h2>Qualquer coisa para exemplificar</h2>
          <p>dajwnjkanwdjnawjkdnajkwndjanwdjknawjdn</p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Scripts -->
<script src="homePage2.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
