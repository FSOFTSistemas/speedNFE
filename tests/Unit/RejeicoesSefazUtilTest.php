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

    public function test_formata_erro_de_telefone_do_responsavel_tecnico(): void
    {
        $mensagem = NFeErroUtil::formatar(
            'Preenchimento Obrigatório! [fone] ZD01 infRespTec - Informar o telefone da pessoa a ser contatada na empresa desenvolvedora do sistema.'
        );

        $this->assertStringContainsString('Possível problema em: Telefone.', $mensagem);
        $this->assertStringContainsString('Telefone inválido ou ausente em Configurações > Responsável técnico.', $mensagem);
        $this->assertStringContainsString('Informe apenas números com DDD', $mensagem);
    }

    public function test_formata_erro_undefined_de_fone_do_cliente_sem_tags_tecnicas(): void
    {
        $mensagem = NFeErroUtil::formatar('Undefined property: stdClass::$fone em <enderDest>');

        $this->assertStringContainsString('Telefone inválido ou ausente em Cadastro do cliente.', $mensagem);
        $this->assertStringNotContainsString('Undefined property', $mensagem);
        $this->assertStringNotContainsString('<enderDest>', $mensagem);
    }

    public function test_rejeicao_com_cedilha_e_til_corrompidos_fica_legivel(): void
    {
        $duplamenteCodificada = NFeErroUtil::formatar('Erro na autorização: [000] - RejeiÃ§Ã£o: Endereço do destinatÃ¡rio invÃ¡lido');
        $iso88591 = NFeErroUtil::formatar("Erro na autoriza\xE7\xE3o: [000] - Rejei\xE7\xE3o: Endere\xE7o inv\xE1lido");

        foreach ([$duplamenteCodificada, $iso88591] as $mensagem) {
            $this->assertStringContainsString('Rejeição', $mensagem);
            $this->assertStringNotContainsString('Ã', $mensagem);
            $this->assertNotFalse(json_encode(['text' => $mensagem]));
        }
    }
}
