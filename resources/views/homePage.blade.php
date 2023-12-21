<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha512-5v3YT0auvSjDz7JeF5VpJ0wWJ1R3r1Pj+Za5r+yF3e8sd9pWcTo1g2Bd25gT5toL50Rlh24RZjLOt4L/2ecClcA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/Telas_Principais/homePage.css') }}">
    <title>speedNFE</title>
</head>

<body>

    <div class="container">
        <header>
            <div class="logo">
                <img src="{{ asset(" css/logo.png") }}">
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
                <img id="laptop" src="{{ asset(" css/Nota-fiscal-eletronica-Saiba-como-emitir-removebg-preview.png")
                    }}">
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
                    <h2 class="pricing-header"> Emissão de Notas Gratuitas</h2>
                    <ul class="pricing-features">
                        <li class="pricing-features-item">Teste de Emissão Gratuito</li>
                        <li class="pricing-features-item">Atualizações e melhorias constantes sem custo adicional</li>
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
                    <li><a href="#" title="Sobre a Empresa">Planos</a></li>
                    <li><a href="#" title="Fale Conosco">Login</a></li>
                </ul>
            </div>

            <div class="colfooter">
                <h3 class="titleFooter">Contato</h3>
                <ul>
                    <li>
                        <p><i class="fab fa-whatsapp"></i> fsoftsistemas@gmail.com</p>
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