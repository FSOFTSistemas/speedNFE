<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>DRE - Demonstrativo de Resultado</title>
    <style>
        /* Estilos para um relatório profissional */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
            color: #00033a; /* Nosso azul padrão */
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 11px;
        }
        .dre-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .dre-table th, .dre-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        /* Cabeçalho da Tabela com nosso azul */
        .dre-table thead th {
            background-color: #00033a; /* Nosso azul padrão */
            color: #ffffff;
            text-transform: uppercase;
            font-size: 12px;
        }
        .dre-table .text-right {
            text-align: right;
        }
        /* Estilo para as linhas principais (Receitas, Despesas) */
        .group-header td {
            font-weight: bold;
            font-size: 16px;
            background-color: #f8f9fa;
        }
        /* Estilo para as linhas de detalhe (sub-categorias) */
        .detail-row td:first-child {
            padding-left: 30px; /* Recuo para criar hierarquia */
        }
        /* Estilo para a linha do resultado final */
        .total-row td {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        /* Cores para os valores */
        .text-success { color: #1a9c54; }
        .text-danger { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DRE - Demonstrativo de Resultado</h2>
        <p>Período de {{ date('d/m/Y', strtotime($dataInicial)) }} a {{ date('d/m/Y', strtotime($dataFinal)) }}</p>
    </div>

    <table class="dre-table">
        <thead>
            <tr>
                <th>Descrição da Conta</th>
                <th class="text-right">Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            {{-- SEÇÃO DE RECEITAS --}}
            <tr class="group-header">
                <td>(+) Receitas</td>
                <td class="text-right text-success">R$ {{ number_format($receitas, 2, ',', '.') }}</td>
            </tr>
            @foreach ($receita_plano as $plano)
                @if ($plano->tipo == 'Entrada')
                    <tr class="detail-row">
                        <td>{{ $plano->descricao }}</td>
                        <td class="text-right">R$ {{ number_format($plano->total, 2, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            {{-- SEÇÃO DE DESPESAS --}}
            <tr class="group-header">
                <td>(-) Despesas</td>
                <td class="text-right text-danger">R$ {{ number_format($despesas, 2, ',', '.') }}</td>
            </tr>
            @foreach ($receita_plano as $plano)
                @if ($plano->tipo == 'Saída')
                    <tr class="detail-row">
                        <td>{{ $plano->descricao }}</td>
                        <td class="text-right">R$ {{ number_format($plano->total, 2, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
            
            {{-- SEÇÃO DO RESULTADO LÍQUIDO --}}
            <tr class="total-row">
                <td>(=) Resultado Líquido do Período</td>
                <td class="text-right {{ $lucro < 0 ? 'text-danger' : 'text-success' }}">
                    R$ {{ number_format($lucro, 2, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>