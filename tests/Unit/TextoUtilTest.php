<?php

namespace Tests\Unit;

use App\Utils\TextoUtil;
use PHPUnit\Framework\TestCase;

class TextoUtilTest extends TestCase
{
    public function test_mantem_texto_utf8_correto()
    {
        $texto = 'Rejeição já correta: município de São Paulo, Nº 1ª, AÇÃO';

        $this->assertSame($texto, TextoUtil::utf8Seguro($texto));
    }

    public function test_converte_bytes_iso_8859_1()
    {
        $this->assertSame('Rejeição: Duplicidade', TextoUtil::utf8Seguro("Rejei\xE7\xE3o: Duplicidade"));
    }

    public function test_corrige_texto_misto_utf8_e_iso_8859_1()
    {
        $this->assertSame(
            'Rejeição: cabeçalho inválido',
            TextoUtil::utf8Seguro("Rejei\xE7\xE3o: cabe\xC3\xA7alho inv\xE1lido")
        );
    }

    public function test_reverte_dupla_codificacao()
    {
        $this->assertSame('Rejeição: Duplicidade de NF-e', TextoUtil::utf8Seguro('RejeiÃ§Ã£o: Duplicidade de NF-e'));
        $this->assertSame('ÇÃO', TextoUtil::utf8Seguro('Ã‡ÃƒO'));
    }

    public function test_reverte_codificacao_tripla()
    {
        $this->assertSame('Rejeição', TextoUtil::utf8Seguro('RejeiÃƒÂ§ÃƒÂ£o'));
    }

    public function test_decodifica_entidades_html()
    {
        $this->assertSame('Rejeição', TextoUtil::utf8Seguro('Rejei&ccedil;&atilde;o'));
        $this->assertSame('Rejeição', TextoUtil::utf8Seguro('Rejei&#231;&#227;o'));
    }

    public function test_resultado_sempre_serializa_em_json()
    {
        $texto = TextoUtil::utf8Seguro("Rejei\xE7\xE3o \x01 inv\xE1lida");

        $this->assertNotFalse(json_encode(['text' => $texto]));
        $this->assertSame('Rejeição  inválida', $texto);
    }
}
