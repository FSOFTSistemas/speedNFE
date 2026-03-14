<?php

namespace App\Services;

use App\Utils\FormatationUtil;
use App\Utils\ValidationEAN13Util;
use Illuminate\Support\Facades\File;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

class NFeService
{

    private $tools;

    public function __construct($config, $emitente)
    {
        $certificado = file_get_contents('../storage/app/public/certificados/' . $emitente->razao . '.pfx');
        $this->tools = new Tools(json_encode($config), Certificate::readPfx($certificado, $emitente->senhaCertificado));
    }

public function gerarXml($venda, $emitente)
    {
        // 1. CORREÇÃO CRÍTICA: Forçar Schema PL_010 para ativar a Reforma Tributária
        $nfe = new Make('PL_010');

        $stdInNFe = new \stdClass();
        $stdInNFe->versao = '4.00';
        $stdInNFe->Id = null;
        $stdInNFe->pk_nItem = '';
        $infNFe = $nfe->taginfNFe($stdInNFe);

        $numeroNFe = $emitente->ultimaNFe + 1;
        $stdIde = new \stdClass();
        $stdIde->cUF = \App\Models\Empresa::getCUF($emitente->endereco->uf);
        $stdIde->cNF = rand(11111, 99999);
        $stdIde->natOp = $venda->cfopNota->natureza;

        $stdIde->mod = 55;
        $stdIde->serie = $emitente->serie;
        $stdIde->nNF = (int) $numeroNFe;
        $stdIde->dhEmi = date("Y-m-d\TH:i:sP");
        $stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
        $stdIde->tpNF = $venda->tpNF;

        $stdIde->idDest = $emitente->endereco->uf != $venda->endereco_cliente->uf ? 2 : 1;
        $stdIde->cMunFG = $emitente->endereco->codigoIBGE;
        $stdIde->tpImp = 1;
        $stdIde->tpEmis = 1;
        $stdIde->cDV = 0;
        $stdIde->tpAmb = $emitente->ambiente;
        $stdIde->finNFe = $venda->finNF;
        $stdIde->indFinal = 1;
        $stdIde->indPres = 1;
        $stdIde->procEmi = '0';
        $stdIde->verProc = '3.10.31';
        $tagide = $nfe->tagide($stdIde);

        // --- CONTROLE DA REFORMA TRIBUTÁRIA (RTC) ---
        $dataEmissao = new \DateTime(date("Y-m-d"));
        $dataVirada  = new \DateTime('2026-01-01');
        $isRTC = ($dataEmissao >= $dataVirada) || (getenv('TESTAR_RTC') == 'true' && $emitente->ambiente == 2);
        // --------------------------------------------

        if ($venda->ref_nfe) {
            $stdrefNFe = new \stdClass();
            $stdrefNFe->refNFe = $venda->ref_nfe;
            $nfe->tagrefNFe($stdrefNFe);
        }

        //TAG EMITENTE
        $stdEmit = new \stdClass();
        $stdEmit->xNome = $emitente->razao;
        $stdEmit->xFant = $emitente->fantasia;

        $ie = str_replace(".", "", $emitente->rg_ie);
        $ie = str_replace("/", "", $ie);
        $ie = str_replace("-", "", $ie);
        $stdEmit->IE = $ie;

        // AJUSTE: CRT DINÂMICO DA EMPRESA
        $stdEmit->CRT = $emitente->crt;

        $cnpj = str_replace(".", "", $emitente->cpf_cnpj);
        $cnpj = str_replace("/", "", $cnpj);
        $cnpj = str_replace("-", "", $cnpj);
        $cnpj = str_replace(" ", "", $cnpj);

        if (strlen($cnpj) == 14) {
            $stdEmit->CNPJ = $cnpj;
        } else {
            $stdEmit->CPF = $cnpj;
        }
        $emit = $nfe->tagemit($stdEmit);

        // ENDERECO EMITENTE
        $stdEnderEmit = new \stdClass();
        $stdEnderEmit->xLgr = FormatationUtil::retiraAcentos($emitente->endereco->rua);
        $stdEnderEmit->nro = $emitente->endereco->numero;
        $stdEnderEmit->xCpl = FormatationUtil::retiraAcentos($emitente->endereco->complemento);

        $stdEnderEmit->xBairro = FormatationUtil::retiraAcentos($emitente->endereco->bairro);
        $stdEnderEmit->cMun = $emitente->endereco->codigoIBGE;
        $stdEnderEmit->xMun = FormatationUtil::retiraAcentos($emitente->endereco->cidade);
        $stdEnderEmit->UF = $emitente->endereco->uf;

        $telefone = $emitente->celular;
        $telefone = str_replace("(", "", $telefone);
        $telefone = str_replace(")", "", $telefone);
        $telefone = str_replace("-", "", $telefone);
        $telefone = str_replace(" ", "", $telefone);
        $stdEnderEmit->fone = $telefone;

        $cep = str_replace("-", "", $emitente->endereco->cep);
        $cep = str_replace(".", "", $cep);
        $stdEnderEmit->CEP = $cep;
        $stdEnderEmit->cPais = '1058';
        $stdEnderEmit->xPais = 'BRASIL';

        $enderEmit = $nfe->tagenderEmit($stdEnderEmit);

        // DESTINATARIO
        $stdDest = new \stdClass();
        $stdDest->xNome = FormatationUtil::retiraAcentos($venda->cliente->nome);

        if ($venda->cliente->contribuinte) {
            $stdDest->indIEDest = ($venda->cliente->rg_ie == 'ISENTO') ? "2" : "1";
        } else {
            $stdDest->indIEDest = "9";
        }

        $cnpj_cpf = str_replace([".", "/", "-"], "", $venda->cliente->cpf_cnpj);

        if (strlen($cnpj_cpf) == 14) {
            $stdDest->CNPJ = $cnpj_cpf;
            $stdDest->IE = str_replace([".", "/", "-"], "", $venda->cliente->rg_ie);
        } else {
            $stdDest->CPF = $cnpj_cpf;
            $ie = str_replace([".", "/", "-"], "", $venda->cliente->rg_ie);
            if (strtolower($ie) != "isento" && $venda->cliente->contribuinte) {
                $stdDest->IE = $ie;
            }
        }

        $dest = $nfe->tagdest($stdDest);

        //ENDEREÇO DESTINATÁRIO
        $stdEnderDest = new \stdClass();
        $stdEnderDest->xLgr = FormatationUtil::retiraAcentos($venda->endereco_cliente->rua);
        $stdEnderDest->nro = FormatationUtil::retiraAcentos($venda->endereco_cliente->numero);
        $stdEnderDest->xCpl = FormatationUtil::retiraAcentos($venda->endereco_cliente->complemento);
        $stdEnderDest->xBairro = FormatationUtil::retiraAcentos($venda->endereco_cliente->bairro);

        $telefone = str_replace(["(", ")", "-", " "], "", $venda->cliente->celular);
        $stdEnderDest->fone = $telefone;
        $stdEnderDest->cMun = FormatationUtil::retiraPontuacoes($venda->endereco_cliente->codigoIBGE);
        $stdEnderDest->xMun = FormatationUtil::retiraAcentos($venda->endereco_cliente->cidade);
        $stdEnderDest->UF = $venda->endereco_cliente->uf;

        $cep = str_replace(["-", "."], "", $venda->endereco_cliente->cep);
        $stdEnderDest->CEP = $cep;
        $stdEnderDest->cPais = "1058";
        $stdEnderDest->xPais = "BRASIL";
        $enderDest = $nfe->tagenderDest($stdEnderDest);

        //ENTREGA
        $entrega = false;
        foreach ($venda->itens as $i) {
            if ($i->produto->operVeic == 2) $entrega = true;
        }
        if ($entrega) {
            $stdEntrega = new \stdClass();
            if (strlen($cnpj_cpf) == 14) {
                $stdEntrega->CNPJ = $cnpj_cpf;
                $stdEntrega->IE = str_replace([".", "/", "-"], "", $venda->cliente->rg_ie);
            } else {
                $stdEntrega->CPF = $cnpj_cpf;
                $ie = str_replace([".", "/", "-"], "", $venda->cliente->rg_ie);
                if (strtolower($ie) != "isento" && $venda->cliente->contribuinte) $stdEntrega->IE = $ie;
            }
            $stdEntrega->xNome = FormatationUtil::retiraAcentos($venda->cliente->nome);
            $stdEntrega->xLgr = FormatationUtil::retiraAcentos($venda->endereco_cliente->rua);
            $stdEntrega->nro = FormatationUtil::retiraAcentos($venda->endereco_cliente->numero);
            $stdEntrega->xCpl = FormatationUtil::retiraAcentos($venda->endereco_cliente->complemento);
            $stdEntrega->xBairro = FormatationUtil::retiraAcentos($venda->endereco_cliente->bairro);
            $stdEntrega->cMun = FormatationUtil::retiraPontuacoes($venda->endereco_cliente->codigoIBGE);
            $stdEntrega->xMun = FormatationUtil::retiraAcentos($venda->endereco_cliente->cidade);
            $stdEntrega->UF = $venda->endereco_cliente->uf;
            $stdEntrega->CEP = $cep;
            $stdEntrega->cPais = '1058';
            $stdEntrega->xPais = 'BRASIL';
            $stdEntrega->fone = $telefone;
            $nfe->tagentrega($stdEntrega);
        }

        // --- INICIALIZAÇÃO DOS ACUMULADORES ---
        $totvBC = 0;
        $totvICMS = 0;
        $totvIPI = 0;
        $totvPIS = 0;
        $totvCOFINS = 0;

        //ITENS DA NFE
        foreach ($venda->itens as $key => $i) {
            $stdProd = new \stdClass();
            $stdProd->item = $key + 1;

            $cod = ValidationEAN13Util::validate_EAN13Barcode($i->produto->codigo);
            $stdProd->cEAN = $cod ? $i->produto->codigo : 'SEM GTIN';
            $stdProd->cEANTrib = $cod ? $i->produto->codigo : 'SEM GTIN';
            $stdProd->cProd = $i->produto->id;
            $stdProd->xProd = FormatationUtil::retiraAcentos($i->produto->produto);
            $stdProd->NCM = str_replace(".", "", $i->produto->ncm);

            if ($isRTC && !empty($i->produto->cClassTrib)) {
                $stdProd->cClassTrib = $i->produto->cClassTrib;
            }

            $stdProd->CFOP = $venda->cfopNota->cfop;
            $stdProd->uCom = $i->produto->un;
            $stdProd->qCom = $i->qtde;
            $stdProd->vUnCom = FormatationUtil::format($i->unitario);
            $stdProd->vProd = FormatationUtil::format(($i->qtde * $i->unitario));
            if ($i->desconto > 0) $stdProd->vDesc = FormatationUtil::format($i->desconto);

            $stdProd->uTrib = $i->produto->un;
            $stdProd->qTrib = $i->qtde;
            $stdProd->vUnTrib = FormatationUtil::format($i->unitario);
            $stdProd->indTot = 1;

            if ($i->produto->tpProd == 1) {
                $stdVeicProd = new \stdClass();
                $stdVeicProd->item = $key + 1;
                $stdVeicProd->tpOp = $i->produto->operVeic;
                $stdVeicProd->chassi = $i->produto->chassiVeic;
                $stdVeicProd->cCor = $i->produto->cCorVeic;
                $stdVeicProd->xCor = $i->produto->corVeic;
                $stdVeicProd->pot = $i->produto->cvVeic;
                $stdVeicProd->cilin = $i->produto->cm3Veic;
                $stdVeicProd->pesoL = $i->produto->pesoLVeic;
                $stdVeicProd->pesoB = $i->produto->pesoBVeic;
                $stdVeicProd->nSerie = $i->produto->serieVeic;
                $stdVeicProd->tpComb = $i->produto->combVeic;
                $stdVeicProd->nMotor = $i->produto->nMotorVeic;
                $stdVeicProd->CMT = $i->produto->cargaVeic;
                $stdVeicProd->dist = $i->produto->distVeic;
                $stdVeicProd->anoMod = $i->produto->anoModVeic;
                $stdVeicProd->anoFab = $i->produto->anoFabVeic;
                $stdVeicProd->tpPint = $i->produto->tpPVeic;
                $stdVeicProd->tpVeic = $i->produto->tpVeic;
                $stdVeicProd->espVeic = $i->produto->espVeic;
                $stdVeicProd->VIN = $i->produto->vinVeic;
                $stdVeicProd->condVeic = $i->produto->condVeic;
                $stdVeicProd->cMod = $i->produto->cMarcaVeic;
                $stdVeicProd->cCorDENATRAN = $i->produto->cCorMontVeic;
                $stdVeicProd->lota = $i->produto->lotVeic;
                $stdVeicProd->tpRest = $i->produto->restriVeic;
                $nfe->tagveicProd($stdVeicProd);
            }
            $nfe->tagprod($stdProd);

            $stdImposto = new \stdClass();
            $stdImposto->item = $key + 1;
            $nfe->tagimposto($stdImposto);
            // --- AJUSTE DINÂMICO DE ICMS POR CRT ---
            $stdICMS = new \stdClass();
            $stdICMS->item = $key + 1;
            $stdICMS->orig = 0;

            if (in_array($emitente->crt, [1, 4])) {
                $stdICMS->CSOSN = $i->produto->cst_csosn;
                if (in_array($stdICMS->CSOSN, ['101', '201', '900'])) {
                    $stdICMS->pCredSN = FormatationUtil::format($i->produto->icms);
                    $stdICMS->vCredICMSSN = FormatationUtil::format($stdProd->vProd * ($stdICMS->pCredSN / 100));
                }
                $nfe->tagICMSSN($stdICMS);
            } else {
                
                
                
                $stdICMS->CST = str_pad($i->produto->cst_csosn, 2, "0", STR_PAD_LEFT);
                
            
               if (in_array($stdICMS->CST, ['00','10','20','70','90'])) {

                    $stdICMS->modBC = 3;
                    $stdICMS->vBC   = FormatationUtil::format($stdProd->vProd);
                    $stdICMS->pICMS = FormatationUtil::format($i->produto->icms);
                    $stdICMS->vICMS = FormatationUtil::format($stdProd->vProd * ($i->produto->icms / 100));
                
                    // >>> ACUMULA TOTAIS <<<
                    $totvBC   += (float)$stdICMS->vBC;
                    $totvICMS += (float)$stdICMS->vICMS;
                
                } else {
                
                    // CST sem destaque
                    unset(
                        $stdICMS->modBC,
                        $stdICMS->vBC,
                        $stdICMS->pICMS,
                        $stdICMS->vICMS
                    );
                }
            

              
                

                
                // chamada única
                $icmsTag = $nfe->tagICMS($stdICMS);
                
                if ($icmsTag === null) {
                    throw new \Exception("Falha ao gerar ICMS. CST={$stdICMS->CST}");
                }

                if ($icmsTag === null) {
                    throw new \Exception("Erro ao gerar tagICMS para o item " . ($key + 1));
                }
            }
            
            // DIFAL só quando: interestadual + não contribuinte
            $ufEmit = $emitente->uf;                 // ex: "PE"
            $ufDest = $venda->cliente->endereco->uf;           // ex: "SP"
            $indIEDest = (int) $stdDest->indIEDest;  // 1,2,9
            
            if ($ufEmit !== $ufDest && $indIEDest === 9) {

                $stdICMSUFDest = new \stdClass();
                $stdICMSUFDest->item = $key + 1;
            
                $stdICMSUFDest->vBCUFDest = FormatationUtil::format($stdProd->vProd);
            
                // Se você ainda não vai calcular DIFAL agora:
                $stdICMSUFDest->pFCPUFDest = 0;
                $stdICMSUFDest->pICMSUFDest = 0;
                $stdICMSUFDest->pICMSInter = 12;        // depois ajusta 7/12 conforme UF
                $stdICMSUFDest->pICMSInterPart = 100;
            
                $stdICMSUFDest->vFCPUFDest = 0;
                $stdICMSUFDest->vICMSUFDest = 0;
                $stdICMSUFDest->vICMSUFRemet = 0;
            
                $nfe->tagICMSUFDest($stdICMSUFDest);
            }


            //PIS
            $stdPIS = new \stdClass();
            $stdPIS->item = $key + 1;
            $stdPIS->CST = $i->produto->cst_pis;
            $stdPIS->vBC = FormatationUtil::format($i->produto->pis) > 0 ? $stdProd->vProd : 0.00;
            $stdPIS->pPIS = FormatationUtil::format($i->produto->pis);
            $stdPIS->vPIS = FormatationUtil::format(($stdProd->vProd) * ($i->produto->pis / 100));
            $totvPIS += (float)$stdPIS->vPIS;
            $nfe->tagPIS($stdPIS);

            //COFINS
            $stdCOFINS = new \stdClass();
            $stdCOFINS->item = $key + 1;
            $stdCOFINS->CST = $i->produto->cst_cofins;
            $stdCOFINS->vBC = FormatationUtil::format($i->produto->cofins) > 0 ? $stdProd->vProd : 0.00;
            $stdCOFINS->pCOFINS = FormatationUtil::format($i->produto->cofins);
            $stdCOFINS->vCOFINS = FormatationUtil::format(($stdProd->vProd) * ($i->produto->cofins / 100));
            $totvCOFINS += (float)$stdCOFINS->vCOFINS;
            $nfe->tagCOFINS($stdCOFINS);

            //IPI
            $stdIPI = new \stdClass();
            $stdIPI->item = $key + 1;
            $stdIPI->cEnq = '999';
            $stdIPI->CST = $i->produto->ipi;
            $stdIPI->vBC = FormatationUtil::format($i->produto->ipi) > 0 ? $stdProd->vProd : 0.00;
            $stdIPI->pIPI = FormatationUtil::format($i->produto->ipi);
            $stdIPI->vIPI = FormatationUtil::format($stdProd->vProd * ($i->produto->ipi / 100));
            $totvIPI += (float)$stdIPI->vIPI;
            $nfe->tagIPI($stdIPI);

            if ($isRTC) {
                $stdIBSCBS = new \stdClass();
                $stdIBSCBS->item = $key + 1;
                $cstBanco = $i->produto->cst_ibs_cbs ?? null;
                $stdIBSCBS->CST = ($cstBanco && strlen($cstBanco) === 3) ? $cstBanco : '010';
                if (!empty($i->produto->cClassTrib)) $stdIBSCBS->cClassTrib = $i->produto->cClassTrib;
                $stdIBSCBS->vBC = FormatationUtil::format($stdProd->vProd);
                $stdIBSCBS->gIBSUF_pIBSUF = FormatationUtil::format($i->produto->pIBS);
                $stdIBSCBS->gIBSUF_vIBSUF = FormatationUtil::format($stdIBSCBS->vBC * ($stdIBSCBS->gIBSUF_pIBSUF / 100));
                $stdIBSCBS->gIBSMun_pIBSMun = 0.00;
                $stdIBSCBS->gIBSMun_vIBSMun = 0.00;
                $stdIBSCBS->vIBS = $stdIBSCBS->gIBSUF_vIBSUF + $stdIBSCBS->gIBSMun_vIBSMun;
                $stdIBSCBS->gCBS_pCBS = FormatationUtil::format($i->produto->pCBS);
                $stdIBSCBS->gCBS_vCBS = FormatationUtil::format($stdIBSCBS->vBC * ($stdIBSCBS->gCBS_pCBS / 100));
                $nfe->tagIBSCBS($stdIBSCBS);

                if (isset($i->produto->pIS_imposto) && $i->produto->pIS_imposto > 0) {
                    $stdIS = new \stdClass();
                    $stdIS->item = $key + 1;
                    $stdIS->vBC  = $stdProd->vProd;
                    $stdIS->pIS  = FormatationUtil::format($i->produto->pIS_imposto);
                    $stdIS->vIS  = FormatationUtil::format($stdIS->vBC * ($stdIS->pIS / 100));
                    $nfe->tagIS($stdIS);
                }
            }
        }

        $stdTransp = new \stdClass();
        $stdTransp->modFrete = '9';
        $nfe->tagtransp($stdTransp);

        //TOTALIZADOR NFE (CORRIGIDO PARA ACUMULAR ITENS)
        $stdICMSTot = new \stdClass();
        $stdICMSTot->vProd = FormatationUtil::format($venda->subtotal);
        $stdICMSTot->vBC = FormatationUtil::format($totvBC);
        $stdICMSTot->vICMS = FormatationUtil::format($totvICMS);
        $stdICMSTot->vICMSDeson = 0.00;
        $stdICMSTot->vBCST = 0.00;
        $stdICMSTot->vST = 0.00;
        $stdICMSTot->vFrete = 0.00;
        $stdICMSTot->vSeg = 0.00;
        $stdICMSTot->vDesc = $venda->desconto > 0 ? FormatationUtil::format($venda->desconto) : 0.00;
        $stdICMSTot->vII = 0.00;
        $stdICMSTot->vIPI = FormatationUtil::format($totvIPI);
        $stdICMSTot->vPIS = FormatationUtil::format($totvPIS);
        $stdICMSTot->vCOFINS = FormatationUtil::format($totvCOFINS);
        $stdICMSTot->vOutro = 0.00;
        $stdICMSTot->vTotTrib = 0.00;
        $stdICMSTot->vNF = FormatationUtil::format($venda->total);
        $nfe->tagICMSTot($stdICMSTot);

        //DUPLICATAS
        $stdFat = new \stdClass();
        $stdFat->nFat = (int) $numeroNFe;
        $stdFat->vOrig = FormatationUtil::format($venda->subtotal);
        $stdFat->vDesc = FormatationUtil::format($venda->desconto);
        $stdFat->vLiq = FormatationUtil::format($venda->subtotal - $venda->desconto);
        if ($venda->tipo_pagamento != '90') $nfe->tagfat($stdFat);

        foreach ($venda->fatura as $fat) {
            $stdPag = new \stdClass();
            $nfe->tagpag($stdPag);
            $stdDetPag = new \stdClass();
            $mapPagamento = [
                "Dinheiro" => '01', "Cheque" => '02', "Cartão de Crédito" => '03',
                "Cartão de Débito" => '04', "Crédito Loja" => '05', "Vale Alimentação" => '10',
                "Vale Refeição" => '11', "Vale Presente" => '12', "Vale Combustível" => '13',
                "Duplicata Mercantil" => '14', "Boleto Bancário" => '15', "Depósito Bancário" => '16',
                "PIX" => '17', "Sem Pagamento" => '90', "Outros" => '99'
            ];
            $stdDetPag->tPag = $mapPagamento[$fat->forma_pag->descricao] ?? '99';
            $stdDetPag->vPag = $fat->forma_pag->descricao != 'Sem Pagamento' ? FormatationUtil::format($fat->valor) : 0;
            $stdDetPag->indPag = 1;
            $stdDetPag->vTroco = 0;
            if (in_array($fat->forma_pag->descricao, ['Cartão de Crédito', 'Cartão de Débito'])) $stdDetPag->tpIntegra = '2';
            $nfe->tagdetPag($stdDetPag);
        }

        $stdInfCpl = new \stdClass();
        $stdInfCpl->infCpl = $venda->info_complementares;
        $nfe->taginfAdic($stdInfCpl);

        if (getenv('AUT_XML') != '') {
            $stdAut = new \stdClass();
            $stdAut->CNPJ = str_replace([".", "-", "/", " "], "", getenv('AUT_XML'));
            $nfe->tagautXML($stdAut);
        }

        $stdRT = new \stdClass();
        $stdRT->CNPJ = getenv('RESP_CNPJ');
        $stdRT->xContato = getenv('RESP_NOME');
        $stdRT->email = getenv('RESP_EMAIL');
        $stdRT->fone = getenv('RESP_FONE');
        $nfe->taginfRespTec($stdRT);

        try {
            $nfe->montaNFe();
            $xml = $nfe->getXML();
            return [
                'chave' => $nfe->getChave(),
                'xml' => $xml,
                'nNf' => $stdIde->nNF,
            ];
        } catch (\Exception $e) {
            return ['erros_xml' => $nfe->getErrors()];
        }
    }

