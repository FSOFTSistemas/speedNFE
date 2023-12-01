<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/homePage.css') }}">
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
                <img src="{{ asset("css/Imagem-nota-fiscal.png") }}" alt="Your Image">
            </div>
            <div class="text-right">
                <h1>Emissor de Nota <br>Fiscal Eletrônica <br>e WEB DANFE Online</h1>
            </div>
        </div>
    </div>

</body>
</html>
