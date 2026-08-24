<?php

namespace App\Services\NFSe;

use App\Models\Empresa;
use GuzzleHttp\Client;
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
        $response = $this->request('POST', '/nfse', $empresa, [
            'json' => [
                'dpsXmlGZipB64' => base64_encode(gzencode($signedDpsXml)),
            ],
        ]);

        $body = $response['body'];
        $body['nfseXml'] = $this->decodeGzipB64($body['nfseXmlGZipB64'] ?? null);

        return array_merge($response, ['body' => $body]);
    }

    public function consultarDps(string $idDps, Empresa $empresa): array
    {
        return $this->request('GET', '/dps/'.$idDps, $empresa);
    }

    public function consultarNFSe(string $chave, Empresa $empresa): array
    {
        return $this->request('GET', '/nfse/'.$chave, $empresa);
    }

    private function request(string $method, string $uri, Empresa $empresa, array $options = []): array
    {
        try {
            $client = new Client([
                'base_uri' => rtrim($this->baseUrl($empresa), '/'),
                'timeout' => config('nfse.sefin.timeout', 30),
                'http_errors' => false,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $response = $client->request($method, $uri, array_replace_recursive([
                'curl' => [
                    CURLOPT_SSLCERTTYPE => 'P12',
                    CURLOPT_SSLCERT => $this->signer->certificatePath($empresa),
                    CURLOPT_SSLCERTPASSWD => (string) $empresa->senhaCertificado,
                    CURLOPT_KEYPASSWD => (string) $empresa->senhaCertificado,
                ],
            ], $options));

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

        $xml = gzdecode($decoded);

        return $xml !== false ? $xml : null;
    }
}
