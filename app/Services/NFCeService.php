<?php

namespace App\Services;

use App\Enums\EstadoEnum;
use App\Models\NFCe;
use App\Utils\FormatationUtil;
use Illuminate\Support\Facades\File;
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

    public static function createNFCe($body, $couponId, $company)
    {
        return NFCe::create([
            'nro' => $company->ultimaNFCe,
            'serie' => $company->serie,
            'chave' => $body['chave'],
            'contingencia' => 'n sei dizer',
            'situacao' => EstadoEnum::AUTORIZADO,
            'xml' => $body['xml'],
            'cupom_id' => $couponId,
            'empresa_id' => $company->id
        ]);
    }

    public function generateXml($cupom, $emitente)
    {
        try {
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
            $std->nNF = $emitente->ultimaNFCe;
            $std->dhEmi = date("Y-m-d\TH:i:sP");;
            $std->dhSaiEnt = date("Y-m-d\TH:i:sP");;
            $std->tpNF = 1;
            $std->idDest = $emitente->endereco->uf == $cupom->cliente->endereco->uf ? 1 : 2;
            $std->cMunFG = $emitente->endereco->codigoIBGE;
            $std->tpImp = 1;
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
            if (strlen($emitente->cpf_cnpj) > 11) {
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

            $std = new \stdClass();
            $std->xNome = FormatationUtil::retiraAcentos($cupom->cliente->nome);
            if (strlen($cupom->cliente->cpf_cnpj) > 11) {
                $std->CNPJ = FormatationUtil::retiraPontuacoes($cupom->cliente->cpf_cnpj);
                $std->IE = FormatationUtil::retiraPontuacoes($cupom->cliente->rg_ie);
            } else {
                $std->CPF = FormatationUtil::retiraPontuacoes($cupom->cliente->cpf_cnpj);
                $ie = FormatationUtil::retiraPontuacoes($cupom->cliente->rg_ie);
                if (strtolower($ie) != "isento" && $cupom->cliente->contribuinte) {
                    $std->IE = $ie;
                }
            }
            if ($cupom->cliente->contribuinte) {
                if ($cupom->cliente->rg_ie == 'ISENTO') {
                    $std->indIEDest = 2;
                } else {
                    $std->indIEDest = 1;
                }
            } else {
                $std->indIEDest = 9;
            }
            $make->tagdest($std);

            $std = new \stdClass();
            $std->xLgr = FormatationUtil::retiraAcentos($cupom->cliente->endereco->rua);
            $std->nro = FormatationUtil::retiraAcentos($cupom->cliente->endereco->numero);
            $std->xCpl = FormatationUtil::retiraAcentos($cupom->cliente->endereco->complemento);
            $std->xBairro = FormatationUtil::retiraAcentos($cupom->cliente->endereco->bairro);
            $std->cMun = $cupom->cliente->endereco->codigoIBGE;
            $std->xMun = FormatationUtil::retiraAcentos($cupom->cliente->endereco->cidade);
            $std->UF = $cupom->cliente->endereco->uf;
            $std->CEP = FormatationUtil::retiraPontuacoes($cupom->cliente->endereco->cep);
            $std->cPais = 1058;
            $std->xPais = 'Brasil';
            $std->fone = FormatationUtil::retiraPontuacoes($cupom->cliente->celular);
            $make->tagenderdest($std);

            dd($cupom);
            foreach ($cupom->itens as $index => $item) {
                $std = new \stdClass();
                $std->item = $index + 1;
                $std->cProd = $item->produto->id;
                $std->cEAN = FormatationUtil::retiraPontuacoes($item->produto->codigo);
                $std->xProd = FormatationUtil::retiraAcentos($item->produto->produto);
                $std->NCM = FormatationUtil::retiraPontuacoes($item->produto->ncm);
                $std->EXTIPI = '';
                $std->CFOP = $item->produto->cfop_interno;
                $std->uCom = $item->produto->un;
                $std->qCom = $item->qtde;
                $std->vUnCom = FormatationUtil::format($item->unitario);
                $std->vProd = FormatationUtil::format($item->qtde * $item->unitario);
                $std->cEANTrib = FormatationUtil::retiraPontuacoes($item->produto->codigo);
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
                $std->vBC = FormatationUtil::format($item->produto->pis) > 0 ? $std->vProd : 0.00;
                $std->pPIS = FormatationUtil::format($item->produto->pis);
                $std->vPIS = FormatationUtil::format(($std->vProd) * ($item->produto->pis / 100));
                $std->qBCProd = 0;
                $std->vAliqProd = 0;
                $make->tagPIS($std);

                $std = new \stdClass();
                $std->item = $index + 1;
                $std->CST = $item->produto->cst_cofins;
                $std->vBC = FormatationUtil::format($item->produto->cofins) > 0 ? $std->vProd : 0.00;
                $std->pCOFINS = FormatationUtil::format($item->produto->cofins);
                $std->vCOFINS = FormatationUtil::format(($std->vProd) *
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

            $std = new \stdClass();
            $std->indPag = 1;
            $std->tPag = '01';
            $std->vPag = 100.00;
            $detpag = $make->tagdetpag($std);

            //infadic
            $std = new \stdClass();
            $std->infAdFisco = '';
            $std->infCpl = '';
            $info = $make->taginfadic($std);

            $std = new \stdClass();
            $std->CNPJ = '99999999999999'; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
            $std->xContato = 'Fulano de Tal'; //Nome da pessoa a ser contatada
            $std->email = 'fulano@soft.com.br'; //E-mail da pessoa jurídica a ser contatada
            $std->fone = '1155551122'; //Telefone da pessoa jurídica/física a ser contatada
            //$std->CSRT = 'G8063VRTNDMO886SFNK5LDUDEI24XJ22YIPO'; //Código de Segurança do Responsável Técnico
            //$std->idCSRT = '01'; //Identificador do CSRT
            $make->taginfRespTec($std);

            $make->monta();
            $xml = $make->getXML();
            $signedXml = $this->sign($xml);
            $key = $make->getChave();
            $this->toTransmit($signedXml);
            $arr = [
                'chave' => $key,
                'xml' => $signedXml,
            ];
            return $arr;
        } catch (\Exception $e) {
            dd($e);
            return redirect('/vendas')->with('error', $make->getErrors());
        }
    }

    private function sign($xml)
    {
        return $this->tools->signNFe($xml);
    }

    private function toTransmit($signedXml)
    {
        $loteId = str_pad(100, 15, '0', STR_PAD_LEFT);
        $resp = $this->tools->sefazEnviaLote([$signedXml], $loteId);
        $st = new Standardize();
        $std = $st->toStd($resp);
        if ($std->cStat != 103) {
            return [
                'erro' => "[$std->cStat] - $std->xMotivo",
            ];
        }
        $std->infRec->nRec;
        sleep(2);
        return $resp;
    }
}
