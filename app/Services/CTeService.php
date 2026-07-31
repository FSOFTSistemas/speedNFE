<?php

namespace App\Services;

use App\Models\Empresa;
use App\Utils\FormatationUtil;
use Exception;
use NFePHP\Common\Certificate;
use NFePHP\CTe\Common\Standardize;
use NFePHP\CTe\Complements;
use NFePHP\CTe\MakeCTe;
use NFePHP\CTe\Tools;

class CTeService
{
    private $tools;

    public function __construct($config, $emitente)
    {
        $certificado = file_get_contents(storage_path('app/certificados/'.$emitente->razao.'.pfx'));
        $this->tools = new Tools(json_encode($config), Certificate::readPfx($certificado, $emitente->senhaCertificado));
    }

    /**
     * Monta o XML do CTe (modal rodoviário) a partir do cabeçalho (CTe) e seus
     * relacionamentos (remetente/destinatário/veículo/documentos). Não assina nem
     * transmite - ver sign()/transmitir().
     */
    public function gerarXml($cte, $emitente)
    {
        $ctee = new MakeCTe;

        $stdInf = new \stdClass;
        $stdInf->Id = '';
        $stdInf->versao = '4.00';
        $ctee->taginfCTe($stdInf);

        $stdIde = new \stdClass;
        $stdIde->cUF = Empresa::getCUF($emitente->endereco->uf);
        $stdIde->cCT = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        $stdIde->CFOP = $cte->cfop;
        $stdIde->natOp = 'Prestacao de servico de transporte';
        $stdIde->serie = $cte->serie;
        $stdIde->nCT = $cte->numero;
        $stdIde->dhEmi = date('Y-m-d\TH:i:sP');
        $stdIde->tpImp = 1;
        $stdIde->tpEmis = 1;
        $stdIde->cDV = '0';
        $stdIde->tpAmb = (int) $emitente->ambiente;
        $stdIde->tpCTe = 0;
        $stdIde->procEmi = 0;
        $stdIde->verProc = '1.0';
        $stdIde->cMunEnv = FormatationUtil::retiraPontuacoes($emitente->endereco->codigoIBGE);
        $stdIde->xMunEnv = FormatationUtil::retiraAcentos($emitente->endereco->cidade);
        $stdIde->UFEnv = $emitente->endereco->uf;
        $stdIde->modal = '01';
        $stdIde->tpServ = 0;
        $stdIde->cMunIni = $cte->mun_ini_codigo;
        $stdIde->xMunIni = FormatationUtil::retiraAcentos($cte->mun_ini_nome);
        $stdIde->UFIni = $cte->uf_inicio;
        $stdIde->cMunFim = $cte->mun_fim_codigo;
        $stdIde->xMunFim = FormatationUtil::retiraAcentos($cte->mun_fim_nome);
        $stdIde->UFFim = $cte->uf_fim;
        $stdIde->retira = 0;
        $stdIde->indIEToma = 1;
        $ctee->tagide($stdIde);

        $stdToma = new \stdClass;
        $stdToma->toma = (int) $cte->toma;
        $ctee->tagtoma3($stdToma);

        $stdEmit = new \stdClass;
        $stdEmit->CNPJ = FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj);
        $stdEmit->IE = FormatationUtil::retiraPontuacoes($emitente->rg_ie);
        $stdEmit->xNome = FormatationUtil::retiraAcentos($emitente->razao);
        $stdEmit->xFant = FormatationUtil::retiraAcentos($emitente->fantasia);
        $stdEmit->CRT = $emitente->crt;
        $ctee->tagemit($stdEmit);

        $stdEnderEmit = new \stdClass;
        $stdEnderEmit->xLgr = FormatationUtil::retiraAcentos($emitente->endereco->rua);
        $stdEnderEmit->nro = $emitente->endereco->numero;
        $stdEnderEmit->xBairro = FormatationUtil::retiraAcentos($emitente->endereco->bairro);
        $stdEnderEmit->cMun = FormatationUtil::retiraPontuacoes($emitente->endereco->codigoIBGE);
        $stdEnderEmit->xMun = FormatationUtil::retiraAcentos($emitente->endereco->cidade);
        $stdEnderEmit->CEP = FormatationUtil::retiraPontuacoes($emitente->endereco->cep);
        $stdEnderEmit->UF = $emitente->endereco->uf;
        $stdEnderEmit->fone = FormatationUtil::retiraPontuacoes($emitente->celular);
        $ctee->tagenderEmit($stdEnderEmit);

        $remetente = $cte->remetente;
        $stdRem = new \stdClass;
        $docRemetente = FormatationUtil::retiraPontuacoes($remetente->cpf_cnpj);
        if (strlen($docRemetente) > 11) {
            $stdRem->CNPJ = $docRemetente;
        } else {
            $stdRem->CPF = $docRemetente;
        }
        $stdRem->IE = FormatationUtil::retiraPontuacoes($remetente->rg_ie);
        $stdRem->xNome = FormatationUtil::retiraAcentos($remetente->nome);
        $ctee->tagrem($stdRem);

        $stdEnderReme = new \stdClass;
        $stdEnderReme->xLgr = FormatationUtil::retiraAcentos($remetente->endereco->rua);
        $stdEnderReme->nro = $remetente->endereco->numero;
        $stdEnderReme->xBairro = FormatationUtil::retiraAcentos($remetente->endereco->bairro);
        $stdEnderReme->cMun = FormatationUtil::retiraPontuacoes($remetente->endereco->codigoIBGE);
        $stdEnderReme->xMun = FormatationUtil::retiraAcentos($remetente->endereco->cidade);
        $stdEnderReme->CEP = FormatationUtil::retiraPontuacoes($remetente->endereco->cep);
        $stdEnderReme->UF = $remetente->endereco->uf;
        $ctee->tagenderReme($stdEnderReme);

