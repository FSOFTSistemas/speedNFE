<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset("css/Telas_Principais/planos.css") }}">
    <title>Planos</title>
</head>
<body>

    <div class="container">
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

    <div class="background">
        <div class="container" id="card-plano">
          <div class="panel pricing-table">
            
            <div class="pricing-plan">
              <h2 class="pricing-header">Produto 1</h2>
              <ul class="pricing-features">
                <li class="pricing-features-item"> Descrição</li>
                <li class="pricing-features-item"> Vantagens</li>
              </ul>
              <span class="pricing-price">R$ 100,00</span>
              <a href="#/" class="pricing-button">Assinar</a>
            </div>
            
            <div class="pricing-plan">
              <h2 class="pricing-header">Produto 2</h2>
              <ul class="pricing-features">
                <li class="pricing-features-item"> Descrição</li>
                <li class="pricing-features-item">Vantagens</li>
              </ul>
              <span class="pricing-price">R$ 100,00</span>
              <a href="#/" class="pricing-button is-featured">Assinar</a>
            </div>
            
            <div class="pricing-plan">
              <h2 class="pricing-header">Produto 3</h2>
              <ul class="pricing-features">
                <li class="pricing-features-item">Descrição</li>
                <li class="pricing-features-item">Vantagens</li>
              </ul>
              <span class="pricing-price">R$ 100,00</span>
              <a href="#/" class="pricing-button">Assinar</a>
            </div>
            
          </div>
        </div>
      </div>

    
</body>
</html>