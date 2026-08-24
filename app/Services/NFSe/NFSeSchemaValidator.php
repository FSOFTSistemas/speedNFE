<?php

namespace App\Services\NFSe;

use DOMDocument;
use RuntimeException;

class NFSeSchemaValidator
{
    public function validateDps(string $xml): void
    {
        $schema = rtrim(config('nfse.schemas_path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'DPS_v1.01.xsd';
        if (! file_exists($schema)) {
            throw new RuntimeException('Schema XSD da DPS não encontrado: '.$schema);
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        libxml_use_internal_errors(true);
        libxml_clear_errors();

        if (! $dom->loadXML($xml)) {
            $errors = $this->errors();
            throw new RuntimeException('XML DPS inválido: '.implode(' | ', $errors));
        }

        if (! $dom->schemaValidate($schema)) {
            $errors = $this->errors();
            throw new RuntimeException('XML DPS fora do schema: '.implode(' | ', $errors));
        }

        libxml_clear_errors();
        libxml_use_internal_errors(false);
    }

    private function errors(): array
    {
        $errors = [];
        foreach (libxml_get_errors() as $error) {
            $errors[] = trim($error->message).' linha '.$error->line;
        }

        libxml_clear_errors();

        return $errors ?: ['erro desconhecido de XML'];
    }
}
