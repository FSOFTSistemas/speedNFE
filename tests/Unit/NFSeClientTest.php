<?php

namespace Tests\Unit;

use App\Models\Empresa;
use App\Services\NFSe\NFSeClient;
use App\Services\NFSe\NFSeSigner;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\TestCase;

class NFSeClientTest extends TestCase
{
    public function test_emissao_preserva_caminho_base_e_compacta_dps(): void
    {
        $history = [];
        $responseXml = '<NFSe xmlns="http://www.sped.fazenda.gov.br/nfse"><infNFSe><chNFSe>123</chNFSe></infNFSe></NFSe>';
        $http = $this->http([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'chaveAcesso' => '123',
                'nfseXmlGZipB64' => base64_encode(gzencode($responseXml)),
            ])),
        ], $history);

        $client = new TestableNFSeClient($this->signer(), $http);
        $result = $client->emitir('<DPS>teste</DPS>', $this->empresa());

        $this->assertTrue($result['ok']);
        $this->assertSame($responseXml, $result['body']['nfseXml']);
        $this->assertSame('/API/SefinNacional/nfse', $history[0]['request']->getUri()->getPath());

        $payload = json_decode((string) $history[0]['request']->getBody(), true);
        $this->assertSame('<DPS>teste</DPS>', gzdecode(base64_decode($payload['dpsXmlGZipB64'])));
    }

    public function test_cancelamento_usa_endpoint_e_payload_oficiais(): void
    {
        $history = [];
        $http = $this->http([
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ], $history);
        $client = new TestableNFSeClient($this->signer(), $http);
        $chave = str_repeat('1', 50);

        $result = $client->registrarEvento($chave, '<pedRegEvento>teste</pedRegEvento>', $this->empresa());

        $this->assertTrue($result['ok']);
        $this->assertSame('/API/SefinNacional/nfse/'.$chave.'/eventos', $history[0]['request']->getUri()->getPath());
        $payload = json_decode((string) $history[0]['request']->getBody(), true);
        $this->assertSame(
            '<pedRegEvento>teste</pedRegEvento>',
            gzdecode(base64_decode($payload['pedidoRegistroEventoXmlGZipB64']))
        );
    }

    private function signer(): NFSeSigner
    {
        $signer = Mockery::mock(NFSeSigner::class);
        $signer->shouldReceive('withCertificateFile')
            ->andReturnUsing(fn (Empresa $empresa, callable $callback) => $callback('/tmp/certificado-teste.pfx'));

        return $signer;
    }

    private function empresa(): Empresa
    {
        $empresa = new Empresa;
        $empresa->forceFill([
            'ambiente' => 2,
            'senhaCertificado' => 'senha',
        ]);

        return $empresa;
    }

    private function http(array $responses, array &$history): ClientInterface
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($history));

        return new Client([
            'handler' => $stack,
            'base_uri' => 'https://example.test/API/SefinNacional/',
        ]);
    }
}

class TestableNFSeClient extends NFSeClient
{
    public function __construct(NFSeSigner $signer, private ClientInterface $http)
    {
        parent::__construct($signer);
    }

    protected function createSefinClient(Empresa $empresa): ClientInterface
    {
        return $this->http;
    }
}
