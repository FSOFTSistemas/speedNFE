<?php

namespace App\Services\NFSe;

use App\Models\Empresa;
use App\Models\Endereco;
use App\Models\NFSe;
use App\Utils\FormatationUtil;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use RuntimeException;

class DpsXmlBuilder
{
    private const NS = 'http://www.sped.fazenda.gov.br/nfse';

    public function build(NFSe $nfse): string
    {
        $nfse->loadMissing(['empresa.endereco', 'cliente.endereco', 'servico']);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;

        $dps = $doc->createElementNS(self::NS, 'DPS');
        $dps->setAttribute('versao', config('nfse.layout_version', '1.01'));
        $doc->appendChild($dps);

        $infDps = $this->append($doc, $dps, 'infDPS');
        $infDps->setAttribute('Id', $this->dpsId($nfse));

        $this->append($doc, $infDps, 'tpAmb', (string) $nfse->tpAmb);
        $this->append($doc, $infDps, 'dhEmi', $this->dateTime($nfse->data_emissao ?: now()));
        $this->append($doc, $infDps, 'verAplic', config('nfse.app_version', 'SpeedNFE-1.0'));
        $this->append($doc, $infDps, 'serie', (string) $this->required($nfse->serieDPS, 'Série da DPS não informada.'));
        $this->append($doc, $infDps, 'nDPS', (string) $this->required($nfse->nDPS, 'Número da DPS não informado.'));
        $this->append($doc, $infDps, 'dCompet', $this->date($nfse->data_competencia));
        $this->append($doc, $infDps, 'tpEmit', (string) ($nfse->tpEmit ?: 1));
        $this->append($doc, $infDps, 'cLocEmi', $this->digits($this->required($nfse->cLocEmi, 'Município emissor da NFS-e não informado.'), 7));

        $this->appendPrestador($doc, $infDps, $nfse->empresa);
        $this->appendPessoa($doc, $infDps, 'toma', $nfse->cliente);
        $this->appendServico($doc, $infDps, $nfse);
        $this->appendValores($doc, $infDps, $nfse);
        $this->appendIbsCbs($doc, $infDps, $nfse);

        return $doc->saveXML($doc->documentElement, LIBXML_NOXMLDECL);
    }

    public function dpsId(NFSe $nfse): string
    {
        $empresa = $nfse->empresa;
        $documento = $this->digitsOnly($this->required($empresa->cpf_cnpj ?? null, 'CPF/CNPJ da empresa não informado.'));
        $tipoInscricao = strlen($documento) === 14 ? '1' : '2';
        $inscricaoFederal = $tipoInscricao === '1' ? $documento : str_pad($documento, 14, '0', STR_PAD_LEFT);

        if (($tipoInscricao === '1' && strlen($inscricaoFederal) !== 14) || ($tipoInscricao === '2' && strlen($documento) !== 11)) {
            throw new RuntimeException('CPF/CNPJ da empresa inválido para formar o Id da DPS.');
        }

        $serie = str_pad($this->digitsOnly($this->required($nfse->serieDPS, 'Série da DPS não informada.')), 5, '0', STR_PAD_LEFT);
        $numero = str_pad((string) (int) $this->required($nfse->nDPS, 'Número da DPS não informado.'), 15, '0', STR_PAD_LEFT);
        $municipio = $this->digits($this->required($nfse->cLocEmi, 'Município emissor da NFS-e não informado.'), 7);

        return 'DPS'.$municipio.$tipoInscricao.$inscricaoFederal.$serie.$numero;
    }

    private function appendPrestador(DOMDocument $doc, DOMElement $parent, Empresa $empresa): void
    {
        $prest = $this->append($doc, $parent, 'prest');
        $this->appendDocumento($doc, $prest, $empresa->cpf_cnpj, 'prestador');
        $this->appendIfFilled($doc, $prest, 'IM', $empresa->inscricao_municipal);
        $this->appendIfFilled($doc, $prest, 'xNome', $this->text($empresa->razao));
        $this->appendEndereco($doc, $prest, $empresa->endereco);
        $this->appendIfFilled($doc, $prest, 'fone', FormatationUtil::normalizarTelefoneFiscal($empresa->celular));
        $this->appendIfFilled($doc, $prest, 'email', $empresa->email ?? null);

        $regTrib = $this->append($doc, $prest, 'regTrib');
        $opSimpNac = $this->opSimpNac($empresa);
        $this->append($doc, $regTrib, 'opSimpNac', $opSimpNac);
        if ($opSimpNac === '3') {
            $this->append($doc, $regTrib, 'regApTribSN', '1');
        }
        $this->append($doc, $regTrib, 'regEspTrib', '0');
    }

