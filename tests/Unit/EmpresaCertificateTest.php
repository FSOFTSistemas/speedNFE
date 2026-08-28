<?php

namespace Tests\Unit;

use App\Models\Empresa;
use App\Services\EmpresaCertificate;
use App\Services\NFSe\NFSeSigner;
use Tests\TestCase;

class EmpresaCertificateTest extends TestCase
{
    public function test_le_certificado_criptografado_do_banco_e_remove_arquivo_temporario(): void
    {
        $pfx = $this->pfx('senha-segura');
        $empresa = new Empresa;
        $empresa->forceFill([
            'razao' => 'Empresa Certificado Teste',
            'certificado_conteudo' => $pfx,
            'senhaCertificado' => 'senha-segura',
        ]);
        $service = app(EmpresaCertificate::class);

        $this->assertSame($pfx, $service->content($empresa));

        $temporaryPath = null;
        $service->withTemporaryFile($empresa, function (string $path) use (&$temporaryPath, $pfx) {
            $temporaryPath = $path;
            $this->assertFileExists($path);
            $this->assertSame($pfx, file_get_contents($path));
            $this->assertSame('0600', substr(sprintf('%o', fileperms($path)), -4));
        });

        $this->assertNotNull($temporaryPath);
        $this->assertFileDoesNotExist($temporaryPath);
    }

    public function test_assina_dps_usando_certificado_do_banco(): void
    {
        $empresa = new Empresa;
        $empresa->forceFill([
            'razao' => 'Empresa Assinatura Teste',
            'certificado_conteudo' => $this->pfx('senha-segura'),
            'senhaCertificado' => 'senha-segura',
        ]);
        $xml = '<DPS xmlns="http://www.sped.fazenda.gov.br/nfse" versao="1.01"><infDPS Id="DPS261160611234567800019900001000000000000001"/></DPS>';

        $signed = app(NFSeSigner::class)->signDps($xml, $empresa);

        $this->assertStringContainsString('<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">', $signed);
        $this->assertStringContainsString('DPS261160611234567800019900001000000000000001', $signed);
    }

    private function pfx(string $password): string
    {
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $csr = openssl_csr_new([
            'countryName' => 'BR',
            'stateOrProvinceName' => 'PE',
            'localityName' => 'Recife',
            'organizationName' => 'SpeedNFE Testes',
            'commonName' => 'SpeedNFE Testes',
        ], $privateKey, ['digest_alg' => 'sha256']);
        $certificate = openssl_csr_sign($csr, null, $privateKey, 1, ['digest_alg' => 'sha256']);

        $pfx = '';
        $this->assertTrue(openssl_pkcs12_export($certificate, $pfx, $privateKey, $password));

        return $pfx;
    }
}
