<?php

namespace App\Services;

use App\Models\Empresa;
use App\Utils\FormatationUtil;
use Exception;
use NFePHP\Common\Certificate;
use NFePHP\NFCom\Common\Standardize;
use NFePHP\NFCom\Complements;
use NFePHP\NFCom\Make;
use NFePHP\NFCom\Tools;

class NFComService
{
    private $tools;

    public function __construct($config, $emitente)
    {
        $certificado = file_get_contents(storage_path('app/certificados/'.$emitente->razao.'.pfx'));
        $this->tools = new Tools(json_encode($config), Certificate::readPfx($certificado, $emitente->senhaCertificado));
    }

    /**
     * Monta o XML da NFCom a partir do cabeçalho (NFCom) e seus itens (NFComItem).
     * Não assina nem transmite - ver sign()/transmitir().
     */
    public function gerarXml($nfcom, $emitente)
    {
        $nfcomMake = new Make;

        // infNFCom - Id vazio: a lib recalcula a chave de 44 dígitos automaticamente
        $stdInf = new \stdClass;
        $stdInf->Id = '';
        $stdInf->versao = '1.00';
        $nfcomMake->tagInfNFCom($stdInf);

        // ide
        $stdIde = new \stdClass;
        $stdIde->cUF = Empresa::getCUF($emitente->endereco->uf);
        $stdIde->tpAmb = (int) $emitente->ambiente;
        $stdIde->mod = 62;
        $stdIde->serie = $nfcom->serie;
        $stdIde->nNF = $nfcom->nro;
        $stdIde->dhEmi = date('Y-m-d\TH:i:sP');
        $stdIde->tpEmis = 1;
        $stdIde->nSiteAutoriz = '0';
        $stdIde->cMunFG = FormatationUtil::retiraPontuacoes($emitente->endereco->codigoIBGE);
        $stdIde->finNFCom = '0';
        $stdIde->tpFat = '1';
        $stdIde->verProc = '1.0';
        $nfcomMake->tagIde($stdIde);

        // emit
        $stdEmit = new \stdClass;
        $stdEmit->CNPJ = FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj);
        $stdEmit->IE = FormatationUtil::retiraPontuacoes($emitente->rg_ie);
        $stdEmit->CRT = $emitente->crt;
        $stdEmit->xNome = FormatationUtil::retiraAcentos($emitente->razao);
        $stdEmit->xFant = FormatationUtil::retiraAcentos($emitente->fantasia);
        $nfcomMake->tagEmit($stdEmit);

        $stdEnderEmit = new \stdClass;
        $stdEnderEmit->xLgr = FormatationUtil::retiraAcentos($emitente->endereco->rua);
        $stdEnderEmit->nro = $emitente->endereco->numero;
        $stdEnderEmit->xCpl = $emitente->endereco->complemento ?? '';
        $stdEnderEmit->xBairro = FormatationUtil::retiraAcentos($emitente->endereco->bairro);
        $stdEnderEmit->cMun = FormatationUtil::retiraPontuacoes($emitente->endereco->codigoIBGE);
        $stdEnderEmit->xMun = FormatationUtil::retiraAcentos($emitente->endereco->cidade);
        $stdEnderEmit->CEP = FormatationUtil::retiraPontuacoes($emitente->endereco->cep);
        $stdEnderEmit->UF = $emitente->endereco->uf;
        $stdEnderEmit->fone = FormatationUtil::retiraPontuacoes($emitente->celular);
        $nfcomMake->tagEnderEmit($stdEnderEmit);

        // dest / enderDest - assinante é o Cliente já cadastrado
        $cliente = $nfcom->cliente;
        $stdDest = new \stdClass;
        $stdDest->xNome = FormatationUtil::retiraAcentos($cliente->nome);
        if (strlen(FormatationUtil::retiraPontuacoes($cliente->cpf_cnpj)) > 11) {
            $stdDest->CNPJ = FormatationUtil::retiraPontuacoes($cliente->cpf_cnpj);
        } else {
            $stdDest->CPF = FormatationUtil::retiraPontuacoes($cliente->cpf_cnpj);
        }
        $stdDest->indIEDest = $cliente->contribuinte ? 1 : 9;
        $stdDest->IE = $cliente->contribuinte ? FormatationUtil::retiraPontuacoes($cliente->rg_ie) : null;
        $nfcomMake->tagDest($stdDest);

