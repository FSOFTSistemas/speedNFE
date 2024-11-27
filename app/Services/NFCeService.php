<?php

namespace App\Services;

use App\Enums\EstadoEnum;
use App\Exceptions\AlreadyExistException;
use App\Exceptions\LimitExceededException;
use App\Exceptions\MalformedXmlException;
use App\Exceptions\TimeExceededException;
use App\Models\NFCe;
use App\Utils\FormatationUtil;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;

class NFCeService
{
    private $tools;

    public function __construct($config, $emitente)
    {
        $certificado = file_get_contents('../storage/app/public/certificados/' . $emitente->razao . '.pfx');
        $this->tools = new Tools(json_encode($config), Certificate::readPfx($certificado, $emitente->senhaCertificado));
        $this->tools->model('65');
    }

    public static function getCompanyNFCes($empresaId)
    {
        return NFCe::whereEmpresaId($empresaId)->get();
    }

    public static function getNFCe($nfceId)
    {
        return NFCe::find($nfceId);
    }

    public static function getMonthlyCompanyXmls($companyId, $month)
    {
        return NFCe::select('xml', 'chave')->whereEmpresaId($companyId)->where('data', 'like', $month . '%')->get();
    }

    public static function getTotalNFCePerMonth($companyId)
    {
        if ($companyId == 1) {
            $companyId = '%';
            $results = NFCe::selectRaw('MONTH(n_f_ces.data) as mes, SUM(cupoms.total) as total_vendas')
                ->join('cupoms', 'n_f_ces.cupom_id', 'cupoms.id')
                ->where('n_f_ces.empresa_id', 'like', $companyId)
                ->where('n_f_ces.situacao', 'Autorizado')
                ->groupBy('mes')
                ->get();
        } else {
            $results = NFCe::selectRaw('MONTH(n_f_ces.data) as mes, SUM(cupoms.total) as total_vendas')
                ->join('cupoms', 'n_f_ces.cupom_id', 'cupoms.id')
                ->where('n_f_ces.empresa_id', $companyId)
                ->where('n_f_ces.situacao', 'Autorizado')
                ->groupBy('mes')
                ->get();
        }
        return $results;
    }

    public static function createNFCe($body, $couponId, $company)
    {
        return NFCe::create([
            'nro' => $company->ultimaNFCe,
            'serie' => $company->serie,
            'chave' => $body['chave'],
            'contingencia' => false,
            'situacao' => EstadoEnum::AUTORIZADO,
            'xml' => $body['xml'],
            'cupom_id' => $couponId,
            'empresa_id' => $company->id
        ]);
    }

