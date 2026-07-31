<?php

namespace App\Utils;

class NFeErroUtil
{
    private const CATEGORIAS = [
        'Cliente' => ['dest', 'destinatário', 'destinatario'],
        'Empresa' => ['emit', 'emitente', 'certificado'],
        'Endereço' => ['ender', 'endereço', 'endereco', 'cep', 'município', 'municipio', ' uf ', 'logradouro'],
        'Produto' => ['prod', 'produto', 'ncm', 'cfop', 'cest', 'det '],
        'Telefone' => ['fone', 'telefone', 'celular'],
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
        $mensagens = self::normalizarErros($erro);

        if (empty($mensagens)) {
            return 'Ocorreu um erro ao processar a nota. Tente novamente.';
        }

        $mensagensFormatadas = array_map(function ($mensagem) {
            return RejeicoesSefazUtil::formatarMensagemAmigavel($mensagem)
                ?? self::formatarErroGeracaoXml($mensagem);
        }, $mensagens);

        $texto = implode(PHP_EOL.PHP_EOL, $mensagensFormatadas);

        $categorias = self::identificarCategorias(implode(' ', $mensagens));
        if (! empty($categorias) && ! self::possuiRejeicaoSefaz($mensagens)) {
            $texto = 'Possível problema em: '.implode(', ', $categorias).'.'.PHP_EOL.PHP_EOL.$texto;
        }

        return $texto;
    }

    private static function normalizarErros($erro): array
    {
        $mensagens = is_array($erro) ? self::achatar($erro) : [$erro];

        return array_values(array_filter(array_map(function ($mensagem) {
            $mensagem = trim((string) $mensagem);
            $mensagem = preg_replace('/\s+/', ' ', $mensagem);

            return $mensagem;
        }, $mensagens)));
    }

    private static function achatar(array $itens): array
    {
        $resultado = [];

        foreach ($itens as $item) {
            if (is_array($item)) {
                $resultado = array_merge($resultado, self::achatar($item));

                continue;
            }

            $resultado[] = $item;
        }

        return $resultado;
    }

    private static function formatarErroGeracaoXml(string $mensagem): string
    {
        $mensagemLimpa = self::limparMensagemTecnica($mensagem);
        $textoBusca = mb_strtolower($mensagemLimpa);

        if (self::contem($textoBusca, ['fone', 'telefone', 'celular'])) {
            $origem = self::origemTelefone($mensagemLimpa);

            return '• Telefone inválido ou ausente em '.$origem
                .'. Informe apenas números com DDD, sem espaços ou símbolos. Exemplo: 87981753993.';
        }

        if (preg_match('/Preenchimento Obrigatório!\s*\[([^\]]+)\]\s*(.+)?/iu', $mensagemLimpa, $match)) {
            $campo = trim($match[1]);
            $descricao = trim($match[2] ?? '');
            $descricao = $descricao !== '' ? ' '.$descricao : '';

            return '• Campo obrigatório não preenchido: '.$campo.'.'.$descricao;
        }

        return '• '.$mensagemLimpa;
    }

    private static function limparMensagemTecnica(string $mensagem): string
    {
        $mensagem = html_entity_decode($mensagem, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $mensagem = preg_replace('/<([A-Za-z0-9_:-]+)>/', '$1', $mensagem);
        $mensagem = strip_tags($mensagem);
        $mensagem = str_replace(['stdClass::$', 'stdClass->'], '', $mensagem);
        $mensagem = preg_replace('/Undefined property:\s*/iu', 'Campo não preenchido: ', $mensagem);
        $mensagem = preg_replace('/\s+/', ' ', $mensagem);

        return trim($mensagem ?? '');
    }

    private static function origemTelefone(string $mensagem): string
    {
        $textoBusca = mb_strtolower($mensagem);

        if (self::contem($textoBusca, ['infresptec', 'responsável técnico', 'responsavel tecnico', 'zd01'])) {
            return 'Configurações > Responsável técnico';
        }

        if (self::contem($textoBusca, ['enderemit', 'emitente', 'c05'])) {
            return 'Administração > Configurações > Empresa';
        }

        if (self::contem($textoBusca, ['enderdest', 'destinatário', 'destinatario', 'cliente', 'e05'])) {
            return 'Cadastro do cliente';
        }

        if (self::contem($textoBusca, ['entrega', 'g01'])) {
            return 'Endereço de entrega';
        }

        return 'cadastro da empresa ou do cliente';
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

    private static function contem(string $texto, array $palavras): bool
    {
        foreach ($palavras as $palavra) {
            if (str_contains($texto, mb_strtolower($palavra))) {
                return true;
            }
        }

        return false;
    }
}
