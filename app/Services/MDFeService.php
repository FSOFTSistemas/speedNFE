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
        // dd($transporte, $mdfe, $emitente);

        //Identificação do MDF-e
        $numeroMDFe = $emitente->ultimaMDFe + 1;
        $stdIde = new \stdClass();
        $stdIde->cUF = \App\Models\Empresa::getCUF($emitente->endereco->uf);
        $stdIde->tpAmb = $emitente->ambiente;
        $stdIde->tpEmit = '1';
        $stdIde->mod = $mdfe->mod;
        $stdIde->serie = $emitente->serie;
        $stdIde->nMDF = $numeroMDFe;
        $stdIde->cDV = '0';
        $stdIde->modal = '1';
        $stdIde->dhEmi = $transporte->created_at;
        $stdIde->tpEmis = '1';
        $stdIde->procEmi = '0';
        $stdIde->verProc = '1.0';
        $stdIde->UFIni = $transporte->uf_inicio;
        $stdIde->UFFim = $transporte->uf_termino;
        $mdfe->tagide($stdIde);

        //Informações do Município de Carregamento
        $infMunCarrega = new \stdClass();
        $infMunCarrega->cMunCarrega = $transporte->codMunCarregamento;
        $infMunCarrega->xMunCarrega = $transporte->municipioCarregamento;
        $mdfe->taginfMunCarrega($infMunCarrega);

        //Informações dos Municípios de Percurso
        foreach ($transporte->percurso as $UFPer) {
            $infPercurso = new \stdClass();
            $infPercurso->UFPer = $UFPer;
            $mdfe->taginfPercurso($infPercurso);
        }

        //Identificação do Emitente do Manifesto
        $emit = new \stdClass();
        $emit->CNPJ = $emitente->cnpj;
        $emit->IE = $emitente->ie;
        $emit->xNome = $emitente->razao;
        $emit->xFant = $emitente->fantasia;
        $mdfe->tagemit($emit);

        //Endereço do Emitente
        $enderEmit = new \stdClass();
        $enderEmit->xLgr = $emitente->rua;
        $enderEmit->nro = $emitente->numero;
        $enderEmit->xBairro = $emitente->bairro;
        $enderEmit->cMun = $emitente->codMun;
        $enderEmit->xMun = $emitente->cidade;
        $enderEmit->CEP = $emitente->cep;
        $enderEmit->UF = $emitente->uf;
        $enderEmit->fone = $emitente->contato;
        $enderEmit->email = $emitente->email;
        $mdfe->tagenderEmit($enderEmit);

        //Grupo de informações para Agência Reguladora
        if ($transporte->veiculoTracao->tipo_propriedade == 'Terceiro') {
            $infANTT = new \stdClass();
            $infANTT->RNTRC = $transporte->veiculoTracao->RNTRC;
            $mdfe->taginfANTT($infANTT);
        }

        //Informações do Contratante do serviço de transporte
        // $infContratante = new \stdClass();
        // $infContratante->CNPJ = 'Não é necessa´rio por momento';
        // $mdfe->taginfContratante($infContratante);

        //Dados do Veículo com a Tração
        $veicTracao = new \stdClass();
        $veicTracao->cInt = $transporte->veiculoTracao->id;
        $veicTracao->placa = $transporte->veiculoTracao->placa;
        $veicTracao->RENAVAM = $transporte->veiculoTracao->renavam;
        $veicTracao->tara = $transporte->veiculoTracao->tara;
        $veicTracao->capKG = $transporte->veiculoTracao->capacidade;
        $veicTracao->tpRod = $transporte->veiculoTracao->tipo_rodado;
        $veicTracao->tpCar = $transporte->veiculoTracao->tipo_carroceria;
        $veicTracao->UF = $transporte->veiculoTracao->uf_veiculo;
        $veicTracao->capM3 = $transporte->veiculoTracao->capacidade_m3;

        //Identificação do Motorista
        $condutor = new \stdClass();
        $condutor->xNome = $transporte->motorista->nome;
        $condutor->CPF = $transporte->motorista->cpf;
        $veicTracao->condutor = [$condutor];

        //Identificação do Proprietário do Veículo
        $prop = new \stdClass();
        $proprietario = $transporte->veiculoTracao->proprietario;
        if (strlen($proprietario->cnpj_cpf) == 14) {
            $prop->CPF = $proprietario->cnpj_cpf;
        } else {
            $prop->CNPJ = $proprietario->cnpj_cpf;
        }
        if ($proprietario->RNTRC) {
            $prop->RNTRC = $proprietario->RNTRC;
        }
        $prop->xNome = $proprietario->nome;
        $prop->IE = $proprietario->ie;
        $prop->UF = $proprietario->uf;
        $prop->tpProp = $proprietario->tipo_proprietario;
        $veicTracao->prop = $prop;
        $mdfe->tagveicTracao($veicTracao);

        if ($transporte->veiculoReboque) {
            //Dados dos Reboques
            $veicReboque = new \stdClass();
            $veicReboque->cInt = $transporte->veiculoReboque->id;
            $veicReboque->placa = $transporte->veicReboque->placa;
            $veicReboque->RENAVAM = $transporte->veicReboque->renavam;
            $veicReboque->tara = $transporte->veicReboque->tara;
            $veicReboque->capKG = $transporte->veicReboque->capacidade;
            $veicReboque->capM3 = $transporte->veicReboque->capacidade_m3;
            $veicReboque->tpCar = $transporte->veicReboque->tipo_carroceria;
            $veicReboque->UF = $transporte->veicReboque->uf_veiculo;

            //Identificação do Proprietário do Reboque
            $prop = new \stdClass();
            $proprietario = $transporte->veiculoReboque->proprietario;
            if (strlen($proprietario->cnpj_cpf) == 14) {
                $prop->CPF = $proprietario->cnpj_cpf;
            } else {
                $prop->CNPJ = $proprietario->cnpj_cpf;
            }
            if ($proprietario->RNTRC) {
                $prop->RNTRC = $proprietario->RNTRC;
            }
            $prop->xNome = $proprietario->nome;
            $prop->IE = $proprietario->ie;
            $prop->UF = $proprietario->uf;
            $prop->tpProp = $proprietario->tipo_proprietario;
            $veicReboque->prop = $prop;
            $mdfe->tagveicReboque($veicReboque);
        }

        //Informações dos lacres de um trasnporte especial
        if ($transporte->nLacre) {
            $lacRodo = new \stdClass();
            $lacRodo->nLacre = $transporte->nLacre;
            $mdfe->taglacRodo($lacRodo);
        }

        //Informações dos Municípios de Descarregamento
        $infMunDescarga = new \stdClass();
        $infMunDescarga->cMunDescarga = $transporte->codMunDescarregamento;
        $infMunDescarga->xMunDescarga = $transporte->municipioDescarregamento;
        $mdfe->taginfMunDescarga($infMunDescarga);

        //Informações para CT-e, implementar no futuro