        $destinatario = $cte->destinatario;
        $stdDest = new \stdClass;
        $docDestinatario = FormatationUtil::retiraPontuacoes($destinatario->cpf_cnpj);
        if (strlen($docDestinatario) > 11) {
            $stdDest->CNPJ = $docDestinatario;
        } else {
            $stdDest->CPF = $docDestinatario;
        }
        $stdDest->IE = FormatationUtil::retiraPontuacoes($destinatario->rg_ie);
        $stdDest->xNome = FormatationUtil::retiraAcentos($destinatario->nome);
        $ctee->tagdest($stdDest);

        $stdEnderDest = new \stdClass;
        $stdEnderDest->xLgr = FormatationUtil::retiraAcentos($destinatario->endereco->rua);
        $stdEnderDest->nro = $destinatario->endereco->numero;
        $stdEnderDest->xBairro = FormatationUtil::retiraAcentos($destinatario->endereco->bairro);
        $stdEnderDest->cMun = FormatationUtil::retiraPontuacoes($destinatario->endereco->codigoIBGE);
        $stdEnderDest->xMun = FormatationUtil::retiraAcentos($destinatario->endereco->cidade);
        $stdEnderDest->CEP = FormatationUtil::retiraPontuacoes($destinatario->endereco->cep);
        $stdEnderDest->UF = $destinatario->endereco->uf;
        $ctee->tagenderDest($stdEnderDest);

        $stdVPrest = new \stdClass;
        $stdVPrest->vTPrest = $cte->vTPrest;
        $stdVPrest->vRec = $cte->vRec;
        $ctee->tagvPrest($stdVPrest);

        $stdComp = new \stdClass;
        $stdComp->xNome = 'FRETE VALOR';
        $stdComp->vComp = $cte->vTPrest;
        $ctee->tagComp($stdComp);

        $stdIcms = new \stdClass;
        $stdIcms->cst = '00';
        $stdIcms->vBC = $cte->vTPrest;
        $stdIcms->pICMS = $cte->picms;
        $stdIcms->vICMS = round($cte->vTPrest * $cte->picms / 100, 2);
        $ctee->tagicms($stdIcms);

        $ctee->taginfCTeNorm();

        $stdInfCarga = new \stdClass;
        $stdInfCarga->vCarga = $cte->vCarga;
        $stdInfCarga->proPred = FormatationUtil::retiraAcentos($cte->xProd);
        $ctee->taginfCarga($stdInfCarga);

        $stdInfQ = new \stdClass;
        $stdInfQ->cUnid = '01';
        $stdInfQ->tpMed = 'PESO BRUTO';
        $stdInfQ->qCarga = $cte->qCarga;
        $ctee->taginfQ($stdInfQ);

        foreach ($cte->documentos as $documento) {
            $stdInfNFe = new \stdClass;
            $stdInfNFe->chave = $documento->chave;
            $ctee->taginfNFe($stdInfNFe);
        }

        $stdInfModal = new \stdClass;
        $stdInfModal->versaoModal = '4.00';
        $ctee->taginfModal($stdInfModal);

        $stdRodo = new \stdClass;
        $stdRodo->RNTRC = FormatationUtil::retiraPontuacoes($cte->veiculo->proprietario->rntrc ?? '');
        $ctee->tagrodo($stdRodo);

        $stdInfRespTec = new \stdClass;
        $stdInfRespTec->CNPJ = 42879649000174;
        $stdInfRespTec->xContato = 'FSOFT SISTEMAS';
        $stdInfRespTec->email = 'fsoftsistemas@gmail.com';
        $stdInfRespTec->fone = '87981753993';
        $ctee->taginfRespTec($stdInfRespTec);

        try {
            $arr = [
                'xml' => $ctee->getXML(),
                'chave' => $ctee->getChave(),
                'nCT' => $stdIde->nCT,
            ];

            return $arr;
        } catch (\Exception $e) {
            return [
                'erros_xml' => $ctee->getErrors(),
            ];
        }
    }

    public function sign($xml)
    {
        return $this->tools->signCTe($xml);
    }

    public function transmitir($signedXml)
    {
        try {
            $resp = $this->tools->sefazEnviaCTe($signedXml);
            $st = new Standardize();
            $std = $st->toStd($resp);
            $cStat = $std->protCTe->infProt->cStat ?? null;
            if ($cStat != 100) {
                return [
                    'erro' => '['.($cStat ?? '').'] - '.($std->protCTe->infProt->xMotivo ?? 'Erro na transmissão'),
                ];
            }
            $xml = Complements::toAuthorize($signedXml, $resp);

            return [
                'sucesso' => true,
                'nProt' => $std->protCTe->infProt->nProt,
                'xml' => $xml,
            ];
        } catch (Exception $e) {
            return [
                'erro' => $e->getMessage(),
            ];
        }
    }

    public function cancelar($cte, $justificativa)
    {
        try {
            $resp = $this->tools->sefazCancela($cte->chave, $justificativa, $cte->nProtocolo);
            $st = new Standardize();
            $std = $st->toStd($resp);
            if (($std->infEvento->cStat ?? null) != 135) {
                return [
                    'erro' => '['.($std->infEvento->cStat ?? '').'] - '.($std->infEvento->xMotivo ?? 'Erro no cancelamento'),
                ];
            }

            return [
                'sucesso' => true,
                'nProt' => $std->infEvento->nProt,
            ];
        } catch (Exception $e) {
            return [
                'erro' => $e->getMessage(),
            ];
        }
    }
}
