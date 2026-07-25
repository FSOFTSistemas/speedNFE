<?php

namespace Tests\Unit;

use App\Utils\NFeErroUtil;
use App\Utils\RejeicoesSefazUtil;
use Tests\TestCase;

class RejeicoesSefazUtilTest extends TestCase
{
    public function test_busca_rejeicao_por_codigo(): void
    {
        $rejeicao = RejeicoesSefazUtil::porCodigo('778');

        $this->assertNotNull($rejeicao);
        $this->assertSame('778', $rejeicao['codigo']);
        $this->assertStringContainsString('NCM', $rejeicao['mensagem']);
    }

    public function test_formata_erro_sefaz_com_mensagem_amigavel_e_sugestao(): void
    {
        $mensagem = NFeErroUtil::formatar('Erro na autorização: [778] - Informado NCM inexistente[nItem:1]');

        $this->assertStringContainsString('Código 778', $mensagem);
        $this->assertStringContainsString('O que aconteceu', $mensagem);
        $this->assertStringContainsString('Como corrigir', $mensagem);
        $this->assertStringContainsString('Abra os produtos da nota', $mensagem);
    }

    public function test_mantem_dica_generica_para_erro_sem_codigo_sefaz(): void
    {
        $mensagem = NFeErroUtil::formatar('Campo prod NCM não informado');

        $this->assertStringContainsString('Possível problema em: Produto.', $mensagem);
        $this->assertStringContainsString('Campo prod NCM não informado', $mensagem);
    }
}
