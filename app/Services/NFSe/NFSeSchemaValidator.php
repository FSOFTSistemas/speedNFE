<?php

namespace App\Services\NFSe;

use DOMDocument;
use RuntimeException;

class NFSeSchemaValidator
{
    public function validateDps(string $xml): void
    {
        $this->validate($xml, 'DPS_v1.01.xsd', 'DPS');
    }

    public function validateEvent(string $xml): void
    {
        $this->validate($xml, 'pedRegEvento_v1.01.xsd', 'pedido de evento');
    }

    private function validate(string $xml, string $schemaFile, string $label): void
    {
        $schema = rtrim(config('nfse.schemas_path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$schemaFile;
        if (! file_exists($schema)) {
            throw new RuntimeException('Schema XSD do '.$label.' não encontrado: '.$schema);
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $previousUseInternalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            if (! $dom->loadXML($xml)) {
                $errors = $this->errors();
                throw new RuntimeException('XML do '.$label.' inválido: '.implode(' | ', $errors));
            }

            if (! $dom->schemaValidate($schema)) {
                $errors = $this->errors();
                throw new RuntimeException('XML do '.$label.' fora do schema: '.implode(' | ', $errors));
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseInternalErrors);
        }
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