    private function appendPessoa(DOMDocument $doc, DOMElement $parent, string $tag, $pessoa): void
    {
        if (! $pessoa) {
            return;
        }

        $node = $this->append($doc, $parent, $tag);
        $this->appendDocumento($doc, $node, $pessoa->cpf_cnpj, 'tomador');
        $this->append($doc, $node, 'xNome', $this->required($this->text($pessoa->nome), 'Nome do tomador não informado.'));
        $this->appendEndereco($doc, $node, $pessoa->endereco);
        $this->appendIfFilled($doc, $node, 'fone', FormatationUtil::normalizarTelefoneFiscal($pessoa->celular ?: $pessoa->telefone));
    }

    private function appendDocumento(DOMDocument $doc, DOMElement $parent, $documento, string $label): void
    {
        $digits = $this->digitsOnly($this->required($documento, 'CPF/CNPJ do '.$label.' não informado.'));

        if (strlen($digits) === 14) {
            $this->append($doc, $parent, 'CNPJ', $digits);

            return;
        }

        if (strlen($digits) === 11) {
            $this->append($doc, $parent, 'CPF', $digits);

            return;
        }

        throw new RuntimeException('CPF/CNPJ do '.$label.' inválido para NFS-e.');
    }

    private function appendEndereco(DOMDocument $doc, DOMElement $parent, ?Endereco $endereco): void
    {
        if (! $endereco || ! $endereco->codigoIBGE || ! $endereco->cep || ! $endereco->rua || ! $endereco->numero || ! $endereco->bairro) {
            return;
        }

        $end = $this->append($doc, $parent, 'end');
        $endNac = $this->append($doc, $end, 'endNac');
        $this->append($doc, $endNac, 'cMun', $this->digits($endereco->codigoIBGE, 7));
        $this->append($doc, $endNac, 'CEP', $this->digits($endereco->cep, 8));
        $this->append($doc, $end, 'xLgr', $this->text($endereco->rua));
        $this->append($doc, $end, 'nro', $this->text($endereco->numero));
        $this->appendIfFilled($doc, $end, 'xCpl', $this->text($endereco->complemento));
        $this->append($doc, $end, 'xBairro', $this->text($endereco->bairro));
    }

    private function appendServico(DOMDocument $doc, DOMElement $parent, NFSe $nfse): void
    {
        $serv = $this->append($doc, $parent, 'serv');
        $locPrest = $this->append($doc, $serv, 'locPrest');
        $this->append($doc, $locPrest, 'cLocPrestacao', $this->digits($this->required($nfse->cLocPrestacao, 'Município de prestação não informado.'), 7));

        $cServ = $this->append($doc, $serv, 'cServ');
        $this->append($doc, $cServ, 'cTribNac', $this->digits($this->required($nfse->cTribNac, 'Código nacional de serviço não informado.'), 6));
        $this->appendIfFilled($doc, $cServ, 'cTribMun', $nfse->cTribMun);
        $this->append($doc, $cServ, 'xDescServ', $this->required($this->text($nfse->discriminacao), 'Discriminação do serviço não informada.'));
        $this->appendIfFilled($doc, $cServ, 'cNBS', $this->digitsOptional($nfse->cNBS, 9));

        $codigoInterno = $this->codigoInterno($nfse->servico->codigo ?? null);
        $this->appendIfFilled($doc, $cServ, 'cIntContrib', $codigoInterno);

        if ($this->filled($nfse->informacoes_complementares)) {
            $infoCompl = $this->append($doc, $serv, 'infoCompl');
            $this->append($doc, $infoCompl, 'xInfComp', $this->text($nfse->informacoes_complementares));
        }
    }

    private function appendValores(DOMDocument $doc, DOMElement $parent, NFSe $nfse): void
    {
        $valores = $this->append($doc, $parent, 'valores');
        $vServPrest = $this->append($doc, $valores, 'vServPrest');
        $this->append($doc, $vServPrest, 'vServ', $this->money($nfse->vServ));

        if ((float) $nfse->vDescIncond > 0 || (float) $nfse->vDescCond > 0) {
            $descontos = $this->append($doc, $valores, 'vDescCondIncond');
            $this->appendIfPositive($doc, $descontos, 'vDescIncond', $nfse->vDescIncond);
            $this->appendIfPositive($doc, $descontos, 'vDescCond', $nfse->vDescCond);
        }

        if ((float) $nfse->vDeducaoReducao > 0) {
            $deducao = $this->append($doc, $valores, 'vDedRed');
            $this->append($doc, $deducao, 'vDR', $this->money($nfse->vDeducaoReducao));
        }

        $trib = $this->append($doc, $valores, 'trib');
        $tribMun = $this->append($doc, $trib, 'tribMun');
        $this->append($doc, $tribMun, 'tribISSQN', $this->tribIssqn($nfse));
        $this->append($doc, $tribMun, 'tpRetISSQN', $this->tipoRetIssqn($nfse));
        if ((float) $nfse->pAliq > 0) {
            $this->append($doc, $tribMun, 'pAliq', $this->percent($nfse->pAliq));
        }

        $totTrib = $this->append($doc, $trib, 'totTrib');
        $this->append($doc, $totTrib, 'indTotTrib', '0');
    }

