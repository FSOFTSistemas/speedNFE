<?php

namespace App\Services;

use Efi\EfiPay;
use Efi\Exception\EfiException;

class EfiPixService
{
    private EfiPay $client;

    public function __construct()
    {
        $options = [
            'clientId'       => config('efipay.client_id'),
            'clientSecret'   => config('efipay.client_secret'),
            // usa caminho ABSOLUTO para o certificado:
            'certificate'    => realpath(base_path(config('efipay.certificate'))),
            // senha do .p12 (se houver):
            'pwdCertificate' => config('efipay.certificate_pass'),
            'sandbox'        => config('efipay.sandbox'),
        ];

        $this->client = new EfiPay($options);
    }

    /** POST /v2/cob (cobrança imediata) */
    public function criarCobranca(array $payload): array
    {
        return $this->client->pixCreateImmediateCharge([], $payload);
    }

    /** GET /v2/loc/{id}/qrcode */
    public function obterQrCodePorLocId(int $locId): array
    {
        return $this->client->pixGenerateQRCode(['id' => $locId], []);
    }

    /** GET /v2/cob/{txid} */
    public function consultarCobranca(string $txid): array
    {
        return $this->client->pixDetailCharge(['txid' => $txid]);
    }

    /** PUT /v2/webhook/:chave */
    public function configurarWebhook(string $pixKey, string $url, bool $skipMtls = false): array
    {
        $headers = $skipMtls ? ['x-skip-mtls-checking' => 'true'] : [];
        return $this->client->pixConfigWebhook(
            ['chave' => $pixKey],
            ['webhookUrl' => $url],
            $headers
        );
    }
}