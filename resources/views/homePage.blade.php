<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-5v3YT0auvSjDz7JeF5VpJ0wWJ1R3r1Pj+Za5r+yF3e8sd9pWcTo1g2Bd25gT5toL50Rlh24RZjLOt4L/2ecClcA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                <img id="laptop" src="{{ asset("css/Nota-fiscal-eletronica-Saiba-como-emitir-removebg-preview.png") }}">
            </div>
            <div class="text-right">
                <h1>Emissor de Nota Fiscal Eletrônica e WEB DANFE Online</h1>
                <h4>Conheça os benefícios de ter um <br>Certificado Digital</h4>
                <a href="{{ route('Planos') }}"><button class="verPlanos" > Connheça nossos Planos</button></a>
            </div>
        </div>
    </div>


    <div class="background">
        <div class="container" id="card-plano">
            <div class="panel pricing-table">
            
                
                <div class="pricing-plan">
                    <h2 class="pricing-header"> Emissão de Notas Gratuitas</h2>
                    <ul class="pricing-features">
                        <li class="pricing-features-item"><i class="fa fa-check"></i> Todo mês, até 15 notas sem custo algum</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Atualizações e melhorias constantes sem custo adicional</li>
                    </ul>
                </div>
                
                <div class="pricing-plan">
                    <h2 class="pricing-header">Plano Profissional</h2>
                    <ul class="pricing-features">
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Envio ilimitado do seu plano profissional</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Envio de e-mail com o arquivo XML e DANFE</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Emissão a partir de qualquer computador, sem precisar baixar nada.</li>
                    </ul>
                </div>
                
                <div class="pricing-plan">
                    <h2 class="pricing-header">Comprometimento e Segurança</h2>
                    <ul class="pricing-features">
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Armazenamento dos arquivos em segurança</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Responsabilidade com a legislação</li>
                        <li class="pricing-features-item"><i class="fas fa-check"></i> Cópias de segurança automáticas</li>
                    </ul>
                </div>
                
            
            </div>
        </div>
    </div>
    
    
</body>
</html>
