<?php

namespace App\Services\NFSe;

use App\Models\Empresa;
use NFePHP\Common\Certificate;
use NFePHP\Common\Signer;
use RuntimeException;

class NFSeSigner
{
    public function signDps(string $xml, Empresa $empresa): string
    {
        $path = $this->certificatePath($empresa);
        if (! file_exists($path)) {
            throw new RuntimeException('Certificado digital da empresa não encontrado para assinar a NFS-e.');
        }

        $content = file_get_contents($path);

        return Signer::sign(
            Certificate::readPfx($content, $empresa->senhaCertificado),
            $xml,
            'infDPS',
            'Id',
            OPENSSL_ALGO_SHA256,
            Signer::CANONICAL,
            'DPS'
        );
    }

    public function certificatePath(Empresa $empresa): string
    {
        if (! empty($empresa->certificado)) {
            return storage_path('app/'.$empresa->certificado);
        }

        return storage_path('app/certificados/'.$empresa->razao.'.pfx');
    }
}