        $stdEnderDest = new \stdClass;
        $stdEnderDest->xLgr = FormatationUtil::retiraAcentos($cliente->endereco->rua);
        $stdEnderDest->nro = $cliente->endereco->numero;
        $stdEnderDest->xBairro = FormatationUtil::retiraAcentos($cliente->endereco->bairro);
        $stdEnderDest->cMun = FormatationUtil::retiraPontuacoes($cliente->endereco->codigoIBGE);
        $stdEnderDest->xMun = FormatationUtil::retiraAcentos($cliente->endereco->cidade);
        $stdEnderDest->CEP = FormatationUtil::retiraPontuacoes($cliente->endereco->cep);
        $stdEnderDest->UF = $cliente->endereco->uf;
        // A lib nfephp-org/sped-nfcom referencia cPais/xPais em tagEnderDest sem
        // declará-los no array $possible do método - definir aqui evita o
        // "Undefined property" resultante desse descuido da própria lib.
        $stdEnderDest->cPais = '1058';
        $stdEnderDest->xPais = 'Brasil';
        $nfcomMake->tagEnderDest($stdEnderDest);

        // assinante - dados de contrato de telecom
        $stdAssinante = new \stdClass;
        $stdAssinante->iCodAssinante = $nfcom->iCodAssinante;
        $stdAssinante->tpAssinante = $nfcom->tpAssinante;
        $stdAssinante->tpServUtil = $nfcom->tpServUtil;
        $stdAssinante->nContrato = $nfcom->nContrato;
        $nfcomMake->tagAssinante($stdAssinante);

