<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    <main>
        <div class="row">
            <div class="col">
                <p>
                    Segue em anexo Arquivo Compactado com os XMLS das NFCes de acordo com os dados abaixo:
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p><b>Empresa:</b> {{ $sender->razao }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p><b>CPNJ:</b> {{ $sender->cpf_cnpj }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p><b>Período:</b> {{ date('m/Y', strtotime($period)) }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p>Enviado por <a href="https://speednfe.com.br/">speednfe.com.br</a></p>
            </div>
        </div>
    </main>
</body>

</html>
