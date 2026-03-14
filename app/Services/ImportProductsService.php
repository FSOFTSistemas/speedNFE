<?php

namespace App\Services;

use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Tools;

class ImportProductsService
{
    private $tools;

    public function __construct($config, $emitente)
    {
        $certificado = file_get_contents('../storage/app/public/certificados/' . $emitente->razao . '.pfx');
        $this->tools = new Tools(json_encode($config), Certificate::readPfx($certificado, $emitente->senhaCertificado));
        $this->tools->model('55');
    }

    public function importProducts($chaveNota)
    {
        $this->manifest($chaveNota, '210200', 1);
        $this->tools->setEnvironment(1);
        $response = $this->tools->sefazDownload($chaveNota);
        $stdCl = new Standardize($response);
        return $stdCl->toStd();
    }

    //Codigo para confirmação de operação: 210200
    //Ciência da emissão: 210210
    //Código para desconhecimento da operação: 210220
    //Código para operação não realizada: 210240
    public function manifest($chaveNota, $evento, $sequenciaEvento, $justificativa = '')
    {
        $response = $this->tools->sefazManifesta($chaveNota, $evento, $justificativa, $sequenciaEvento);
        $std = new Standardize($response);
        return $std->toStd();
    }

    public function keyQuery($chaveNota)
    {
        $response = $this->tools->sefazConsultaChave($chaveNota);
        $stdCl = new Standardize($response);
        return $stdCl->toStd();
    }

    public static function readXML($xml)
    {
        $note = simplexml_load_file($xml);
        return ['nota' => ['ide' => $note->NFe->infNFe->ide, 'emit' => $note->NFe->infNFe->emit, 'vNF' => $note->NFe->infNFe->total->ICMSTot->vNF, 'chNFe' => $note->protNFe->infProt->chNFe], 'prods' => $note->NFe->infNFe->det];
    }

}