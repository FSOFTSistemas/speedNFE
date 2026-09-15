<?php

namespace App\Services\NFSe;

use App\Models\Empresa;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Throwable;

class NFSeClient
{
    private NFSeSigner $signer;

    public function __construct(NFSeSigner $signer)
    {
        $this->signer = $signer;
    }

    public function emitir(string $signedDpsXml, Empresa $empresa): array
    {
        $response = $this->request('POST', 'nfse', $empresa, [
            'json' => [
                'dpsXmlGZipB64' => base64_encode(gzencode($signedDpsXml)),
            ],
        ]);

        return $this->withDecodedXml($response);
    }

    public function consultarDps(string $idDps, Empresa $empresa): array
    {
        return $this->withDecodedXml($this->request('GET', 'dps/'.rawurlencode($idDps), $empresa));
    }

    public function consultarNFSe(string $chave, Empresa $empresa): array
    {
        return $this->withDecodedXml($this->request('GET', 'nfse/'.rawurlencode($chave), $empresa));
    }

    public function registrarEvento(string $chave, string $signedEventXml, Empresa $empresa): array
    {
        $response = $this->request('POST', 'nfse/'.rawurlencode($chave).'/eventos', $empresa, [
            'json' => [
                'pedidoRegistroEventoXmlGZipB64' => base64_encode(gzencode($signedEventXml)),
            ],
        ]);

        return $this->withDecodedEvent($response);
    }

    public function consultarEvento(string $chave, string $tipoEvento, int $sequencia, Empresa $empresa): array
    {
        $uri = sprintf(
            'nfse/%s/eventos/%s/%d',
            rawurlencode($chave),
            rawurlencode($tipoEvento),
            $sequencia
        );

        return $this->withDecodedEvent($this->request('GET', $uri, $empresa));
    }

    public function baixarDanfse(string $chave): array
    {
        try {
            $response = $this->createDanfseClient()->request('GET', 'danfse/'.rawurlencode($chave), [
                'headers' => ['Accept' => 'application/pdf'],
                'http_errors' => false,
            ]);

            return [
                'ok' => $response->getStatusCode() >= 200 && $response->getStatusCode() < 300,
                'status' => $response->getStatusCode(),
                'content_type' => $response->getHeaderLine('Content-Type'),
                'body' => (string) $response->getBody(),
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'status' => null,
                'content_type' => null,
                'body' => $e->getMessage(),
            ];
        }
    }

    private function request(string $method, string $uri, Empresa $empresa, array $options = []): array
    {
        try {
            $response = $this->signer->withCertificateFile($empresa, function (string $certificatePath) use ($method, $uri, $empresa, $options) {
                return $this->createSefinClient($empresa)->request($method, ltrim($uri, '/'), array_replace_recursive([
                    'curl' => [
                        CURLOPT_SSLCERTTYPE => 'P12',
                        CURLOPT_SSLCERT => $certificatePath,
                        CURLOPT_SSLCERTPASSWD => (string) $empresa->senhaCertificado,
                        CURLOPT_KEYPASSWD => (string) $empresa->senhaCertificado,
                    ],
                ], $options));
            });

            $contents = (string) $response->getBody();
            $decoded = json_decode($contents, true);

            return [
                'ok' => $response->getStatusCode() >= 200 && $response->getStatusCode() < 300,
                'status' => $response->getStatusCode(),
                'body' => is_array($decoded) ? $decoded : ['raw' => $contents],
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'status' => null,
                'body' => ['erros' => [$e->getMessage()]],
            ];
        }
    }

    protected function createSefinClient(Empresa $empresa): ClientInterface
    {
        return new Client([
            'base_uri' => rtrim($this->baseUrl($empresa), '/').'/',
            'timeout' => config('nfse.sefin.timeout', 30),
            'connect_timeout' => config('nfse.sefin.connect_timeout', 10),
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    protected function createDanfseClient(): ClientInterface
    {
        return new Client([
            'base_uri' => rtrim(config('nfse.danfse.base_url'), '/').'/',
            'timeout' => config('nfse.sefin.timeout', 30),
            'connect_timeout' => config('nfse.sefin.connect_timeout', 10),
        ]);
    }

    private function baseUrl(Empresa $empresa): string
    {
        if ((int) $empresa->ambiente === 1) {
            return config('nfse.sefin.producao_base_url');
        }

        return config('nfse.sefin.restrita_base_url');
    }

    private function decodeGzipB64(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            return null;
        }

        $xml = @gzdecode($decoded);

        return $xml !== false ? $xml : null;
    }

    private function withDecodedXml(array $response): array
    {
        $body = $response['body'];
        if (is_array($body)) {
            $body['nfseXml'] = $this->decodeGzipB64($body['nfseXmlGZipB64'] ?? null);
        }

        return array_merge($response, ['body' => $body]);
    }

    private function withDecodedEvent(array $response): array
    {
        $body = $response['body'];
        if (is_array($body)) {
            $body['eventoXml'] = $this->decodeGzipB64($body['eventoXmlGZipB64'] ?? null);
            $body['pedidoRegistroEventoXml'] = $this->decodeGzipB64($body['pedidoRegistroEventoXmlGZipB64'] ?? null);
        }

        return array_merge($response, ['body' => $body]);
    }
}
