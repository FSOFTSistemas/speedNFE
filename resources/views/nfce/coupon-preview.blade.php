@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="pt-br" class="m-2">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ date('d-m-Y') . '_' . $cupom->nroCupom }}</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        .items-table {
            width: 100%;
            font-size: 60%;
        }

        .items-rows {
            border-top: 1px solid #ddd;
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
            font-size: 16px;
            color: rgba(0, 0, 0, 0.505);
            pointer-events: none;
        }
    </style>
</head>

<body>
    <header>
        <section class="coupon-header text-center">
            <div class="row">
                <div class="col">
                    {{ $cupom->empresa->razao }}
                </div>
            </div>

            <div class="row">
                <div class="col">
                    CNPJ: {{ $cupom->empresa->cpf_cnpj }} IE: {{ $cupom->empresa->rg_ie }}
                </div>
            </div>

            <div class="row">
                <div class="col text-break">
                    {{ $cupom->empresa->endereco->rua }}, {{ $cupom->empresa->endereco->numero }}
                    {{ strtoupper($cupom->empresa->endereco->bairro) }}
                    {{ strtoupper($cupom->empresa->endereco->cidade) }}-{{ $cupom->empresa->endereco->uf }}
                </div>
            </div>

            <div class="row">
                <div class="col">
                    Fone: {{ $cupom->empresa->celular }}
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
                    <th class="w-25">Item</th>
                    <th class="w-100">Descrição</th>
                    <th class="w-25">Qtde</th>
                    <th class="w-25">UN</th>
                    <th class="w-50">Vl Unit</th>
                    <th class="w-50">Vl Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cupom->itens as $index => $item)
                    <tr class="items-rows">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ Str::limit($item->produto->produto, 20, '...') }}</td>
                        <td>{{ $item->qtde }}</td>
                        <td>{{ $item->produto->un }}</td>
                        <td>{{ number_format($item->unitario, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">Qtde total de itens</td>
                    <td class="text-end">{{ count($cupom->itens) }}</td>
                </tr>
                <tr>
                    <td colspan="5">Valor Total R$</td>
                    <td class="text-end">{{ number_format($cupom->total, 2) }}</td>
                </tr>
                <div class="position-fixed start-50 text-center translate-middle w-100">
                    <div class="watermark"><strong>SEM VALOR FISCAL</strong></div>
                </div>
                <tr>
                    <td colspan="5">Desconto R$</td>
                    <td class="text-end">{{ number_format($cupom->desconto, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5">Frete R$</td>
                    <td class="text-end">0,00</td>
                </tr>
                <tr>
                    <td colspan="5"><strong class="fs-6">Valor a Pagar R$</strong></td>
                    <td class="text-end"><strong class="fs-6">{{ number_format($cupom->total, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="6">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">FORMA PAGAMENTO</td>
                    <td class="text-end" colspan="3">VALOR PAGO R$</td>
                </tr>
                @foreach ($cupom->formasPagamento as $formaPagamento)
                    <tr>
                        <td colspan="5">{{ $formaPagamento->forma }}</td>
                        <td class="text-end">{{ number_format($formaPagamento->valor, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5">Troco R$</td>
                    <td class="text-end">{{ number_format($cupom->troco, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        <hr>

        <section class="coupon-client text-center">
            <div class="row">
                <div class="col">
                    @if ($cupom->cliente)
                        {{ $cupom->cliente->nome }} - {{ $cupom->cliente->cpf_cnpj }}
                    @else
                        CONSUMIDOR
                    @endif
                </div>
            </div>

            @if ($cupom->cliente)
                <div class="row">
                    <div class="col">
                        {{ $cupom->cliente->endereco->rua }}, {{ $cupom->cliente->endereco->numero }}
                        {{ strtoupper($cupom->cliente->endereco->bairro) }}
                        {{ strtoupper($cupom->cliente->endereco->cidade) }}-{{ $cupom->cliente->endereco->uf }}
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col">
                    <b>{{ $cupom->data }}</b>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
