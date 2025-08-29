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

        th,
        td {
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

        .total-entrada td {
            background-color: #28a745 !important;
            color: white !important;
            font-weight: bold;
        }

        .total-saida td {
            background-color: #dc3545 !important;
            color: white !important;
            font-weight: bold;
        }

        .saida-highlight {
            background-color: #dc3545;
            /* Vermelho */
            color: white;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <h2>Relatório de Fluxo de Caixa</h2>
    <p class="total"><strong>Período:</strong> {{ \Carbon\Carbon::parse($request->data_inicio)->format('d/m/Y') }} a
        {{ \Carbon\Carbon::parse($request->data_fim)->format('d/m/Y') }}</p>

    @if ($request->tipo_relatorio == 'geral')
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
                @php
                    $totalEntrada = 0;
                    $totalSaida = 0;
                @endphp
                @foreach ($dados as $item)
                    @if ($item->tipo == 'Entrada')
                        @php $totalEntrada += $item->valor; @endphp
                    @else
                        @php $totalSaida += $item->valor; @endphp
                    @endif
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->data)->format('d/m/Y') }}</td>
                        <td>{{ $item->descricao }}</td>
                        <td>{{ number_format($item->valor, 2, ',', '.') }}</td>
                        <td>
                            <span class="{{ $item->tipo == 'Entrada' ? 'highlight' : 'saida-highlight' }}">
                                {{ $item->tipo }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-entrada">
                    <td colspan="2"><strong>Total Entradas:</strong></td>
                    <td colspan="2">R$ {{ number_format($totalEntrada, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-saida">
                    <td colspan="2"><strong>Total Saídas:</strong></td>
                    <td colspan="2">R$ {{ number_format($totalSaida, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
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
                @php 
                    $totalReceitas = 0;
                    $totalDespesas = 0;
                @endphp
                @foreach ($dados as $item)
                    @if ($item->tipo == 'Entrada')
                        @php $totalReceitas += $item->total; @endphp
                    @elseif ($item->tipo == 'Saída')
                        @php $totalDespesas += $item->total; @endphp
                    @endif
                    <tr>
                        <td>{{ $item->tipo }}</td>
                        <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total Geral:</strong></td>
                    <td>R$ {{ number_format($totalReceitas - $totalDespesas, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @elseif($request->tipo_relatorio == 'resumo')
        <p class="total total-entrada"><strong>Total Receitas:</strong> R$
            {{ number_format($dados['total_receitas'], 2, ',', '.') }}</p>
        <p class="total total-saida"><strong>Total Despesas:</strong> R$
            {{ number_format($dados['total_despesas'], 2, ',', '.') }}</p>
        <p class="total highlight" style="padding: 10px; text-align: center;">
            <strong>Saldo Final:</strong> R$ {{ number_format($dados['saldo_final'], 2, ',', '.') }}
        </p>
    @endif
</body>

</html>
