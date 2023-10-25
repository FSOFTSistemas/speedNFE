<!DOCTYPE html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <div class="text-center">
        Relátorio de Notas Fiscais Eletrônicas
    </div>

    <div class="row">
        <div class="col-6">
            @if($empresa != '')
                <strong>{{$empresa->fantasia}}</strong>
            @endif
            <p>Data da emissão: {{date('d/m/Y')}}</p>
            </div>
        </div>
    </div>
    <br>
    <hr>

    <style>
        .serie{
            width: 5%;
        }
        .numero{
            width: 9%;
        }
        .emissao{
            width: 13%;
        }
        .chave{
            width: 55%;
        }
        .status{
            width: 12%;
        }
        .valor{
            width: 6%;
        }
    </style>

    <table>
        <thead>
            <th>Série</th>
            <th>Número</th>
            <th>Emissão</th>
            <th>Chave</th>
            <th>Status</th>
            <th>Valor</th>
        </thead>
        <tbody>
            @php($total = 0)
            @php($valorTotal = 0)
            @foreach($pedidos as $pedido)
                <tr>
                    <td class="serie">b</td>
                    <td class="numero" >{{$pedido->numero_nfe}}</td>
                    <td class="emissao">{{date('d/m/Y', strtotime($pedido->updated_at))}}</td>
                    <td class="chave" >{{$pedido->chave}}</td>
                    <td class="status">{{$pedido->estado}}</td>
                    <td class="valor">{{number_format($pedido->total, 2,',')}}</td>
                    @php($total = $total + 1)
                    @php($valorTotal = $valorTotal + $pedido->total)
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <td>Total  </td>
            <td>de Notas:</td>
            <td>{{$total}}</td>
            <td></td>
            <td>Valor Total: </td>
            <td><strong>R${{number_format($valorTotal, 2, ',')}}</strong></td>
        </tfoot>
    </table>
</body>
</html>