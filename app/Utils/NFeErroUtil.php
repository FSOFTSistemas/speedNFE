<?php

namespace App\Utils;

class NFeErroUtil
{
    private const CATEGORIAS = [
        'Cliente' => ['dest', 'destinatário', 'destinatario'],
        'Empresa' => ['emit', 'emitente', 'certificado'],
        'Endereço' => ['ender', 'endereço', 'endereco', 'cep', 'município', 'municipio', ' uf ', 'logradouro'],
        'Produto' => ['prod', 'produto', 'ncm', 'cfop', 'cest', 'det '],
    ];

    /**
     * Formata os erros vindos do gerador de XML (sped-nfe) ou da rejeição da
     * SEFAZ em uma mensagem legível, com uma dica de qual parte da venda
     * (cliente, empresa, endereço ou produto) provavelmente está envolvida.
     *
     * @param  string|array  $erro
     */
    public static function formatar($erro): string
    {
        $mensagens = is_array($erro)
            ? array_values(array_filter(array_map('strval', $erro)))
            : array_values(array_filter([trim((string) $erro)]));

        if (empty($mensagens)) {
            return 'Ocorreu um erro ao processar a nota. Tente novamente.';
        }

        $mensagensFormatadas = array_map(function ($mensagem) {
            return RejeicoesSefazUtil::formatarMensagemAmigavel($mensagem) ?? '• '.$mensagem;
        }, $mensagens);

        $texto = implode(PHP_EOL.PHP_EOL, $mensagensFormatadas);

        $categorias = self::identificarCategorias(implode(' ', $mensagens));
        if (! empty($categorias) && ! self::possuiRejeicaoSefaz($mensagens)) {
            $texto = 'Possível problema em: '.implode(', ', $categorias).'.'.PHP_EOL.PHP_EOL.$texto;
        }

        return $texto;
    }

    private static function possuiRejeicaoSefaz(array $mensagens): bool
    {
        foreach ($mensagens as $mensagem) {
            if (RejeicoesSefazUtil::porCodigo(RejeicoesSefazUtil::extrairCodigo($mensagem)) !== null) {
                return true;
            }
        }

        return false;
    }

    private static function identificarCategorias(string $texto): array
    {
        $textoBusca = ' '.mb_strtolower($texto).' ';
        $encontradas = [];

        foreach (self::CATEGORIAS as $categoria => $palavrasChave) {
            foreach ($palavrasChave as $palavra) {
                if (str_contains($textoBusca, mb_strtolower($palavra))) {
                    $encontradas[] = $categoria;
                    break;
                }
            }
        }

        return $encontradas;
    }
}