    public function sign($xml)
    {
        return $this->tools->signNFe($xml);
    }

    // public function transmitir($signXml, $chave, $caminho)
    // {
    //     try {
    //         $idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
    //         $resp = $this->tools->sefazEnviaLote([$signXml], $idLote);

    //         $st = new Standardize();
    //         $std = $st->toStd($resp);
    //         sleep(2);
    //         if ($std->cStat != 103) {

    //             return [
    //                 'erro' => "[$std->cStat] - $std->xMotivo",
    //             ];
    //         }
    //         $recibo = $std->infRec->nRec;
    //         $protocolo = $this->tools->sefazConsultaRecibo($recibo);
    //         sleep(3);
    //         $xml = Complements::toAuthorize($signXml, $protocolo);
    //         if (!File::exists(public_path($caminho . '/'))) {
    //             File::makeDirectory(public_path($caminho . '/'), 0777, true, true);
    //         }
    //         file_put_contents(public_path($caminho . '/') . $chave . '.xml', $xml);
    //         return [
    //             'sucesso' => $recibo,
    //         ];
    //     } catch (\Exception $e) {
    //         return [
    //             'erro' => $e->getMessage(),
    //         ];
    //     }
    // }

    public function transmitir($signXml, $chave, $caminho)
    {
        try {
            // Define idLote com 15 dígitos numéricos
            $idLote = str_pad(100, 15, '0', STR_PAD_LEFT);

            // Envia em modo síncrono
            $resp = $this->tools->sefazEnviaLote([$signXml], $idLote, 1);

            $st = new Standardize();
            $std = $st->toStd($resp);

            if ($std->cStat == 104) {
                $infProt = $std->protNFe->infProt;

                if ($infProt->cStat == 100) {
                    $xml = Complements::toAuthorize($signXml, $resp);

                    if (!File::exists(public_path($caminho . '/'))) {
                        File::makeDirectory(public_path($caminho . '/'), 0777, true, true);
                    }

                    file_put_contents(public_path($caminho . '/') . $chave . '.xml', $xml);

                    return [
                        'sucesso' => $infProt->nProt,
                    ];
                } else {
                    return [
                        'erro' => "Erro na autorização: [{$infProt->cStat}] - {$infProt->xMotivo}",
                    ];
                }
            } else {
                return [
                    'erro' => "Erro no processamento do lote: [{$std->cStat}] - {$std->xMotivo}",
                ];
            }
        } catch (\Exception $e) {
            return [
                'erro' => $e->getMessage(),
            ];
        }
    }

