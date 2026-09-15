<?php

namespace App\Services\NFSe;

use App\Models\NFSe;
use Carbon\CarbonInterface;
use DOMDocument;
use DOMElement;
use RuntimeException;

class CancelamentoXmlBuilder
{
    private const NS = 'http://www.sped.fazenda.gov.br/nfse';

    public function build(NFSe $nfse, string $codigoMotivo, string $justificativa, ?CarbonInterface $dataEvento = null): string
    {
        $nfse->loadMissing('empresa');
        $chave = preg_replace('/\s+/', '', (string) $nfse->chave);
        if (! preg_match('/^[0-9]{6}[0-9A-Z]{14}[0-9]{30}$/', $chave)) {
            throw new RuntimeException('Chave de acesso da NFS-e inválida para cancelamento.');
        }
        if (! in_array($codigoMotivo, ['1', '2', '9'], true)) {
            throw new RuntimeException('Código do motivo de cancelamento inválido.');
        }

        $justificativa = trim($justificativa);
        if (mb_strlen($justificativa) < 15 || mb_strlen($justificativa) > 255) {
            throw new RuntimeException('A justificativa do cancelamento deve ter entre 15 e 255 caracteres.');
        }

        $documento = preg_replace('/\D/', '', (string) $nfse->empresa->cpf_cnpj);
        if (! in_array(strlen($documento), [11, 14], true)) {
            throw new RuntimeException('CPF/CNPJ do autor do cancelamento inválido.');
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;

        $root = $doc->createElementNS(self::NS, 'pedRegEvento');
        $root->setAttribute('versao', config('nfse.layout_version', '1.01'));
        $doc->appendChild($root);

        $inf = $this->append($doc, $root, 'infPedReg');
        $inf->setAttribute('Id', 'PRE'.$chave.'101101');
        $this->append($doc, $inf, 'tpAmb', (string) $nfse->tpAmb);
        $this->append($doc, $inf, 'verAplic', config('nfse.app_version', 'SpeedNFE-1.0'));
        $this->append($doc, $inf, 'dhEvento', ($dataEvento ?: now())->format('Y-m-d\TH:i:sP'));
        $this->append($doc, $inf, strlen($documento) === 14 ? 'CNPJAutor' : 'CPFAutor', $documento);
        $this->append($doc, $inf, 'chNFSe', $chave);

        $evento = $this->append($doc, $inf, 'e101101');
        $this->append($doc, $evento, 'xDesc', 'Cancelamento de NFS-e');
        $this->append($doc, $evento, 'cMotivo', $codigoMotivo);
        $this->append($doc, $evento, 'xMotivo', $justificativa);

        return $doc->saveXML($doc->documentElement, LIBXML_NOXMLDECL);
    }

    private function append(DOMDocument $doc, DOMElement $parent, string $tag, ?string $value = null): DOMElement
    {
        $node = $doc->createElementNS(self::NS, $tag);
        if ($value !== null) {
            $node->appendChild($doc->createTextNode($value));
        }
        $parent->appendChild($node);

        return $node;
    }
}
