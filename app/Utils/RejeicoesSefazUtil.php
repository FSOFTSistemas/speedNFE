<?php

namespace App\Utils;

use Illuminate\Support\Str;

class RejeicoesSefazUtil
{
    public static function todas(): array
    {
        return config('rejeicoes_sefaz', []);
    }

    public static function porCodigo(?string $codigo): ?array
    {
        if (empty($codigo)) {
            return null;
        }

        foreach (self::todas() as $rejeicao) {
            if (($rejeicao['codigo'] ?? null) === (string) $codigo) {
                return $rejeicao;
            }
        }

        return null;
    }

    public static function extrairCodigo(string $texto): ?string
    {
        if (preg_match('/\[(\d{3})\]/', $texto, $match)) {
            return $match[1];
        }

        if (preg_match('/\b(?:cstat|código|codigo|rejeição|rejeicao)\D{0,20}(\d{3})\b/iu', $texto, $match)) {
            return $match[1];
        }

        return null;
    }

    public static function artigoAjuda(array $item): array
    {
        $mensagem = $item['mensagem'];
        $detalhes = array_values(array_filter([
            ! empty($item['modelo']) ? '<b>Modelo:</b> '.$item['modelo'] : null,
            ! empty($item['regra']) ? '<b>Regra:</b> '.$item['regra'] : null,
            ! empty($item['grupo_xml']) ? '<b>Grupo XML:</b> '.$item['grupo_xml'] : null,
            ! empty($item['campo_xml']) ? '<b>Campo XML:</b> '.$item['campo_xml'] : null,
            ! empty($item['observacoes']) ? '<b>Observações:</b> '.$item['observacoes'] : null,
        ]));

        $blocos = [
            ['heading' => 'O que significa', 'paragrafo' => 'A SEFAZ validou o XML da NF-e/NFC-e e retornou o código '.$item['codigo'].' para indicar: <b>'.$mensagem.'</b>.'],
            ['heading' => 'Causa provável', 'paragrafo' => 'Algum dado cadastral, fiscal, tributário ou estrutural da nota não atende à regra de validação informada para essa rejeição.'],
            ['heading' => 'Como resolver', 'lista' => self::sugestoesCorrecao($mensagem)],
        ];

        if ($detalhes !== []) {
            $blocos[] = ['heading' => 'Detalhes técnicos', 'lista' => $detalhes];
        }

        return [
            'slug' => $item['slug'] ?? 'rejeicao-'.$item['codigo'],
            'categoria' => 'rejeicoes-sefaz',
            'titulo' => 'Rejeição '.$item['codigo'].': '.$mensagem,
            'resumo' => 'A SEFAZ rejeitou a NF-e/NFC-e porque: '.$mensagem.'.',
            'palavras_chave' => 'codigo '.$item['codigo'].' cstat rejeicao sefaz nfe nfce '.mb_strtolower($mensagem),
            'blocos' => $blocos,
            'codigo' => $item['codigo'],
        ];
    }

    public static function formatarMensagemAmigavel(string $erro): ?string
    {
        $codigo = self::extrairCodigo($erro);
        $rejeicao = self::porCodigo($codigo);

        if ($codigo === null || $rejeicao === null) {
            return null;
        }

        $mensagem = $rejeicao['mensagem'];
        $retorno = self::limparRetornoSefaz($erro);
        $sugestoes = implode(PHP_EOL, array_map(fn ($item) => '• '.$item, self::sugestoesCorrecao($mensagem.' '.$retorno)));

        $texto = 'Código '.$codigo.': '.$mensagem.PHP_EOL.PHP_EOL;
        $texto .= 'O que aconteceu: a SEFAZ recusou a NF-e/NFC-e porque '.$mensagem.'.'.PHP_EOL.PHP_EOL;

        if ($retorno !== '' && ! str_contains(Str::ascii(mb_strtolower($retorno)), Str::ascii(mb_strtolower($mensagem)))) {
            $texto .= 'Retorno recebido: '.$retorno.PHP_EOL.PHP_EOL;
        }

        $texto .= 'Como corrigir:'.PHP_EOL.$sugestoes;

        return $texto;
    }

