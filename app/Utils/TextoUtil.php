<?php

namespace App\Utils;

class TextoUtil
{
    /** Bytes 0x80-0x9F do Windows-1252 e o caractere Unicode que representam. */
    private const CP1252 = [
        0x80 => 0x20AC, 0x82 => 0x201A, 0x83 => 0x0192, 0x84 => 0x201E, 0x85 => 0x2026, 0x86 => 0x2020,
        0x87 => 0x2021, 0x88 => 0x02C6, 0x89 => 0x2030, 0x8A => 0x0160, 0x8B => 0x2039, 0x8C => 0x0152,
        0x8E => 0x017D, 0x91 => 0x2018, 0x92 => 0x2019, 0x93 => 0x201C, 0x94 => 0x201D, 0x95 => 0x2022,
        0x96 => 0x2013, 0x97 => 0x2014, 0x98 => 0x02DC, 0x99 => 0x2122, 0x9A => 0x0161, 0x9B => 0x203A,
        0x9C => 0x0153, 0x9E => 0x017E, 0x9F => 0x0178,
    ];

    /**
     * Garante texto UTF-8 legível para exibir ao usuário (mensagens da SEFAZ, exceções de bibliotecas):
     *
     * - bytes fora de UTF-8 (ISO-8859-1 / Windows-1252) são convertidos;
     * - texto duplamente codificado ("RejeiÃ§Ã£o") é revertido ("Rejeição"), inclusive em textos mistos;
     * - entidades HTML ("Rejei&ccedil;&atilde;o") são decodificadas;
     * - caracteres de controle são removidos (mantendo quebras de linha e tabulação).
     *
     * Texto inválido quebra acentos/cedilha e faz json_encode falhar, o que some com o alerta na tela.
     */
    public static function utf8Seguro($texto): string
    {
        $texto = (string) $texto;

        if ($texto === '') {
            return $texto;
        }

        if (! mb_check_encoding($texto, 'UTF-8')) {
            $convertido = @mb_convert_encoding($texto, 'UTF-8', 'Windows-1252');
            $texto = ($convertido !== false && mb_check_encoding($convertido, 'UTF-8'))
                ? $convertido
                : mb_scrub($texto, 'UTF-8');
        }

        // Até 2 passadas cobrem texto codificado duas vezes seguidas
        for ($i = 0; $i < 2; $i++) {
            $corrigido = self::reverterDuplaCodificacao($texto);
            if ($corrigido === $texto) {
                break;
            }
            $texto = $corrigido;
        }

        $texto = html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return preg_replace('/[\x{0}-\x{8}\x{B}\x{C}\x{E}-\x{1F}\x{7F}]/u', '', $texto) ?? $texto;
    }

    /**
     * Reverte sequências UTF-8 que foram lidas como Latin-1/Windows-1252 e recodificadas
     * (ex.: "ç" = C3 A7 virou "Ã§"). Só substitui quando os bytes reconstruídos formam um
     * caractere UTF-8 válido, para não alterar texto que já está correto.
     */
    private static function reverterDuplaCodificacao(string $texto): string
    {
        static $continuacao = null;

        if ($continuacao === null) {
            $extras = implode('', array_map(fn ($cp) => sprintf('\x{%X}', $cp), self::CP1252));
            $continuacao = '[\x{80}-\x{BF}'.$extras.']';
        }

        $c = $continuacao;
        $padrao = '/[\x{C2}-\x{DF}]'.$c.'|[\x{E0}-\x{EF}]'.$c.'{2}|[\x{F0}-\x{F4}]'.$c.'{3}/u';

        return preg_replace_callback($padrao, function ($match) {
            $bytes = '';

            foreach (mb_str_split($match[0], 1, 'UTF-8') as $caractere) {
                $codigo = mb_ord($caractere, 'UTF-8');
                $byte = $codigo <= 0xFF ? $codigo : array_search($codigo, self::CP1252, true);

                if ($byte === false) {
                    return $match[0];
                }

                $bytes .= chr($byte);
            }

            $esperado = ord($bytes[0]) >= 0xF0 ? 4 : (ord($bytes[0]) >= 0xE0 ? 3 : 2);

            if (strlen($bytes) !== $esperado || ! mb_check_encoding($bytes, 'UTF-8')) {
                return $match[0];
            }

            return $bytes;
        }, $texto) ?? $texto;
    }
}
