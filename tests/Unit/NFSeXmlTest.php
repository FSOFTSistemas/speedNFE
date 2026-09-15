<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Endereco;
use App\Models\NFSe;
use App\Models\Servico;
use App\Services\NFSe\CancelamentoXmlBuilder;
use App\Services\NFSe\DpsXmlBuilder;
use App\Services\NFSe\NFSeSchemaValidator;
use DOMDocument;
use DOMXPath;
use RuntimeException;
use Tests\TestCase;

class NFSeXmlTest extends TestCase
{
    private const NS = 'http://www.sped.fazenda.gov.br/nfse';

    public function test_gera_dps_com_grupo_ibscbs_compativel_com_xsd_oficial(): void
    {
        $nfse = $this->nfse();
        $xml = app(DpsXmlBuilder::class)->build($nfse);

        app(NFSeSchemaValidator::class)->validateDps($xml);

        $xpath = $this->xpath($xml);
        $this->assertSame('0', $xpath->evaluate('string(/n:DPS/n:infDPS/n:IBSCBS/n:finNFSe)'));
        $this->assertSame('100101', $xpath->evaluate('string(/n:DPS/n:infDPS/n:IBSCBS/n:cIndOp)'));
        $this->assertSame('0', $xpath->evaluate('string(/n:DPS/n:infDPS/n:IBSCBS/n:indDest)'));
        $this->assertSame('000', $xpath->evaluate('string(/n:DPS/n:infDPS/n:IBSCBS/n:valores/n:trib/n:gIBSCBS/n:CST)'));
        $this->assertSame('000001', $xpath->evaluate('string(/n:DPS/n:infDPS/n:IBSCBS/n:valores/n:trib/n:gIBSCBS/n:cClassTrib)'));
        $this->assertSame(0, $xpath->query('//n:IBSCBS//n:vIBS')->length);
        $this->assertSame(0, $xpath->query('//n:IBSCBS//n:vCBS')->length);
    }

    public function test_dps_exige_classificacao_ibscbs(): void
    {
        $nfse = $this->nfse();
        $nfse->cst_ibs_cbs = null;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('CST do IBS/CBS não informado');

        app(DpsXmlBuilder::class)->build($nfse);
    }

    public function test_gera_pedido_de_cancelamento_compativel_com_xsd_oficial(): void
    {
        $nfse = $this->nfse();
        $nfse->chave = '26116061'.'1'.'12345678000199'.str_repeat('1', 27);

        $xml = app(CancelamentoXmlBuilder::class)->build(
            $nfse,
            '1',
            'Erro identificado durante a emissão da nota.'
        );

        app(NFSeSchemaValidator::class)->validateEvent($xml);

        $xpath = $this->xpath($xml);
        $this->assertSame('PRE'.$nfse->chave.'101101', $xpath->evaluate('string(/n:pedRegEvento/n:infPedReg/@Id)'));
        $this->assertSame('12345678000199', $xpath->evaluate('string(/n:pedRegEvento/n:infPedReg/n:CNPJAutor)'));
        $this->assertSame('1', $xpath->evaluate('string(/n:pedRegEvento/n:infPedReg/n:e101101/n:cMotivo)'));
        $this->assertSame('Cancelamento de NFS-e', $xpath->evaluate('string(/n:pedRegEvento/n:infPedReg/n:e101101/n:xDesc)'));
    }

    public function test_cancelamento_rejeita_justificativa_curta(): void
    {
        $nfse = $this->nfse();
        $nfse->chave = '26116061'.'1'.'12345678000199'.str_repeat('1', 27);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('entre 15 e 255');

        app(CancelamentoXmlBuilder::class)->build($nfse, '9', 'Muito curta');
    }

    private function nfse(): NFSe
    {
        $endereco = new Endereco([
            'rua' => 'Rua do Teste',
            'bairro' => 'Centro',
            'numero' => '100',
            'cidade' => 'Recife',
            'uf' => 'PE',
            'codigoIBGE' => '2611606',
            'cep' => '50000000',
        ]);

        $empresa = new Empresa;
        $empresa->forceFill([
            'id' => 2,
            'razao' => 'Empresa de Teste',
            'cpf_cnpj' => '12345678000199',
            'inscricao_municipal' => '1234567',
            'celular' => '81999999999',
            'crt' => '3',
            'ambiente' => 2,
        ]);
        $empresa->setRelation('endereco', $endereco);

        $cliente = new Cliente;
        $cliente->forceFill([
            'id' => 3,
            'nome' => 'Cliente de Teste',
            'cpf_cnpj' => '98765432000188',
            'celular' => '81988888888',
        ]);
        $cliente->setRelation('endereco', $endereco);

        $servico = new Servico([
            'codigo' => 'SERV1',
            'tribISSQN' => '1',
            'tpRetISSQN' => '1',
        ]);

        $nfse = new NFSe;
        $nfse->forceFill([
            'id' => 10,
            'serieDPS' => '1',
            'nDPS' => '15',
            'tpAmb' => 2,
            'tpEmit' => 1,
            'data_competencia' => '2026-08-27',
            'data_emissao' => '2026-08-27 10:00:00',
            'cLocEmi' => '2611606',
            'cLocPrestacao' => '2611606',
            'cTribNac' => '010101',
            'cNBS' => '123456789',
            'cIndOp' => '100101',
            'cClassTrib' => '000001',
            'cst_ibs_cbs' => '000',
            'finNFSe' => '0',
            'indFinal' => '0',
            'indDest' => '0',
            'vServ' => 100,
            'vBC' => 100,
            'pAliq' => 5,
            'discriminacao' => 'Serviço prestado para teste de integração nacional.',
        ]);
        $nfse->setRelation('empresa', $empresa);
        $nfse->setRelation('cliente', $cliente);
        $nfse->setRelation('servico', $servico);

        return $nfse;
    }

    private function xpath(string $xml): DOMXPath
    {
        $dom = new DOMDocument;
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('n', self::NS);

        return $xpath;
    }
}
