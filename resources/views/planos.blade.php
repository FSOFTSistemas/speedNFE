<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS" crossorigin="anonymous">
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
            
            <div class="pricing-plan" style="background: linear-gradient(to top, #D98719, #fff)">
              <h2 class="pricing-header" id="Classic" >Classic</h2>
              <ul class="pricing-features" id="p-classic">
                <p class="pricing-features-item"> Número de Usuários: Até 1 usuários.</p>
                <p class="pricing-features-item"> Até 5 notas mensais.</p>
                <p class="pricing-features-item"> Controle Básico: Produtos, Clientes.</p>
                <p class="pricing-features-item">Suporte: E-mail, 48 horas.</p>
                <p class="pricing-features-item">Armazenamento: Até 500MB documentos.</p>
                <p class="pricing-features-item">Atualizações: Segurança, Correções.</p>
                <p class="pricing-features-item">Relatórios Básicos: Vendas, Emissões, Cadastros.</p>
              </ul>
              <span class="pricing-price">R$ 29,99  </span>
              <a href="#/" class="pricing-button">Assinar</a>
            </div>
            

            <div class="pricing-plan" style="background: linear-gradient(to top, #FFD700, #fff)">
              <h2 class="pricing-header" id="Premium">Premium</h2>
              <ul class="pricing-features" id="p-premium">
                <p class="pricing-features-item">Usuários: Ilimitados.</p>
                <p class="pricing-features-item">Notas: Emissões ilimitadas.</p>
                <p class="pricing-features-item">Controle Total: Produtos, Clientes, Veículos, Motoristas.</p>
                <p class="pricing-features-item">Suporte Prioritário: 24/7, Telefone, E-mail.</p>
                <p class="pricing-features-item">Armazenamento: Ilimitado, Documentos.</p>
                <p class="pricing-features-item">Atualizações: Acesso imediato, Novas funcionalidades.</p>
                <p class="pricing-features-item">Relatórios Avançados: Dashboards Interativos.</p>
                <p class="pricing-features-item">Integrações: ERP, Gestão Empresarial.</p>
              </ul>
              <span class="pricing-price">R$ 89,99</span>
              <a href="#/" class="pricing-button">Assinar</a>
            </div>


            <div class="pricing-plan" style="background: linear-gradient(to top, #c0c0c0, #fff)">
              <h2 class="pricing-header" id="Advanced">Advanced</h2>
              <ul class="pricing-features" id="p-advanced">
                <p class="pricing-features-item">Número de Usuários: Até 2 usuários simultâneos.</p>
                <p class="pricing-features-item">Notas: Até 30 mensais.</p>
                <p class="pricing-features-item">Controle Avançado: Produtos, Clientes, Veículos.</p>
                <p class="pricing-features-item">Suporte: E-mail, 24 horas.</p>
                <p class="pricing-features-item">Armazenamento: Até 2GB documentos.</p>
                <p class="pricing-features-item">Atualizações: Segurança, Correções, Funcionalidades.</p>
                <p class="pricing-features-item">Relatórios Personalizáveis: Avançados, Dashboards.</p>
                <p class="pricing-features-item">Integrações: ERP, Sistemas Empresariais.



                </p>
              </ul>
              <span class="pricing-price" style="margin-left: 30px">R$ 59,99</span>
              <a href="#/" class="pricing-button is-featured" style="margin-left: 30px">Assinar</a>
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
                <h3 class="titleFooter">Endereço</h3>
               
                    
                <p><i class="fas fa-map-marker-alt" id="location"></i> Rua Luiz Brito N° 53, Centro, Garanhuns-PE</p>
                <div class="mapouter">
                  <div class="gmap_canvas">
                      <iframe width="770" height="510" id="gmap_canvas" src="https://maps.google.com/maps?q=Rua Luiz Brito N° 53, Centro, Garanhuns-PE&t=&z=15&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                      <a href="https://2yu.co">2yu</a><br>
                      <a href="https://embedgooglemap.2yu.co">html embed google map</a>
                  </div>
              </div>

                
            </div>

            <div class="colfooter">
                <h3 class="titleFooter">Redes Sociais</h3>
                <a target="_blank" href="https://api.whatsapp.com/send?phone=5587981753993&text=Ola%20como%20posso%20ajudar%20voce" class="botao"><span> <i class="fab fa-whatsapp"></i> </span></a>
                <a target="_blank" href="https://www.instagram.com/fsoft_sistemas?igsh=OGQ5ZDc2ODk2ZA==" class="botao"><span> <i class="fab fa-instagram"></i> </span></a>
                <a target="_blank" href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" class="botao"><span> <i class="fa-regular fa-envelope"></i> </span></a>
                <a target="_blank" href="https://www.linkedin.com/in/fsoft-sistemas-2a8049238/" class="botao"><span> <i class="fa-brands fa-linkedin-in"></i> </span></a>
            </div>

            <div class="clear"></div>
        </div>

        <div class="main_footer_copy">
            <a target='_blank' href="https://f-softsistemas.com.br/"> SpeedNFE - 2021, todos os direitos reservados. Desenvolvido por:
                FsoftSistemas </a>


        </div>
    </footer>

    
</body>
</html>