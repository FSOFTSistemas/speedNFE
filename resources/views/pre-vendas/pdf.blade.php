<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Pré-venda {{ $preVenda->numero }}</title>
    <style>
        body { color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .header, .totals { margin-bottom: 20px; width: 100%; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid #d1d5db; padding: 8px 6px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .totals { margin-left: auto; margin-top: 18px; width: 280px; }
        .totals td { border: 0; padding: 3px 6px; }
        .total { font-size: 15px; font-weight: bold; }
        .observacoes { background: #f9fafb; margin-top: 20px; padding: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pré-venda nº {{ $preVenda->numero }}</h1>
        <div>{{ $preVenda->empresa->fantasia ?? $preVenda->empresa->razao }}</div>
        <div class="muted">
            Data: {{ $preVenda->data->format('d/m/Y') }}
            @if ($preVenda->validade_at)
                · Validade: {{ $preVenda->validade_at->format('d/m/Y') }}
            @endif
        </div>
        <div>Cliente: {{ $preVenda->cliente->nome ?? $preVenda->cliente_nome ?? 'Consumidor não identificado' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qtd.</th>
                <th class="right">Unitário</th>
                <th class="right">Desconto</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($preVenda->itens as $item)
                <tr>
                    <td>{{ $item->descricao }}</td>
                    <td class="right">{{ number_format($item->quantidade, 4, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($item->desconto, 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="right">R$ {{ number_format($preVenda->subtotal, 2, ',', '.') }}</td></tr>
        <tr><td>Desconto</td><td class="right">R$ {{ number_format($preVenda->desconto, 2, ',', '.') }}</td></tr>
        <tr><td>Acréscimo</td><td class="right">R$ {{ number_format($preVenda->acrescimo, 2, ',', '.') }}</td></tr>
        <tr class="total"><td>Total</td><td class="right">R$ {{ number_format($preVenda->total, 2, ',', '.') }}</td></tr>
    </table>

    @if ($preVenda->observacoes)
        <div class="observacoes">
            <strong>Observações</strong><br>
            {{ $preVenda->observacoes }}
        </div>
    @endif
</body>
</html>
