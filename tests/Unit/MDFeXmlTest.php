<?php

namespace Tests\Unit;

use App\Enums\EstadoEnum;
use App\Models\MDFeXml;
use PHPUnit\Framework\TestCase;

class MDFeXmlTest extends TestCase
{
    public function test_mapeia_modo_de_impressao_para_tipo_de_xml(): void
    {
        $this->assertSame(MDFeXml::TIPO_AUTORIZADO, MDFeXml::tipoPorModo(0));
        $this->assertSame(MDFeXml::TIPO_ENCERRADO, MDFeXml::tipoPorModo(1));
        $this->assertSame(MDFeXml::TIPO_CANCELADO, MDFeXml::tipoPorModo(2));
        $this->assertNull(MDFeXml::tipoPorModo(99));
    }

    public function test_mapeia_situacao_para_tipo_de_xml(): void
    {
        $this->assertSame(MDFeXml::TIPO_AUTORIZADO, MDFeXml::tipoPorSituacao(EstadoEnum::AUTORIZADO));
        $this->assertSame(MDFeXml::TIPO_ENCERRADO, MDFeXml::tipoPorSituacao(EstadoEnum::ENCERRADO));
        $this->assertSame(MDFeXml::TIPO_CANCELADO, MDFeXml::tipoPorSituacao('Cancelado'));
        $this->assertNull(MDFeXml::tipoPorSituacao(EstadoEnum::PENDENTE));
    }
}