    public function inutilizarNum($serie, $numI, $numF, $xJust, $caminho)
    {
        try {
            $response = $this->tools->sefazInutiliza($serie, $numI, $numF, $xJust);
            sleep(2);
            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();
            $arr = $stdCl->toArray();
            $json = $stdCl->toJson();

            if ($std->infInut->cStat == 102 || $std->infInut->cStat == 563) {
                $xml = Complements::toAuthorize($this->tools->lastRequest, $response);
                if (!File::exists(public_path($caminho . '/'))) {
                    File::makeDirectory(public_path($caminho . '/'), 0777, true, true);
                }
                file_put_contents(public_path($caminho . '/') . $std->infInut->attributes->Id . '.xml', $xml);

                return $json;
            } else {
                ['erro' => true, 'data' => $arr];
            }
        } catch (\Exception $e) {
            return ['erro' => true, 'data' => $e->getMessage()];
        }
    }

    public function cartaCorrecao($venda, $justificativa, $caminho)
    {
        try {
            $chave = $venda->chave;
            $xCorrecao = $justificativa;
            $nSeqEvento = $venda->sequencia_evento + 1;
            $response = $this->tools->sefazCCe($chave, $xCorrecao, $nSeqEvento);
            sleep(2);
            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();
            $arr = $stdCl->toArray();
            $json = $stdCl->toJson();
            if ($std->cStat != 128) {
            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '135' || $cStat == '136') {
                    $xml = Complements::toAuthorize($this->tools->lastRequest, $response);
                    if (!File::exists(public_path($caminho . '/'))) {
                        File::makeDirectory(public_path($caminho . '/'), 0777, true, true);
                    }
                    file_put_contents(public_path($caminho . '/') . $chave . '.xml', $xml);

                    $venda->sequencia_evento += 1;
                    $venda->save();
                    return $json;
                } else {
                    return ['erro' => true, 'data' => $arr];
                }
            }
        } catch (\Exception $e) {
            return ['erro' => true, 'data' => $e->getMessage()];
        }
    }

    public function cancelar($venda, $justificativa, $caminho)
    {
        try {
            $chave = $venda->chave;
            $response = $this->tools->sefazConsultaChave($chave);
            sleep(2);
            $stdCl = new Standardize($response);
            $arr = $stdCl->toArray();
            $xJust = $justificativa;
            $nProt = $arr['protNFe']['infProt']['nProt'];

            $response = $this->tools->sefazCancela($chave, $xJust, $nProt);
            sleep(2);
            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();
            $arr = $stdCl->toArray();
            $json = $stdCl->toJson();
            if ($std->cStat != 128) {
            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                    $xml = Complements::toAuthorize($this->tools->lastRequest, $response);
                    if (!File::exists(public_path($caminho . '/'))) {
                        File::makeDirectory(public_path($caminho . '/'), 0777, true, true);
                    }
                    file_put_contents(public_path($caminho . '/') . $chave . '.xml', $xml);

                    return $json;
                } else {
                    return ['erro' => true, 'data' => $arr];
                }
            }
        } catch (\Exception $e) {
            return ['erro' => true, 'data' => $e->getMessage()];
        }
    }
}