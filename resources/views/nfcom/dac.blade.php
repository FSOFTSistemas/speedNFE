<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>NFCom {{ $nfcom->nro }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
        table { width: 100%; border-collapse: collapse; }
        .box { border: 1px solid #333; padding: 6px; margin-bottom: 8px; }
        .title { font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 4px; }
        .subtitle { font-size: 10px; text-align: center; color: #555; margin-bottom: 10px; }
        .label { font-size: 8px; color: #555; text-transform: uppercase; }
        .value { font-size: 11px; font-weight: bold; }
        .chave { font-family: monospace; font-size: 11px; word-break: break-all; text-align: center; }
        th, td { border: 1px solid #999; padding: 4px; font-size: 9px; text-align: left; }
        th { background-color: #eee; text-transform: uppercase; }
        .text-right { text-align: right; }
        .totals td { font-weight: bold; }
    </style>
</head>
<body>
    <div class="title">Documento Auxiliar da Nota Fiscal de Comunicação (DAC)</div>
    <div class="subtitle">NFCom nº {{ $nfcom->nro }} - Série {{ $nfcom->serie }} - Situação: {{ $nfcom->situacao->value }}</div>

    <div class="box">
        <span class="label">Emitente</span><br>
        <span class="value">{{ $nfcom->empresa->razao }}</span>
        - CNPJ/CPF: {{ $nfcom->empresa->cpf_cnpj }}
        - IE: {{ $nfcom->empresa->rg_ie }}<br>
        {{ $nfcom->empresa->endereco->rua ?? '' }}, {{ $nfcom->empresa->endereco->numero ?? '' }} -
        {{ $nfcom->empresa->endereco->bairro ?? '' }} - {{ $nfcom->empresa->endereco->cidade ?? '' }}/{{ $nfcom->empresa->endereco->uf ?? '' }}
    </div>

    <div class="box chave">
        Chave de Acesso: {{ $nfcom->chave ?? 'NÃO AUTORIZADA' }}
        @if ($nfcom->nProtocolo)
            <br>Protocolo de Autorização: {{ $nfcom->nProtocolo }}
        @endif
    </div>

    <div class="box">
        <span class="label">Assinante</span><br>
        <span class="value">{{ $nfcom->cliente->nome }}</span>
        - CPF/CNPJ: {{ $nfcom->cliente->cpf_cnpj }}<br>
        {{ $nfcom->cliente->endereco->rua ?? '' }}, {{ $nfcom->cliente->endereco->numero ?? '' }} -
        {{ $nfcom->cliente->endereco->bairro ?? '' }} - {{ $nfcom->cliente->endereco->cidade ?? '' }}/{{ $nfcom->cliente->endereco->uf ?? '' }}<br>
        Código do Assinante: {{ $nfcom->iCodAssinante }} | Contrato: {{ $nfcom->nContrato ?? '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descrição</th>
                <th>Un.</th>
                <th class="text-right">Qtde.</th>
                <th class="text-right">Vlr. Unit.</th>
                <th class="text-right">Desconto</th>
                <th class="text-right">Vlr. ICMS</th>
                <th class="text-right">Vlr. Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($nfcom->itens as $item)
                <tr>
                    <td>{{ $item->cProd }}</td>
                    <td>{{ $item->xProd }}</td>
                    <td>{{ $item->uMed }}</td>
                    <td class="text-right">{{ number_format($item->qFaturada, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->vItem, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->vDesc, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->icms_vICMS, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->vProd, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="7" class="text-right">Total dos Serviços</td>
                <td class="text-right">R$ {{ number_format($nfcom->vProd, 2, ',', '.') }}</td>
            </tr>
            <tr class="totals">
                <td colspan="7" class="text-right">Desconto</td>
                <td class="text-right">R$ {{ number_format($nfcom->vDesc, 2, ',', '.') }}</td>
            </tr>
            <tr class="totals">
                <td colspan="7" class="text-right">Valor Total da NFCom</td>
                <td class="text-right">R$ {{ number_format($nfcom->vNF, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="box" style="margin-top: 8px;">
        <span class="label">Faturamento</span><br>
        Competência: {{ $nfcom->competFat }} |
        Vencimento: {{ optional($nfcom->dVencFat)->format('d/m/Y') }} |
        Período de uso: {{ optional($nfcom->dPerUsoIni)->format('d/m/Y') }} a {{ optional($nfcom->dPerUsoFim)->format('d/m/Y') }}
        @if ($nfcom->codBarras)
            <br>Linha digitável: {{ $nfcom->codBarras }}
        @endif
    </div>
</body>
</html>