    public static function sugestoesCorrecao(string $texto): array
    {
        $textoBusca = Str::ascii(mb_strtolower($texto));

        if (self::contem($textoBusca, ['duplicidade', 'ja transmitida', 'ja autorizado', 'ja existente'])) {
            return [
                'Verifique se a nota já foi autorizada ou está aguardando processamento antes de reenviar.',
                'Não altere manualmente número, série, data ou chave de acesso de uma nota já transmitida.',
                'Se for uma venda nova, gere a nota com uma numeração ainda não utilizada.',
            ];
        }

        if (self::contem($textoBusca, ['ncm', 'cfop', 'cest', 'cst', 'csosn', 'gtin', 'cean', 'produto', 'item'])) {
            return [
                'Abra os produtos da nota e revise NCM, CFOP, CEST, CST/CSOSN, GTIN e demais dados fiscais citados na rejeição.',
                'Corrija o cadastro do produto ou a tributação aplicada à operação.',
                'Salve a venda e transmita a NF-e/NFC-e novamente.',
            ];
        }

        if (self::contem($textoBusca, ['destinatario', 'cliente', 'cpf do destinatario', 'cnpj do destinatario', 'ie do destinatario', 'endereco do destinatario'])) {
            return [
                'Abra o cadastro do cliente e revise CPF/CNPJ, Inscrição Estadual, indicador de contribuinte e endereço.',
                'Confirme se o cliente está cadastrado corretamente na UF de destino.',
                'Salve o cadastro, atualize a venda e transmita a nota novamente.',
            ];
        }

        if (self::contem($textoBusca, ['emitente', 'certificado', 'crt', 'regime tributario', 'cnpj do emitente', 'ie do emitente'])) {
            return [
                'Revise o cadastro da empresa emitente em Administração > Configurações.',
                'Confira CNPJ, Inscrição Estadual, regime tributário, UF e certificado digital.',
                'Depois de corrigir os dados da empresa, gere e transmita a nota novamente.',
            ];
        }

        if (self::contem($textoBusca, ['pagamento', 'troco', 'fatura', 'duplicata', 'cartao', 'parcela'])) {
            return [
                'Revise as formas de pagamento, parcelas, troco e dados de cartão informados na venda.',
                'Confirme se a soma dos pagamentos fecha com o total da nota.',
                'Salve a correção e transmita novamente.',
            ];
        }

        if (self::contem($textoBusca, ['total', 'valor', 'icms', 'fcp', 'pis', 'cofins', 'ipi', 'desconto', 'frete', 'seguro'])) {
            return [
                'Revise totais, descontos, frete, seguro e valores de impostos calculados nos itens e no total da nota.',
                'Corrija o item ou a regra tributária que causou diferença de valores.',
                'Recalcule/salve a venda e transmita novamente.',
            ];
        }

        if (self::contem($textoBusca, ['qr-code', 'qrcode', 'csc'])) {
            return [
                'Revise a configuração de NFC-e, principalmente CSC, identificador do CSC e URLs da SEFAZ.',
                'Confirme se os dados correspondem ao ambiente correto: homologação ou produção.',
                'Salve a configuração e transmita novamente.',
            ];
        }

        if (self::contem($textoBusca, ['schema', 'xml', 'mal formado', 'namespace'])) {
            return [
                'Revise os dados obrigatórios da empresa, cliente, produtos, pagamentos e totais da nota.',
                'Tente gerar a NF-e/NFC-e novamente após corrigir cadastros incompletos ou inválidos.',
                'Se persistir, acione o suporte informando o número da venda e o retorno da SEFAZ.',
            ];
        }

        if (self::contem($textoBusca, ['contingencia', 'epec', 'svc'])) {
            return [
                'Confirme se a emissão em contingência está realmente necessária para a UF e o ambiente atual.',
                'Aguarde a normalização da SEFAZ quando o bloqueio for temporário.',
                'Depois ajuste o tipo de emissão e transmita novamente.',
            ];
        }

        return [
            'Revise os dados da empresa, cliente, produtos, tributação, totais e forma de pagamento relacionados à mensagem da rejeição.',
            'Corrija o cadastro ou os dados da nota que deram origem ao XML rejeitado.',
            'Emita ou transmita a nota novamente após salvar a correção.',
            'Se a mensagem envolver regra fiscal específica, confirme o enquadramento com a contabilidade ou com a SEFAZ da UF.',
        ];
    }

    private static function limparRetornoSefaz(string $erro): string
    {
        $texto = trim($erro);
        $texto = preg_replace('/^Erro (na autorização|no processamento do lote):\s*/iu', '', $texto);
        $texto = preg_replace('/^\[\d{3}\]\s*-\s*/', '', $texto);

        return trim($texto ?? '');
    }

    private static function contem(string $texto, array $palavras): bool
    {
        foreach ($palavras as $palavra) {
            if (str_contains($texto, Str::ascii(mb_strtolower($palavra)))) {
                return true;
            }
        }

        return false;
    }
}