//         $std = new \stdClass();
//         $std->chCTe = '35310800000000000372570010001999091000027765';
//         $std->SegCodBarra = '012345678901234567890123456789012345';
//         $std->indReentrega = '1';
//         $std->nItem = 0;

// /* Informações das Unidades de Transporte (Carreta/Reboque/Vagão) */
//         $stdinfUnidTransp = new \stdClass();
//         $stdinfUnidTransp->tpUnidTransp = '1';
//         $stdinfUnidTransp->idUnidTransp = 'AAA-1111';

// /* Lacres das Unidades de Transporte */
//         $stdlacUnidTransp = new \stdClass();
//         $stdlacUnidTransp->nLacre = ['00000001', '00000002'];

//         $stdinfUnidTransp->lacUnidTransp = $stdlacUnidTransp;

// /* Informações das Unidades de Carga (Containeres/ULD/Outros) */
//         $stdinfUnidCarga = new \stdClass();
//         $stdinfUnidCarga->tpUnidCarga = '1';
//         $stdinfUnidCarga->idUnidCarga = '01234567890123456789';

// /* Lacres das Unidades de Carga */
//         $stdlacUnidCarga = new \stdClass();
//         $stdlacUnidCarga->nLacre = ['00000001', '00000002'];

//         $stdinfUnidCarga->lacUnidCarga = $stdlacUnidCarga;
//         $stdinfUnidCarga->qtdRat = '3.50';

//         $stdinfUnidTransp->infUnidCarga = [$stdinfUnidCarga];
//         $stdinfUnidTransp->qtdRat = '3.50';

//         $std->infUnidTransp = [$stdinfUnidTransp];

// /* transporte de produtos classificados pela ONU como perigosos */
//         $stdperi = new \stdClass();
//         $stdperi->nONU = '1234';
//         $stdperi->xNomeAE = 'testeNome';
//         $stdperi->xClaRisco = 'testeClaRisco';
//         $stdperi->grEmb = 'testegrEmb';
//         $stdperi->qTotProd = '1';
//         $stdperi->qVolTipo = '1';
//         $std->peri = [$stdperi];

// /* Grupo de informações da Entrega Parcial (Corte de Voo) */
//         $stdinfEntregaParcial = new \stdClass();
//         $stdinfEntregaParcial->qtdTotal = '1234.56';
//         $stdinfEntregaParcial->qtdParcial = '1234.56';
//         $std->infEntregaParcial = $stdinfEntregaParcial;

//         $mdfe->taginfCTe($std);

