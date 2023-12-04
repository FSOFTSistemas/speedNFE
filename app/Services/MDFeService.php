<?php

namespace App\Services;

use App\Models\MDFE;
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
        // $this->tools->model(55);
    }

    public function gerarXml($transporte, $emitente)
    {
        $mdfe = new Make();
        // $stdInfMDFe = new \stdClass();
        // $stdInfMDFe->versao = $mdfe->versao;
        // $stdInfMDFe->Id = null;
        // $stdInfMDFe->pk_nItem = '';
        // $mdfe->taginfMDFeTransp($stdInfMDFe);

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
        $stdIde->dhEmi = $transporte->created_at;
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

        $rodo = new \stdClass();
        $mdfe->taglacRodo($rodo);

        $infANTT = new \stdClass();
        $mdfe->taginfANTT($infANTT);

        $infContratante = new \stdClass();
        $infContratante->CNPJ = $emitente->cnpj;
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

    }

    public function buscarMDFes($empresaId)
    {
        return MDFE::select('m_d_f_e_s.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'm_d_f_e_s.empresaId')
            ->where('m_d_f_e_s.empresaId', $empresaId)
            ->get();
    }

}