    private function appendIbsCbs(DOMDocument $doc, DOMElement $parent, NFSe $nfse): void
    {
        $finNFSe = (string) ($nfse->finNFSe ?? '0');
        $indFinal = (string) ($nfse->indFinal ?? '0');
        $indDest = (string) ($nfse->indDest ?? '0');

        if ($finNFSe !== '0') {
            throw new RuntimeException('Finalidade da NFS-e inválida para o leiaute nacional atual.');
        }
        if (! in_array($indFinal, ['0', '1'], true)) {
            throw new RuntimeException('Indicador de consumidor final inválido para IBS/CBS.');
        }
        if (! in_array($indDest, ['0', '1'], true)) {
            throw new RuntimeException('Indicador de destinatário inválido para IBS/CBS.');
        }

        $ibsCbs = $this->append($doc, $parent, 'IBSCBS');
        $this->append($doc, $ibsCbs, 'finNFSe', $finNFSe);
        $this->append($doc, $ibsCbs, 'indFinal', $indFinal);
        $this->append($doc, $ibsCbs, 'cIndOp', $this->digits($this->required($nfse->cIndOp, 'Indicador da operação de IBS/CBS não informado.'), 6));
        $this->append($doc, $ibsCbs, 'indDest', $indDest);

        $valores = $this->append($doc, $ibsCbs, 'valores');
        $tributos = $this->append($doc, $valores, 'trib');
        $grupo = $this->append($doc, $tributos, 'gIBSCBS');
        $this->append($doc, $grupo, 'CST', $this->digits($this->required($nfse->cst_ibs_cbs, 'CST do IBS/CBS não informado.'), 3));
        $this->append($doc, $grupo, 'cClassTrib', $this->digits($this->required($nfse->cClassTrib, 'Classificação tributária do IBS/CBS não informada.'), 6));
    }

    private function append(DOMDocument $doc, DOMElement $parent, string $tag, $value = null): DOMElement
    {
        $node = $doc->createElementNS(self::NS, $tag);
        if ($value !== null) {
            $node->appendChild($doc->createTextNode((string) $value));
        }
        $parent->appendChild($node);

        return $node;
    }

    private function appendIfFilled(DOMDocument $doc, DOMElement $parent, string $tag, $value): void
    {
        if ($this->filled($value)) {
            $this->append($doc, $parent, $tag, $value);
        }
    }

    private function appendIfPositive(DOMDocument $doc, DOMElement $parent, string $tag, $value): void
    {
        if ((float) $value > 0) {
            $this->append($doc, $parent, $tag, $this->money($value));
        }
    }

    private function required($value, string $message)
    {
        if (! $this->filled($value)) {
            throw new RuntimeException($message);
        }

        return $value;
    }

    private function filled($value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    private function digits($value, int $length): string
    {
        $digits = $this->digitsOnly($value);
        if (strlen($digits) !== $length) {
            throw new RuntimeException("Campo fiscal deve conter {$length} dígitos.");
        }

        return $digits;
    }

    private function digitsOptional($value, int $length): ?string
    {
        if (! $this->filled($value)) {
            return null;
        }

        return $this->digits($value, $length);
    }

    private function digitsOnly($value): string
    {
        return preg_replace('/\D/', '', (string) $value);
    }

    private function date($value): string
    {
        return Carbon::parse($this->required($value, 'Data de competência não informada.'))->format('Y-m-d');
    }

    private function dateTime($value): string
    {
        return Carbon::parse($value)->format('Y-m-d\TH:i:sP');
    }

    private function money($value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function percent($value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function text($value): ?string
    {
        if (! $this->filled($value)) {
            return null;
        }

        $text = trim(FormatationUtil::retiraAcentos((string) $value));
        $text = preg_replace('/\s+/', ' ', $text);

        return $text;
    }

    private function codigoInterno($value): ?string
    {
        if (! $this->filled($value)) {
            return null;
        }

        $codigo = preg_replace('/[^a-zA-Z0-9]/', '', (string) $value);

        return $codigo !== '' ? substr($codigo, 0, 20) : null;
    }

    private function opSimpNac(Empresa $empresa): string
    {
        if ((string) $empresa->crt === '4') {
            return '2';
        }

        if (in_array((string) $empresa->crt, ['1', '2'], true)) {
            return '3';
        }

        return '1';
    }

    private function tribIssqn(NFSe $nfse): string
    {
        $value = (string) ($nfse->servico->tribISSQN ?? '1');

        return in_array($value, ['1', '2', '3', '4'], true) ? $value : '1';
    }

    private function tipoRetIssqn(NFSe $nfse): string
    {
        $value = (string) ($nfse->servico->tpRetISSQN ?? '1');

        return in_array($value, ['1', '2', '3'], true) ? $value : '1';
    }
}