//         $infMunDescarga = new \stdClass();
//         $infMunDescarga->cMunDescarga = '1502400';
//         $infMunDescarga->xMunDescarga = 'CASTANHAL';
//         $infMunDescarga->nItem = 1;
//         $mdfe->taginfMunDescarga($infMunDescarga);

// /* infCTe */
//         $std = new \stdClass();
//         $std->chCTe = '35310800000000000372570010001998991000614492';
//         $std->nItem = 1;
//         $mdfe->taginfCTe($std);

        //Informações das NFes
        foreach ($transporte->notas as $nota) {
            $infNFe = new \stdClass();
            $infNFe->chNFe = $nota->chave;
            $mdfe->taginfNFe($infNFe);
        }

        //Informações de Transporte da MDFe
        $infMDFe = new \stdClass();
        $infMDFe->chMDFe = '0';

        //Informações das Unidades de Transporte (Carreta/Reboque/Vagão)
        $unidades = [$transporte->veiculoTracao, $transporte->veiculoReboque];
        foreach ($unidades as $un) {
            $stdinfUnidTransp = new \stdClass();
            $stdinfUnidTransp->tpUnidTransp = $un->tipo_veiculo == 'Tração' ? '1' : '2';
            $stdinfUnidTransp->idUnidTransp = $un->placa;
        }

        //Lacres das Unidades de Transporte
        $stdlacUnidTransp = new \stdClass();
        $stdlacUnidTransp->nLacre = ['00000001', '00000002'];

        $stdinfUnidTransp->lacUnidaTransp = $stdlacUnidTransp;

        //Informações das Unidades de Carga (Containeres/ULD/Outros)
        $stdinfUnidCarga = new \stdClass();
        $stdinfUnidCarga->tpUnidCarga = '1';
        $stdinfUnidCarga->idUnidCarga = '01234567890123456789';

        //Lacres das Unidades de Carga
        $stdlacUnidCarga = new \stdClass();
        $stdlacUnidCarga->nLacre = ['00000001', '00000001'];

        $stdinfUnidCarga->lacUnidCarga = $stdlacUnidCarga;
        $stdinfUnidCarga->qtdRat = '3.50';

        $stdinfUnidTransp->infUnidCarga = [$stdinfUnidCarga];
        $stdinfUnidTransp->qtdRat = '3.50';

        $infMDFe->infUnidTransp = [$stdinfUnidTransp];

        //Transporte de produtos classificados pela ONU como perigosos
        if ($transporte->prodsPrerigosos) {
            $stdperi = new \stdClass();
            $stdperi->nONU = '1234';
            $stdperi->xNomeAE = 'testeNome';
            $stdperi->xClaRisco = 'testeClaRisco';
            $stdperi->grEmb = 'testegrEmb';
            $stdperi->qTotProd = '1';
            $stdperi->qVolTipo = '1';

            $infMDFe->peri = [$stdperi];
        }

        $mdfe->taginfMDFeTransp($infMDFe);

        $tot = new \stdClass();
        $tot->qCTe = '0';
        $tot->qNFe = count($transporte->notas);
        $tot->qMDFe = '0';
        $tot->vCarga = $transporte->valorTotal;
        $tot->cUnid = '01';
        $tot->qCarga = $transporte->pesoTotal;
        $mdfe->tagtot($tot);

        $prodPred = new \stdClass();
        $prodPred->tpCarga = $transporte->tipoCarga;
        $prodPred->xProd = $transporte->produtoPredominante;
        $prodPred->cEAN = null;
        $prodPred->NCM = null;

        $localCarrega = new \stdClass();
        $localCarrega->CEP = '00000000';
        $localCarrega->latitude = null;
        $localCarrega->longitude = null;

        $localDescarrega = new \stdClass();
        $localDescarrega->CEP = '00000000';
        $localDescarrega->latitude = null;
        $localDescarrega->longitude = null;

        $lotacao = new \stdClass();
        $lotacao->infLocalCarrega = $localCarrega;
        $lotacao->infLocalDescarrega = $localDescarrega;

        $prodPred->infLotacao = $lotacao;

        $mdfe->tagprodPred($prodPred);

        $infRespTec = new \stdClass();
        $infRespTec->CNPJ = 42879649000174;
        $infRespTec->xContato = 'FSOFT SISTEMAS';
        $infRespTec->email = 'fsoftsistemas@gmail.com';
        $infRespTec->fone = '87981753993';
        $mdfe->taginfRespTec($infRespTec);

        $infAdic = new \stdClass();
        $infAdic->infCpl = 'Hello my friend';
        $infAdic->infAdFisco = 'Very good bro!';
        $mdfe->taginfAdic($infAdic);

        $xml = $mdfe->getXml();
        header("Content-type: text/xml");
        echo $mdfe->getXML();
    }

}
