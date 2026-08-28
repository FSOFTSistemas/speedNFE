<?php

namespace App\Services;

use App\Models\Empresa;
use NFePHP\Common\Certificate;
use RuntimeException;

class EmpresaCertificate
{
    public function content(Empresa $empresa): string
    {
        $databaseContent = $empresa->certificado_conteudo;
        if (is_string($databaseContent) && $databaseContent !== '') {
            return $databaseContent;
        }

        $legacyPath = $this->legacyPath($empresa);
        if ($legacyPath !== null) {
            $content = file_get_contents($legacyPath);
            if ($content === false) {
                throw new RuntimeException('Não foi possível ler o certificado digital legado da empresa.');
            }

            return $content;
        }

        $legacyContent = $empresa->getRawOriginal('certificado');
        if (is_string($legacyContent) && strlen($legacyContent) > 100 && str_contains($legacyContent, "\0")) {
            return $legacyContent;
        }

        throw new RuntimeException('Certificado digital da empresa não encontrado no banco de dados.');
    }

    public function validate(string $content, string $password): void
    {
        try {
            Certificate::readPfx($content, $password);
        } catch (\Throwable $e) {
            throw new RuntimeException('Certificado digital ou senha inválidos.', 0, $e);
        }
    }

    public function withTemporaryFile(Empresa $empresa, callable $callback)
    {
        $path = tempnam(sys_get_temp_dir(), 'speednfe-cert-');
        if ($path === false) {
            throw new RuntimeException('Não foi possível criar o arquivo temporário do certificado.');
        }

        try {
            if (file_put_contents($path, $this->content($empresa), LOCK_EX) === false) {
                throw new RuntimeException('Não foi possível materializar temporariamente o certificado.');
            }

            chmod($path, 0600);

            return $callback($path);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function legacyPath(Empresa $empresa): ?string
    {
        $basePath = realpath(storage_path('app/certificados'));
        if ($basePath === false) {
            return null;
        }

        $candidates = [];
        $legacy = $empresa->getRawOriginal('certificado');
        if (is_string($legacy) && $legacy !== '' && ! str_contains($legacy, "\0")) {
            $candidates[] = storage_path('app/'.$legacy);
        }
        if ($empresa->razao) {
            $candidates[] = storage_path('app/certificados/'.$empresa->razao.'.pfx');
        }

        foreach (array_unique($candidates) as $candidate) {
            $resolved = realpath($candidate);
            if ($resolved !== false && str_starts_with($resolved, $basePath.DIRECTORY_SEPARATOR) && is_file($resolved)) {
                return $resolved;
            }
        }

        return null;
    }
}
