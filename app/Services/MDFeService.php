<?php

namespace App\Services;

use NFePHP\Common\Certificate;
use NFePHP\MDFe\Make;
use NFePHP\MDFe\Tools;

error_reporting(E_ALL);
ini_set('display_errors', 'On');
class MDFeService
{
    private $tools;

    public function __construct($config, $emitente)
    {
        $certificado = file_get_contents('../storage/app/public/certificados/' . $emitente->razao . '.pfx');
        $this->tools = new Tools(json_encode($config), Certificate::readPfx($certificado, $emitente->senhaCertificado));
    }

    public function gerarXml($transporte, $emitente)
    {
        $mdfe = new Make();

        $numeroMDFe = $emitente->ultimaMDFe + 1;
        $stdIde = new \stdClass();
        $stdIde->cUF = \App\Models\Empresa::getCUF($emitente->endereco->uf);
        $stdIde->cNF = rand(11111, 99999);
        $stdIde->tpAmb = $emitente->ambiente;
        $stdIde->tpEmit = 1;
        $stdIde->mod = $mdfe->mod;
        $stdIde->serie = $emitente->serie;
        $stdIde->nMDF = $numeroMDFe;
        $stdIde->cdv = '?';
        $stdIde->modal = 1;
        $stdIde->dhEmi = '2023-12-04';
        $stdIde->tpEmis = '?';
        $stdIde->procEmi = '?';
        $stdIde->verProc = '?';
        $stdIde->UFIni = $transporte->localCarregamento;
        $stdIde->UFFim =$transporte->localDescarregamento;
        $mdfe->tagide($stdIde);

        $infMunCarrega = new \stdClass();
        $infMunCarrega->cMunCarrega = $transporte->codMun;
        $infMunCarrega->xMunCarrega = $transporte->municipio;
        $mdfe->taginfMunCarrega($infMunCarrega);

        $emit = new \stdClass();
        $emit->CNPJ = $emitente->cnpj;
        $emit->IE = $emitente->ie;
        $emit->xNome = $emitente->razao;
        $emit->xFant = $emitente->fantasia;
        $mdfe->tagemit($emit);

        $enderEmit = new \stdClass();
        $enderEmit->xLgr = $emitente->rua;
        $enderEmit->nro = $emitente->numero;
        $enderEmit->xCpl = $emitente->complemento;
        $enderEmit->xBairro = $emitente->bairro;
        $enderEmit->cMun = $emitente->codMun;
        $enderEmit->xMun = $emitente->cidade;
        $enderEmit->CEP = $emitente->cep;
        $enderEmit->UF =  $emitente->uf;
        $enderEmit->fone = $emitente->contato;
        $mdfe->tagenderEmit($enderEmit);

        // $rodo = new \stdClass();
        // $mdfe->taglacRodo($rodo);

        $infANTT = new \stdClass();
        $mdfe->taginfANTT($infANTT);

        $infContratante = new \stdClass();
        $infContratante->CNPJ = '';
        $mdfe->taginfContratante($infContratante);

        $veicTracao = new \stdClass();
        $veicTracao->placa = $transporte->veicTracao->placa;
        $veicTracao->RENAVAM = $transporte->veicTracao->renavam;
        $veicTracao->tara = $transporte->veicTracao->tara;
        $veicTracao->capKG = $transporte->veicTracao->capacidade;
        $veicTracao->condutor->xNome = $transporte->motorista->nome;
        $veicTracao->condutor->CPF = $transporte->motorista->cpf;
        $veicTracao->tpRod = $transporte->veicTracao->tipo_rodado;
        $veicTracao->tpCar = $transporte->veicTracao->tipo_carroceria;
        $veicTracao->UF = $transporte->veicTracao->uf;
        $veicTracao->capM3 = $transporte->veicTracao->capacidade_m3;
        $veicTracao->tpProp = $transporte->veicTracao->tipo_propriedade;
        $mdfe->tagveicTracao($veicTracao);

        if ($transporte->veiculoReboque) {
            $veicReboque = new \stdClass();
            $veicReboque->placa = $transporte->veicReboque->placa;
            $veicReboque->RENAVAM = $transporte->veicReboque->renavam;
            $veicReboque->tara = $transporte->veicReboque->tara;
            $veicReboque->capKG = $transporte->veicReboque->capacidade;
            $veicReboque->capM3 = $transporte->veicReboque->capacidade_m3;
            $veicReboque->placa = $transporte->veicReboque->placa;
            $veicReboque->UF = $transporte->veicReboque->UF;
            $veicReboque->tpProp = $transporte->veicReboque->tipo_propriedade;
            $veicReboque->tpCar = $transporte->veicReboque->tipo_carroceria;
            $mdfe->tagveicReboque($veicReboque);
        }

        $infMunDescarga = new \stdClass();
        $infMunDescarga->cMunDescarga = $transporte->municipio;
        $infMunDescarga->xMunDescarga = $transporte->municipio;
        $mdfe->taginfMunDescarga($infMunDescarga);

        $infNFe = new \stdClass();
        foreach ($transporte->NFes as $NFe) {
            $infNFe->chNFe = $NFe->chave;
        }
        $mdfe->taginfNFe($infNFe);

        $seg = new \stdClass();
        $seg->infResp->respSeg = 1;
        $mdfe->tagseg($seg);

        $prodPred = new \stdClass();
        $prodPred->tpCarga = $transporte->tipoCarga;
        $prodPred->xProd = $transporte->produtoPredominante;
        $prodPred->NCM = $transporte->ncm;
        $prodPred->infLotacao->infLocalCarrega->CEP = $transporte->localCarregamento->cep;
        $prodPred->infLotacao->infLocalDescarrega->CEP = $transporte->localDescarregamento->cep;
        $mdfe->tagprodPred($prodPred);

        $tot = new \stdClass();
        $tot->qNFe = count($transporte->NFes);
        $tot->vCarga = $transporte->valorTotal;
        //CÓDIGO DE UNIDADE: 01 - KG, 02 - TON.
        $tot->cUnid = 01;
        $tot->qCarga = $transporte->pesoTotal;
        $mdfe->tagtot($tot);

        $infRespTec = new \stdClass();
        $infRespTec->CNPJ = 42879649000174;
        $infRespTec->xContato = 'FSOFT SISTEMAS';
        $infRespTec->email = 'fsoftsistemas@gmail.com';
        $infRespTec->fone = '87981753993';
        $mdfe->taginfRespTec($infRespTec);

        try {
            $mdfe->montaMDFe();
            $arr = [
                'chave' => $mdfe->getChave(),
                'xml' => $mdfe->getXML(),
                'nNf' => $stdIde->nNF,
            ];
            return $arr;
        } catch (\Exception $e) {
            return [
                'erros_xml' => $mdfe->getErrors(),
            ];
        }
    }

}