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
        $response = $this->tools->sefazDistDFe(0, 0, $chaveNota);
        $stdCl = new Standardize($response);
        return $stdCl->toStd();
    }

}