<?php

namespace App\Services\NFSe;

use App\Models\Empresa;
use App\Services\EmpresaCertificate;
use NFePHP\Common\Certificate;
use NFePHP\Common\Signer;

class NFSeSigner
{
    public function __construct(private EmpresaCertificate $certificates) {}

    public function signDps(string $xml, Empresa $empresa): string
    {
        return Signer::sign(
            $this->certificate($empresa),
            $xml,
            'infDPS',
            'Id',
            OPENSSL_ALGO_SHA256,
            Signer::CANONICAL,
            'DPS'
        );
    }

    public function signEvent(string $xml, Empresa $empresa): string
    {
        return Signer::sign(
            $this->certificate($empresa),
            $xml,
            'infPedReg',
            'Id',
            OPENSSL_ALGO_SHA256,
            Signer::CANONICAL,
            'pedRegEvento'
        );
    }

    public function withCertificateFile(Empresa $empresa, callable $callback)
    {
        return $this->certificates->withTemporaryFile($empresa, $callback);
    }

    private function certificate(Empresa $empresa): Certificate
    {
        return Certificate::readPfx(
            $this->certificates->content($empresa),
            (string) $empresa->senhaCertificado
        );
    }
}
