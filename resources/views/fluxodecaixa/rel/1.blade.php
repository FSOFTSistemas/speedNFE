<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Fluxo de Caixa</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #444;
        }

        p {
            font-size: 14px;
            margin: 5px 0;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .highlight {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Relatório de Fluxo de Caixa</h2>
    <p class="total"><strong>Período:</strong> {{ \Carbon\Carbon::parse($request->data_inicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($request->data_fim)->format('d/m/Y') }}</p>

    @if($request->tipo_relatorio == 'geral')
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Valor (R$)</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dados as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->data)->format('d/m/Y') }}</td>
                        <td>{{ $item->descricao }}</td>
                        <td>{{ number_format($item->valor, 2, ',', '.') }}</td>
                        <td>
                            <span class="{{ $item->tipo == 'Entrada' ? 'highlight' : '' }}">
                                {{ $item->tipo }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($request->tipo_relatorio == 'receitas_despesas')
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Total (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dados as $item)
                    <tr>
                        <td>{{ $item->tipo }}</td>
                        <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($request->tipo_relatorio == 'resumo')
        <p class="total"><strong>Total Receitas:</strong> R$ {{ number_format($dados['total_receitas'], 2, ',', '.') }}</p>
        <p class="total"><strong>Total Despesas:</strong> R$ {{ number_format($dados['total_despesas'], 2, ',', '.') }}</p>
        <p class="total highlight" style="padding: 10px; text-align: center;">
            <strong>Saldo Final:</strong> R$ {{ number_format($dados['saldo_final'], 2, ',', '.') }}
        </p>
    @endif
</body>
</html>