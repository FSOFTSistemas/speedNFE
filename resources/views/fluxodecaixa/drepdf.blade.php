<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRE - PDF</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">DRE - Demonstrativo de Resultado</h2>
    <p>Período: {{ date('d/m/Y', strtotime($dataInicial)) }} a {{ date('d/m/Y', strtotime($dataFinal)) }}</p>

    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Receitas</strong></td>
                <td>R$ {{ number_format($receitas, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Despesas</strong></td>
                <td>R$ {{ number_format($despesas, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Lucro Líquido</strong></td>
                <td>R$ {{ number_format($lucro, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>