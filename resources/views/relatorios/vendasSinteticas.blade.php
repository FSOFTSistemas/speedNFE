<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Vendas</title>
    <style>
        /* Define a fonte e o estilo geral do documento */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* Estiliza o cabeçalho com o período e o título principal */
        .header {
            width: 100%;
            text-align: center;
            position: relative;
            margin-bottom: 30px;
        }

        /* Coloca o período no canto superior direito */
        .periodo {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 10px;
            color: #666;
        }

        /* Estiliza o título principal */
        .titulo {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            padding-top: 20px; /* Garante espaço para o período acima */
        }

        /* Estiliza a tabela de dados */
        .tabela-vendas {
            width: 100%;
            border-collapse: collapse; /* Remove o espaçamento entre as células */
        }

        /* Estiliza o cabeçalho da tabela com o nosso azul padrão */
        .tabela-vendas thead th {
            background-color: #00033a; /* Nosso azul padrão */
            color: #ffffff; /* Texto branco para contraste */
            padding: 10px;
            text-align: left;
            font-size: 13px;
            border: 1px solid #00033a;
        }

        /* Estiliza as células do corpo da tabela */
        .tabela-vendas tbody td {
            padding: 8px;
            border: 1px solid #dddddd; /* Linhas cinzas claras */
        }

        /* Cria um efeito de zebrado nas linhas para melhor legibilidade */
        .tabela-vendas tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Alinhamento para colunas de valor */
        .text-right {
            text-align: right;
        }
        
        /* Estilo para a linha de totais no rodapé da tabela */
        .tabela-vendas tfoot td {
            font-weight: bold;
            font-size: 14px;
            padding: 10px;
            border-top: 2px solid #333;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="periodo">
            Período de: {{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}
        </div>
        <h1 class="titulo">Relatório de Vendas</h1>
    </div>

    <table class="tabela-vendas">
        <thead>
            <tr>
                <th>Nº Nota</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Situação</th>
                <th class="text-right">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vendas as $venda)
                <tr>
                    <td>{{ $venda->numero_nfe ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($venda->data)->format('d/m/Y') }}</td>
                    <td>{{ $venda->cliente ?? 'Consumidor Final' }}</td>
                    <td>{{ $venda->estado ?? 'Indefinido' }}</td>
                    <td class="text-right">R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Nenhuma venda encontrada para o período selecionado.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">TOTAL GERAL:</td>
                <td class="text-right">R$ {{ number_format($vendas->sum('total'), 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>