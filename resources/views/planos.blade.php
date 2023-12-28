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


      <footer class="main_footer container_3">
        <div class="content">
            <div class="colfooter">
                <h3 class="titleFooter">Menu</h3>
                <ul>
                    <li><a href="#" title="Página Inícial">Página Inícial</a></li>
                    <li><a href="{{ route('Planos') }}" title="Sobre a Empresa">Planos</a></li>
                    <li><a href="{{ route('login') }}" title="Fale Conosco">Login</a></li>
                </ul>
            </div>

            <div class="colfooter">
                <h3 class="titleFooter">Contato</h3>
                <ul>
                    <li>
                        <p><i class="fab fa-whatsapp" id="envelope"></i> fsoftsistemas@gmail.com</p>
                    </li>
                    <li>
                        <p><i class="fas fa-phone"></i> (87) 98122-0025</p>
                    </li>
                    <li>
                        <p><i class="fab fa-whatsapp"></i> (87) 981753993</p>
                    </li>
                </ul>
            </div>

            <div class="colfooter">
                <h3 class="titleFooter">Redes Sociais</h3>
                <a href="https://www.instagram.com/fsoft_sistemas?igsh=OGQ5ZDc2ODk2ZA==" class="botao"><span> <i class="fab fa-whatsapp"></i> </span></a>
                <a href="#" class="botao"><span> <i class="fab fa-instagram"></i> </span></a>
                <a href="#" class="botao"><span> <i class="fab fa-twitter"></i> </span></a>
                <a href="#" class="botao"><span> <i class="fab fa-pinterest"></i> </span></a>
            </div>

            <div class="clear"></div>
        </div>

        <div class="main_footer_copy">
            <a href="https://f-softsistemas.com.br/"> SpeedNFE - 2021, todos os direitos reservados. Desenvolvido por:
                FsoftSistemas </a>


        </div>
    </footer>

    
</body>
</html>