        // itens de serviço faturados
        // Simples Nacional (CRT 1 ou 4) usa a tag ICMSSN (CSOSN), não ICMS (CST) - mesma
        // convenção já usada em outros Services do projeto para decidir o regime.
        $isSimples = in_array((int) $emitente->crt, [1, 4]);
        $vProd = $vDesc = $vOutro = $vPIS = $vCOFINS = $vFUST = $vFUNTTEL = 0;
        $vBCIcms = $vICMS = $vFCP = 0;
        foreach ($nfcom->itens as $key => $item) {
            $n = $key + 1;

            $stdDet = new \stdClass;
            $stdDet->item = $n;
            $nfcomMake->tagDet($stdDet);

            $stdProd = new \stdClass;
            $stdProd->item = $n;
            $stdProd->cProd = $item->cProd;
            $stdProd->xProd = FormatationUtil::retiraAcentos($item->xProd);
            $stdProd->cClass = $item->cClass;
            $stdProd->CFOP = $item->cfop;
            $stdProd->uMed = $item->uMed;
            $stdProd->qFaturada = $item->qFaturada;
            $stdProd->vItem = $item->vItem;
            $stdProd->vDesc = $item->vDesc;
            $stdProd->vOutro = $item->vOutro;
            $stdProd->vProd = $item->vProd;
            $nfcomMake->tagProd($stdProd);

            if ($isSimples) {
                $stdICMS = new \stdClass;
                $stdICMS->item = $n;
                // A lib lê $std->orig/$std->CSOSN (não CST/indSN, apesar do que o $possible
                // de tagICMSSN sugere) - orig vira a tag <CST>, CSOSN vira a tag <indSN>.
                $stdICMS->orig = $item->icms_orig ?? '0';
                $stdICMS->CSOSN = $item->icms_csosn ?? '102';
                $nfcomMake->tagICMSSN($stdICMS);
            } else {
                $stdICMS = new \stdClass;
                $stdICMS->item = $n;
                $stdICMS->CST = $item->icms_cst ?? '00';
                $stdICMS->vBC = $item->icms_vBC;
                $stdICMS->pICMS = $item->icms_pICMS;
                $stdICMS->vICMS = $item->icms_vICMS;
                $stdICMS->pFCP = $item->icms_pFCP;
                $stdICMS->vFCP = $item->icms_vFCP;
                $nfcomMake->tagICMS($stdICMS);
            }

            $stdPIS = new \stdClass;
            $stdPIS->item = $n;
            $stdPIS->CST = $item->pis_cst;
            $stdPIS->vBC = $item->pis_vBC;
            $stdPIS->pPIS = $item->pis_pPIS;
            $stdPIS->vPIS = $item->pis_vPIS;
            $nfcomMake->tagPIS($stdPIS);

            $stdCOFINS = new \stdClass;
            $stdCOFINS->item = $n;
            $stdCOFINS->CST = $item->cofins_cst;
            $stdCOFINS->vBC = $item->cofins_vBC;
            $stdCOFINS->pCOFINS = $item->cofins_pCOFINS;
            $stdCOFINS->vCOFINS = $item->cofins_vCOFINS;
            $nfcomMake->tagCOFINS($stdCOFINS);

            if (! empty($item->fust_vFUST)) {
                $stdFust = new \stdClass;
                $stdFust->item = $n;
                $stdFust->vBC = $item->fust_vBC;
                $stdFust->pFUST = $item->fust_pFUST;
                $stdFust->vFUST = $item->fust_vFUST;
                $nfcomMake->tagFUST($stdFust);
            }

            if (! empty($item->funttel_vFUNTTEL)) {
                $stdFunttel = new \stdClass;
                $stdFunttel->item = $n;
                $stdFunttel->vBC = $item->funttel_vBC;
                $stdFunttel->pFUNTTEL = $item->funttel_pFUNTTEL;
                $stdFunttel->vFUNTTEL = $item->funttel_vFUNTTEL;
                $nfcomMake->tagFUNTTEL($stdFunttel);
            }

            $vProd += $item->vProd;
            $vDesc += $item->vDesc;
            $vOutro += $item->vOutro;
            $vPIS += $item->pis_vPIS;
            $vCOFINS += $item->cofins_vCOFINS;
            $vFUST += $item->fust_vFUST;
            $vFUNTTEL += $item->funttel_vFUNTTEL;
            $vBCIcms += $item->icms_vBC;
            $vICMS += $item->icms_vICMS;
            $vFCP += $item->icms_vFCP;
        }

        // totais
        $stdTotal = new \stdClass;
        $stdTotal->vProd = $vProd;
        $stdTotal->vCOFINS = $vCOFINS;
        $stdTotal->vPIS = $vPIS;
        $stdTotal->vFUNTTEL = $vFUNTTEL;
        $stdTotal->vFUST = $vFUST;
        $stdTotal->vDesc = $vDesc;
        $stdTotal->vOutro = $vOutro;
        $stdTotal->vNF = $nfcom->vNF;
        $nfcomMake->tagTotal($stdTotal);

        $stdICMSTot = new \stdClass;
        $stdICMSTot->vBC = $vBCIcms;
        $stdICMSTot->vICMS = $vICMS;
        $stdICMSTot->vICMSDeson = 0;
        $stdICMSTot->vFCP = $vFCP;
        $nfcomMake->tagICMSTot($stdICMSTot);

        // gFat - faturamento
        $stdGFat = new \stdClass;
        $stdGFat->CompetFat = $nfcom->competFat;
        $stdGFat->dVencFat = optional($nfcom->dVencFat)->format('Y-m-d');
        $stdGFat->dPerUsoIni = optional($nfcom->dPerUsoIni)->format('Y-m-d');
        $stdGFat->dPerUsoFim = optional($nfcom->dPerUsoFim)->format('Y-m-d');
        $stdGFat->codBarras = $nfcom->codBarras;
        $nfcomMake->tagGFat($stdGFat);

