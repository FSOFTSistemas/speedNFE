<?php

namespace Tests\Unit;

use App\Services\CTeService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CTeServiceTest extends TestCase
{
    /**
     * Monta um CTeService sem certificado digital (o construtor exige um .pfx
     * real, indisponível neste ambiente). gerarXml() não depende de $this->tools,
     * então isso é suficiente para validar a montagem do XML.
     */
    private function makeServiceWithoutCertificate(): CTeService
    {
        $reflection = new ReflectionClass(CTeService::class);

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
        $emitente->razao = 'Transportadora Teste';
        $emitente->fantasia = 'Teste Transportes';
        $emitente->celular = '87999999999';
        $emitente->endereco = $this->endereco();

        return $emitente;
    }

    private function participante(string $nome): \stdClass
    {
        $participante = new \stdClass;
        $participante->nome = $nome;
        $participante->cpf_cnpj = '98765432000188';
        $participante->rg_ie = '987654321';
        $participante->endereco = $this->endereco();

        return $participante;
    }

    private function documento(): \stdClass
    {
        $documento = new \stdClass;
        $documento->chave = str_repeat('1', 44);

        return $documento;
    }

    private function cte(): \stdClass
    {
        $veiculo = new \stdClass;
        $veiculo->proprietario = new \stdClass;
        $veiculo->proprietario->rntrc = '12345678';

        $cte = new \stdClass;
        $cte->numero = 1;
        $cte->serie = 1;
        $cte->toma = 0;
        $cte->cfop = '5353';
        $cte->uf_inicio = 'PE';
        $cte->mun_ini_codigo = '2611606';
        $cte->mun_ini_nome = 'Recife';
        $cte->uf_fim = 'PE';
        $cte->mun_fim_codigo = '2607901';
        $cte->mun_fim_nome = 'Jaboatao dos Guararapes';
        $cte->xProd = 'Mercadorias em geral';
        $cte->qCarga = 100;
        $cte->vCarga = 1000;
        $cte->vTPrest = 200;
        $cte->vRec = 200;
        $cte->picms = 12;
        $cte->remetente = $this->participante('Remetente Teste');
        $cte->destinatario = $this->participante('Destinatario Teste');
        $cte->veiculo = $veiculo;
        $cte->documentos = [$this->documento()];

        return $cte;
    }

    public function test_gerar_xml_monta_documento_valido_sem_erros(): void
    {
        $service = $this->makeServiceWithoutCertificate();

        $resultado = $service->gerarXml($this->cte(), $this->emitente());

        $this->assertArrayNotHasKey('erros_xml', $resultado, implode(
            "\n",
            $resultado['erros_xml'] ?? []
        ));
        $this->assertArrayHasKey('xml', $resultado);
        $this->assertArrayHasKey('chave', $resultado);
        $this->assertSame(44, strlen($resultado['chave']));
        $this->assertStringContainsString('<CTe', $resultado['xml']);
        $this->assertStringContainsString('<infCte', $resultado['xml']);
    }
}
