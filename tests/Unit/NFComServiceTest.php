<?php

namespace Tests\Unit;

use App\Services\NFComService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NFComServiceTest extends TestCase
{
    /**
     * Monta um NFComService sem certificado digital (o construtor exige um .pfx
     * real, indisponível neste ambiente). gerarXml() não depende de $this->tools,
     * então isso é suficiente para validar a montagem do XML.
     */
    private function makeServiceWithoutCertificate(): NFComService
    {
        $reflection = new ReflectionClass(NFComService::class);

        return $reflection->newInstanceWithoutConstructor();
    }

    private function endereco(): \stdClass
    {
        $endereco = new \stdClass;
        $endereco->rua = 'Rua Teste';
        $endereco->numero = '100';
        $endereco->bairro = 'Centro';
        $endereco->codigoIBGE = '2611606';
        $endereco->cidade = 'Recife';
        $endereco->cep = '50000000';
        $endereco->uf = 'PE';

        return $endereco;
    }

    private function emitente(): \stdClass
    {
        $emitente = new \stdClass;
        $emitente->ambiente = 2;
        $emitente->crt = 3;
        $emitente->cpf_cnpj = '12345678000199';
        $emitente->rg_ie = '123456789';
        $emitente->razao = 'Empresa Teste Telecom';
        $emitente->fantasia = 'Teste Telecom';
        $emitente->celular = '87999999999';
        $emitente->endereco = $this->endereco();

        return $emitente;
    }

    private function item(): \stdClass
    {
        $item = new \stdClass;
        $item->cProd = 'SERV001';
        $item->xProd = 'Servico de internet banda larga';
        $item->cClass = '101010100';
        $item->cfop = '5307';
        $item->uMed = 'UN';
        $item->qFaturada = 1;
        $item->vItem = 100.00;
        $item->vDesc = 0;
        $item->vOutro = 0;
        $item->vProd = 100.00;
        $item->icms_cst = '00';
        $item->icms_vBC = 100.00;
        $item->icms_pICMS = 25;
        $item->icms_vICMS = 25.00;
        $item->icms_pFCP = 0;
        $item->icms_vFCP = 0;
        $item->pis_cst = '01';
        $item->pis_vBC = 100.00;
        $item->pis_pPIS = 0.65;
        $item->pis_vPIS = 0.65;
        $item->cofins_cst = '01';
        $item->cofins_vBC = 100.00;
        $item->cofins_pCOFINS = 3;
        $item->cofins_vCOFINS = 3.00;
        $item->fust_vBC = null;
        $item->fust_pFUST = null;
        $item->fust_vFUST = null;
        $item->funttel_vBC = null;
        $item->funttel_pFUNTTEL = null;
        $item->funttel_vFUNTTEL = null;

        return $item;
    }

    private function nfcom(): \stdClass
    {
        $cliente = new \stdClass;
        $cliente->nome = 'Cliente Assinante Teste';
        $cliente->cpf_cnpj = '11122233344';
        $cliente->contribuinte = 0;
        $cliente->rg_ie = null;
        $cliente->endereco = $this->endereco();

        $nfcom = new \stdClass;
        $nfcom->serie = 1;
        $nfcom->nro = 1;
        $nfcom->iCodAssinante = 'ASS0001';
        $nfcom->tpAssinante = 1;
        $nfcom->tpServUtil = 1;
        $nfcom->nContrato = 'CT-0001';
        $nfcom->competFat = '202507';
        $nfcom->dVencFat = Carbon::parse('2025-08-10');
        $nfcom->dPerUsoIni = Carbon::parse('2025-07-01');
        $nfcom->dPerUsoFim = Carbon::parse('2025-07-31');
        $nfcom->codBarras = null;
        $nfcom->vNF = 100.00;
        $nfcom->cliente = $cliente;
        $nfcom->itens = [$this->item()];

        return $nfcom;
    }

    public function test_gerar_xml_monta_documento_valido_sem_erros(): void
    {
        $service = $this->makeServiceWithoutCertificate();

        $resultado = $service->gerarXml($this->nfcom(), $this->emitente());

        $this->assertArrayNotHasKey('erros_xml', $resultado, implode(
            "\n",
            $resultado['erros_xml'] ?? []
        ));
        $this->assertArrayHasKey('xml', $resultado);
        $this->assertArrayHasKey('chave', $resultado);
        $this->assertSame(44, strlen($resultado['chave']));
        $this->assertStringContainsString('<NFCom', $resultado['xml']);
        $this->assertStringContainsString('<infNFCom', $resultado['xml']);
    }
}
