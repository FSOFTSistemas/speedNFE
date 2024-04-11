<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Relatório de Vendas</h1>

<p>Status: {{ $estado }}</p>
<p>Data Início: {{ $dataInicio }}</p>
<p>Data Fim: {{ $dataFim }}</p>

<table>
    <thead>
        <tr>
            <th>Nº Nota</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Situação</th>
            <th>Valor Total</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($vendas) && !empty($vendas))
        @foreach ($vendas as $venda)
            <tr>
                <td>{{ $venda->numero_nfe }}</td>
                <td>{{ $venda->data }}</td>
                <td>{{ $venda->cliente }}</td>
                <td>{{ $venda->estado }}</td>
                <td>{{ $venda->total }}</td>
            </tr>
        @endforeach
    @else
        <p>Nenhuma venda encontrada.</p>
    @endif
    
    </tbody>
</table>

</body>
</html>