        // gRespTec - dados do responsável técnico pelo sistema emissor
        // idCSRT/hashCSRT exigem o Código de Segurança do Responsável Técnico
        // obtido junto à SEFAZ especificamente para NFCom - configurar via .env
        // (NFCOM_RESPTEC_ID_CSRT / NFCOM_RESPTEC_CSRT) antes de transmitir em produção.
        $stdRespTec = new \stdClass;
        $stdRespTec->CNPJ = env('RESPTEC_CNPJ', '42879649000174');
        $stdRespTec->xContato = env('RESPTEC_CONTATO', 'FSOFT SISTEMAS');
        $stdRespTec->email = env('RESPTEC_EMAIL', 'fsoftsistemas@gmail.com');
        $stdRespTec->fone = env('RESPTEC_FONE', '87981753993');
        $stdRespTec->idCSRT = env('NFCOM_RESPTEC_ID_CSRT', '1');
        $stdRespTec->hashCSRT = env('NFCOM_RESPTEC_CSRT', 'PENDENTE-CONFIGURAR-CSRT-NFCOM');
        // hashCSRT() da lib referencia $this->chNFe (typo interno, nunca declarado
        // na classe Make - o correto seria chNFCom) e emite "Undefined property"
        // sem isso. Declarar a propriedade dinamicamente evita o aviso.
        $nfcomMake->chNFe = $nfcomMake->getChave();
        $nfcomMake->tagGResptec($stdRespTec);

        try {
            $xml = $nfcomMake->getXML();

            return [
                'xml' => $xml,
                'chave' => $nfcomMake->getChave(),
                'nNF' => $stdIde->nNF,
            ];
        } catch (Exception $e) {
            return [
                'erros_xml' => $nfcomMake->getErrors(),
            ];
        }
    }

    public function sign($xml)
    {
        return $this->tools->signNFCom($xml);
    }

    /**
     * Envio síncrono (sem lote - a NFCom não possui envio em lote).
     */
    public function transmitir($signedXml)
    {
        try {
            $resp = $this->tools->sefazEnvia($signedXml);
            $std = (new Standardize)->toStd($resp);
            $cStat = $std->protNFCom->infProt->cStat ?? null;
            if ($cStat != 100) {
                return [
                    'erro' => "[$cStat] - ".($std->protNFCom->infProt->xMotivo ?? 'Rejeitado pela SEFAZ'),
                ];
            }
            $xmlAutorizado = Complements::toAuthorize($signedXml, $resp);

            return [
                'sucesso' => true,
                'xml' => $xmlAutorizado,
                'nProt' => $std->protNFCom->infProt->nProt,
            ];
        } catch (Exception $e) {
            return [
                'erro' => $e->getMessage(),
            ];
        }
    }

    public function cancelar($nfcom, $justificativa)
    {
        try {
            if (empty($nfcom->nProtocolo)) {
                $resp = $this->tools->sefazConsultaChave($nfcom->chave);
                $std = (new Standardize)->toStd($resp);
                $nfcom->nProtocolo = $std->protNFCom->infProt->nProt ?? null;
            }
            $resp = $this->tools->sefazCancela($nfcom->chave, $justificativa, $nfcom->nProtocolo);
            $std = (new Standardize)->toStd($resp);
            $cStat = $std->infEvento->cStat ?? null;
            if (! in_array($cStat, [135, 136, 155])) {
                return [
                    'erro' => "[$cStat] - ".($std->infEvento->xMotivo ?? 'Rejeitado pela SEFAZ'),
                ];
            }

            return [
                'sucesso' => true,
                'nProt' => $std->infEvento->nProt ?? null,
            ];
        } catch (Exception $e) {
            return [
                'erro' => $e->getMessage(),
            ];
        }
    }

    public function consultar($chave)
    {
        return $this->tools->sefazConsultaChave($chave);
    }

    public function statusServico()
    {
        return $this->tools->sefazStatus();
    }
}
