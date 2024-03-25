<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header Responsivo</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('css/Telas_Principais/homePage2.css') }}">
  <link rel="stylesheet" href="styles.css">
</head>
<body>


    <header class="navbar navbar-expand-lg navbar-light bg-transparent position-relative">
        <div class="container">
          <a class="navbar-brand" href="#">
            <img src="{{ asset('css/logo.png') }}">
          </a>
      
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
      
          <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Services</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Contact</a>
              </li>
            </ul>
          </div>
        </div>
      </header>

<div class="container" id="container-2">
  <div class="row mt-md-4" id="row-1"> 
    <div class="col-md-6">
      <img src="{{ asset('css/Nota-fiscal-eletronica-Saiba-como-emitir-removebg-preview.png') }}" class="img-fluid">
    </div>
    <div class="col-md-6">
      <p class="text-large">Emissor de Nota Fiscal Eletrônica e WEB DANFE Online</p>
      <p class="text-small">Conheça os benefícios de ter um Certificado Digital</p>
    </div>
  </div>
</div>


<div class="row"> 
    <div class="container">

        <h3> Planos Exclusivos, Com Muitas Vantagens</h3>
        <h5> "Descubra nossos planos exclusivos repletos de benefícios feitos sob medida para você. Aproveite soluções inteligentes e economize tempo e dinheiro com nossas opções flexíveis. Escolha qualidade, escolha eficiência, escolha nossos planos hoje." </h5>
        <div class="text-center">
            <button type="button" class="button">Conheça os Planos</button>
          </div>
    </div>
   
    </div>

    <div class="main-card">
        <div class="title">Plano de Benefícios</div>
        <div class="sub-card">
          <div class="benefit-title">Benefício 1</div>
          <div class="benefit-description">Descrição do Benefício 1.</div>
        </div>
        <div class="sub-card">
          <div class="benefit-title">Benefício 2</div>
          <div class="benefit-description">Descrição do Benefício 2.</div>
        </div>
        <div class="sub-card">
          <div class="benefit-title">Benefício 3</div>
          <div class="benefit-description">Descrição do Benefício 3.</div>
        </div>
      </div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