    public function generateXml($cupom, $emitente)
    {
        try {
            if (count($emitente->nfces) >= $emitente->limNFCes && $emitente->id != 1) {
                throw new LimitExceededException("O limite de notas NFCe foi atingido!");
            }
            $make = new Make();

            $std = new \stdClass();
            $std->Id = '';
            $std->versao = '4.00';
            $make->taginfNFe($std);

            $std = new \stdClass();
            $std->cUF = $emitente::getCUF($emitente->endereco->uf);
            $std->cNF = rand(11111, 99999);
            $std->natOp = 'VENDA CONSUMIDOR';
            $std->mod = 65;
            $std->serie = $emitente->serie;
            $std->nNF = $emitente->ultimaNFCe + 1;
            $std->dhEmi = date("Y-m-d\TH:i:sP");;
            $std->dhSaiEnt = date("Y-m-d\TH:i:sP");;
            $std->tpNF = 1;
            $cliente = $cupom->cliente ?? null;
            $clientAddress = $cliente->endereco ?? null;
            $std->idDest = ($clientAddress && $emitente->endereco->uf == $clientAddress->uf) || !$clientAddress ? 1 : 2;
            $std->cMunFG = $emitente->endereco->codigoIBGE;
            $std->tpImp = 4;
            $std->indSinc = 0;
            $std->tpEmis = 1;
            $std->cDV = 0;
            $std->tpAmb = $emitente->ambiente;
            $std->finNFe = 1;
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = '4.13';
            $std->dhCont = null;
            $std->xJust = null;
            $make->tagIde($std);

            $std = new \stdClass();
            $std->xNome = $emitente->razao;
            $std->xFant = $emitente->fantasia;
            $std->IE = FormatationUtil::retiraPontuacoes($emitente->rg_ie);
            $std->IEST = null;
            $std->CRT = 1; //Simples Nacional
            if (strlen(FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj)) >= 12) {
                $std->CNPJ = FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj);
            } else {
                $std->CPF = FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj);
            }
            $make->tagemit($std);

            $std = new \stdClass();
            $std->xLgr = FormatationUtil::retiraAcentos($emitente->endereco->rua);
            $std->nro = $emitente->endereco->numero;
            $std->xCpl = FormatationUtil::retiraAcentos($emitente->endereco->complemento);
            $std->xBairro = FormatationUtil::retiraAcentos($emitente->endereco->bairro);
            $std->cMun = $emitente->endereco->codigoIBGE;
            $std->xMun = FormatationUtil::retiraAcentos($emitente->endereco->cidade);
            $std->UF = $emitente->endereco->uf;
            $std->CEP = FormatationUtil::retiraPontuacoes($emitente->endereco->cep);
            $std->cPais = 1058;
            $std->xPais = 'Brasil';
            $std->fone = FormatationUtil::retiraPontuacoes($emitente->celular);
            $make->tagenderemit($std);

            if ($cliente) {
                $std = new \stdClass();
                $std->xNome = FormatationUtil::retiraAcentos($cliente->nome);
                if (strlen(FormatationUtil::retiraPontuacoes($cliente->cpf_cnpj)) >= 12) {
                    $std->CNPJ = FormatationUtil::retiraPontuacoes($cliente->cpf_cnpj);
                    // $std->IE = FormatationUtil::retiraPontuacoes($cliente->rg_ie);
                } else {
                    $std->CPF = FormatationUtil::retiraPontuacoes($cliente->cpf_cnpj);
                    // $ie = FormatationUtil::retiraPontuacoes($cliente->rg_ie);
                    // if (strtolower($ie) != "isento" && $cliente->contribuinte) {
                    //     $std->IE = $ie;
                    // }
                }
                if ($cliente->contribuinte) {
                    if ($cliente->rg_ie == 'ISENTO') {
                        $std->indIEDest = 2;
                    } else {
                        $std->indIEDest = 1;
                    }
                } else {
                    $std->indIEDest = 9;
                }
                $make->tagdest($std);

                $std = new \stdClass();
                $std->xLgr = FormatationUtil::retiraAcentos($cliente->endereco->rua);
                $std->nro = FormatationUtil::retiraAcentos($cliente->endereco->numero);
                $std->xCpl = FormatationUtil::retiraAcentos($cliente->endereco->complemento);
                $std->xBairro = FormatationUtil::retiraAcentos($cliente->endereco->bairro);
                $std->cMun = $cliente->endereco->codigoIBGE;
                $std->xMun = FormatationUtil::retiraAcentos($cliente->endereco->cidade);
                $std->UF = $cliente->endereco->uf;
                $std->CEP = FormatationUtil::retiraPontuacoes($cliente->endereco->cep);
                $std->cPais = 1058;
                $std->xPais = 'Brasil';
                $std->fone = FormatationUtil::retiraPontuacoes($cliente->celular);
                $make->tagenderdest($std);
                
            }

            foreach ($cupom->itens as $index => $item) {
                $std = new \stdClass();
                $std->item = $index + 1;
                $std->cProd = $item->produto->id;
                $std->cEAN = $item->produto->codigo != "SEM GTIN" && strlen($item->produto->codigo) >= 8 ? FormatationUtil::retiraPontuacoes($item->produto->codigo) : 9780000000002;
                $std->xProd = FormatationUtil::retiraAcentos($item->produto->produto);
                $std->NCM = FormatationUtil::retiraPontuacoes($item->produto->ncm);
                $std->EXTIPI = '';
                $std->CFOP = $item->produto->cfop_interno;
                $std->uCom = $item->produto->un;
                $std->qCom = $item->qtde;
                $std->vUnCom = FormatationUtil::format($item->unitario);
                $vProd = FormatationUtil::format($item->qtde * $item->unitario);
                $std->vProd = $vProd;
                $std->cEANTrib = $item->produto->codigo != "SEM GTIN" && strlen($item->produto->codigo) >= 8 ? FormatationUtil::retiraPontuacoes($item->produto->codigo) : 9780000000002;
                $std->uTrib = $item->produto->un;
                $std->qTrib = $item->qtde;
                $std->vUnTrib = FormatationUtil::format($item->unitario);
                $std->indTot = 1;
                $make->tagprod($std);

                $tag = new \stdClass();
                $tag->item = $index + 1;
                $tag->infAdProd = FormatationUtil::retiraAcentos($item->produto->produto);
                $make->taginfAdProd($tag);

                $std = new \stdClass();
                $std->item = $index + 1;
                $std->vTotTrib = 0.00;
                $make->tagimposto($std);

                $std = new \stdClass();
                $std->item = $index + 1;
                $std->orig = 0;
                $std->CSOSN = $item->produto->cst_csosn;
                $std->pCredSN = 0.00;
                $std->vCredICMSSN = 0.00;
                $std->modBCST = null;
                $std->pMVAST = null;
                $std->pRedBCST = null;
                $std->vBCST = null;
                $std->pICMSST = null;
                $std->vICMSST = null;
                $std->vBCFCPST = null;
                $std->pFCPST = null;
                $std->vFCPST = null;
                $std->vBCSTRet = null;
                $std->pST = null;
                $std->vICMSSTRet = null;
                $std->vBCFCPSTRet = null;
                $std->pFCPSTRet = null;
                $std->vFCPSTRet = null;
                $std->modBC = null;
                $std->vBC = null;
                $std->pRedBC = null;
                $std->pICMS = null;
                $std->vICMS = null;
                $std->pRedBCEfet = null;
                $std->vBCEfet = null;
                $std->pICMSEfet = null;
                $std->vICMSEfet = null;
                $std->vICMSSubstituto = null;
                $make->tagICMSSN($std);
                $std = new \stdClass();
                $std->item = $index + 1;
                $std->CST = $item->produto->cst_pis;
                $std->vBC = FormatationUtil::format($item->produto->pis) > 0 ? $vProd : 0.00;
                $std->pPIS = FormatationUtil::format($item->produto->pis);
                $std->vPIS = FormatationUtil::format(($vProd) * ($item->produto->pis / 100));
                $std->qBCProd = 0;
                $std->vAliqProd = 0;
                $make->tagPIS($std);

                $std = new \stdClass();
                $std->item = $index + 1;
                $std->CST = $item->produto->cst_cofins;
                $std->vBC = FormatationUtil::format($item->produto->cofins) > 0 ? $vProd : 0.00;
                $std->pCOFINS = FormatationUtil::format($item->produto->cofins);
                $std->vCOFINS = FormatationUtil::format(($vProd) *
                    ($item->produto->cofins / 100));
                $std->qBCProd = 0;
                $std->vAliqProd = 0;
                $make->tagCOFINS($std);
            }

            $std = new \stdClass();
            $std->vProd = 0.00;
            $std->vBC = 0.00;
            $std->vICMS = 0.00;
            $std->vICMSDeson = 0.00;
            $std->vBCST = 0.00;
            $std->vST = 0.00;
            $std->vFrete = 0.00;
            $std->vSeg = 0.00;
            $std->vDesc = FormatationUtil::format($cupom->desconto);
            $std->vII = 0.00;
            $std->vIPI = 0.00;
            $std->vPIS = 0.00;
            $std->vCOFINS = 0.00;
            $std->vOutro = 0.00;
            $std->vTotTrib = 0.00;
            $std->vNF = FormatationUtil::format($cupom->total);
            $make->tagicmstot($std);

            $std = new \stdClass();
            $std->modFrete = 9;
            $make->tagtransp($std);

            $std = new \stdClass();
            $std->vTroco = FormatationUtil::format($cupom->troco);
            $make->tagpag($std);

            foreach ($cupom->formasPagamento as $item) {
                $card = false;
                $std = new \stdClass();
                switch ($item->forma) {
                    case 'DINHEIRO':
                        $std->tPag = '01';
                        break;
                    case 'CARTÃO/CRÉDITO':
                        $std->tPag = '03';
                        $card = true;
                        break;
                    case 'CARTÃO/DÉBITO':
                        $std->tPag = '04';
                        $card = true;
                        break;
                    case 'PIX':
                        $std->tPag = '17';
                        $card = true;
                        break;
                    case 'OUTROS':
                        $std->tPag = '99';
                        break;
                    default:
                        throw new \Exception("Forma de pagamento inválida: $item->forma");
                }
                $std->vPag = FormatationUtil::format($item->valor);
                if ($card) {
                    $std->tpIntegra = 2;
                    $std->tBand = '05';
                }
                $make->tagdetpag($std);
            }

            $std = new \stdClass();
            $std->infAdFisco = '';
            $std->infCpl = '';
            $make->taginfadic($std);

            $std = new \stdClass();
            $std->CNPJ = getenv('RESP_CNPJ');
            $std->xContato = getenv('RESP_NOME');
            $std->email = getenv('RESP_EMAIL');
            $std->fone = getenv('RESP_FONE');
            $make->taginfRespTec($std);

            try {
                $make->monta();
                $xml = $make->getXML();
            } catch (\Exception $e) {
                dd($e->getMessage(), $make->getErrors());
            }
            $signedXml = $this->sign($xml);
            $key = $make->getChave();
            $this->toTransmit($signedXml);
            $arr = [
                'chave' => $key,
                'xml' => $signedXml,
            ];
            return $arr;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function sign($xml)
    {
        return $this->tools->signNFe($xml);
    }

    private function toTransmit($signedXml)
    {
        $loteId = str_pad(100, 15, '0', STR_PAD_LEFT);
        $resp = $this->tools->sefazEnviaLote([$signedXml], $loteId, 1);
        $st = new Standardize();
        $std = $st->toStd($resp);
        sleep(2);
        if ($std->cStat == 104) {
            if ($std->protNFe->infProt->cStat == 100) {
                return true;
            } else {
                throw new MalformedXmlException($std->protNFe->infProt->xMotivo);
            }
        } else {
            throw new MalformedXmlException($std->xMotivo);
        }
    }

    public function cancel($key, $xJust)
    {
        $response = $this->tools->sefazConsultaChave($key);
        $std = new Standardize($response);
        $std = $std->toStd();
        $nProt = $std->protNFe->infProt->nProt;
        $response = $this->tools->sefazCancela($key, $xJust, $nProt);
        $std = new Standardize($response);
        $std = $std->toStd();
        if ($std->cStat == 128) {
            $cStat = $std->retEvento->infEvento->cStat;
            if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                return $std;
            } elseif ($cStat == '573') {
                throw new AlreadyExistException($std->retEvento->infEvento->xMotivo);
            } else {
                throw new TimeExceededException($std->retEvento->infEvento->xMotivo);
            }
        }
    }

    public function unuse($nSerie, $numI, $numF, $xJust)
    {
        $response = $this->tools->sefazInutiliza($nSerie, $numI, $numF, $xJust);
        $stdCl = new Standardize($response);
        $std = $stdCl->toStd($response);
        if ($std->infInut->cStat == 102) {
            return Complements::toAuthorize($this->tools->lastRequest, $response);
        } else {
            throw new AlreadyExistException($std->infInut->xMotivo);
        }
    }
}
