<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/Telas_Principais/homePage.css') }}">
    <title>speedNFE</title>
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
        <div class="container-2" id="text-home">
            <div class="text-left">
                <img id="laptop" src="{{ asset("css/Nota-fiscal-eletronica-Saiba-como-emitir-removebg-preview.png")}}">
            </div>
            <div class="text-right">
                <h1>Emissor de Nota Fiscal Eletrônica e WEB DANFE Online</h1>
                <h4>Conheça os benefícios de ter um <br>Certificado Digital</h4>
                <a href="{{ route('Planos') }}"><button class="verPlanos">Conheça nossos Planos</button></a>
            </div>
        </div>
    </div>


    <div class="background">
        <div class="container" id="card-plano">
            <div class="panel pricing-table card-1">
                <div class="pricing-plan">
                    <h2 class="pricing-header">Emissão de Notas Gratuitas</h2>
                    <ul class="pricing-features">
                        <li class="pricing-features-item"><i class="fas fa-check"></i>Teste de Emissão Gratuito</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i>Atualizações e melhorias
                            constantes sem custo adicional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="background">
        <div class="container" id="card-plano">
            <div class="panel pricing-table card-2">
                <div class="pricing-plan">
                    <h2 class="pricing-header">Plano Profissional</h2>
                    <ul class="pricing-features">
                        <li class="pricing-features-item"><i class="fa   fa-check"></i> Envio ilimitado do seu plano
                            profissional</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Envio de e-mail com o arquivo
                            XML e DANFE</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Emissão a partir de qualquer
                            computador, sem precisar baixar nada.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="background">
        <div class="container" id="card-plano">
            <div class="panel pricing-table card-3">
                <div class="pricing-plan">
                    <h2 class="pricing-header">Comprometimento e Segurança</h2>
                    <ul class="pricing-features">
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Armazenamento dos arquivos em
                            segurança</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Responsabilidade com a
                            legislação</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Cópias de segurança
                            automáticas</li>

                    </ul>
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
                <a target="_blank"
                    href="https://api.whatsapp.com/send?phone=5587981753993&text=Ola%20como%20posso%20ajudar%20voce"
                    class="botao"><span> <i class="fab fa-whatsapp"></i> </span></a>
                <a target="_blank" href="https://www.instagram.com/fsoft_sistemas?igsh=OGQ5ZDc2ODk2ZA=="
                    class="botao"><span> <i class="fab fa-instagram"></i> </span></a>
                <a target="_blank" href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" class="botao"><span> <i
                            class="fa-regular fa-envelope"></i> </span></a>
                <a target="_blank" href="https://www.linkedin.com/in/fsoft-sistemas-2a8049238/" class="botao"><span> <i
                            class="fa-brands fa-linkedin-in"></i> </span></a>
            </div>

            <div class="clear"></div>
        </div>

        <div class="main_footer_copy">
            <a target='_blank' href="https://f-softsistemas.com.br/"> SpeedNFE - 2021, todos os direitos reservados.
                Desenvolvido por:
                FsoftSistemas </a>


        </div>
    </footer>


</body>

</html>