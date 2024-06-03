<!DOCTYPE html>
<html lang="pt-br" class="m-2">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Fiscal Eletrônica</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        .items-table {
            width: 100%;
            font-size: 60%;
        }

        .coupon-client {
            font-size: 60%;
        }

        .coupon-header {
            font-size: 70%;
        }

        .coupon-header-2 {
            font-size: 65%;
            margin: 3% 0%;
        }

        hr {
            border-top: 2px dashed;
            margin: 1% 0%;
        }

        .watermark {
            font-size: 15px;
            color: rgba(0, 0, 0, 0.505);
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="position-fixed top-50 start-50 text-center translate-middle w-100">
        <div class="watermark"><strong>SEM VALOR FISCAL</strong></div>
    </div>

    <header>
        <section class="coupon-header text-center">
            <div class="row">
                <div class="col">
                    SUA RAZÃO SOCIAL LTDA
                </div>
            </div>

            <div class="row">
                <div class="col">
                    CNPJ: 99.999.999/9999-99 IE: 111111111
                </div>
            </div>

            <div class="row">
                <div class="col">
                    Avenida Getúlio Vargas, 5022 CENTRO BOA VISTA-RR
                </div>
            </div>

            <div class="row">
                <div class="col">
                    Fone: 5555-5555
                </div>
            </div>
        </section>
        <hr>
    </header>

    <main>
        <section class="coupon-header-2 text-center">
            <div class="row">
                <div class="col">
                    Documento Auxiliar da Nota Fiscal de Consumidor Eletronica
                </div>
            </div>
        </section>
        <hr>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Qtde</th>
                    <th>UN</th>
                    <th>Vl Unit</th>
                    <th>Vl Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1111</td>
                    <td>NOTA FISCAL EMITIDA</td>
                    <td>1</td>
                    <td>UNID</td>
                    <td>100,00</td>
                    <td>100,00</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">Qtde total de itens</td>
                    <td class="text-end">1</td>
                </tr>
                <tr>
                    <td colspan="5">Valor Total R$</td>
                    <td class="text-end">100,00</td>
                </tr>
                <tr>
                    <td colspan="5">Desconto R$</td>
                    <td class="text-end">0,00</td>
                </tr>
                <tr>
                    <td colspan="5">Frete R$</td>
                    <td class="text-end">0,00</td>
                </tr>
                <tr>
                    <td colspan="5"><strong class="fs-6">Valor a Pagar R$</strong></td>
                    <td class="text-end"><strong class="fs-6">100,00</strong></td>
                </tr>
                <tr>
                    <td colspan="6">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">FORMA PAGAMENTO</td>
                    <td class="text-end">Dinheiro</td>
                </tr>
                <tr>
                    <td colspan="5">VALOR PAGO R$</td>
                    <td class="text-end">100,00</td>
                </tr>
                <tr>
                    <td colspan="5">Troco R$</td>
                    <td class="text-end">0,00</td>
                </tr>
            </tfoot>
        </table>
        <hr>

        <section class="coupon-client text-center">
            <div class="row">
                <div class="col">
                    CONSUMIDOR - CNPJ 01.234.123/4567-89
                </div>
            </div>

            <div class="row">
                <div class="col">
                    Avenida Seebastião Diniz, 458 CENTRO Boa Vista-RR
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <b>31/05/2024 16:40:55</b>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
