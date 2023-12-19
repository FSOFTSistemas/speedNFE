<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use NFePHP\Common\Certificate;
use NFePHP\MDFe\Common\Standardize;
use NFePHP\MDFe\Complements;
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
        if ($transporte->situacao == 'Autorizado' || $transporte->situacao == 'Cancelado') {
            return false;
        }
        $mdfe = new Make();

        //Identificação do MDF-e
        $numeroMDFe = $emitente->ultimaMDFe + 1;
        $stdIde = new \stdClass();
        $stdIde->cUF = \App\Models\Empresa::getCUF($emitente->endereco->uf);
        $stdIde->tpAmb = $emitente->ambiente;
        $stdIde->tpEmit = '2';
        $stdIde->mod = $mdfe->mod;
        $stdIde->serie = $emitente->serie;
        $stdIde->nMDF = $numeroMDFe;
        $stdIde->cDV = '0';
        $stdIde->modal = '1';
        $stdIde->dhEmi = date("Y-m-d\TH:i:sP");
        $stdIde->tpEmis = '1';
        $stdIde->procEmi = '0';
        $stdIde->verProc = '1.0';
        $stdIde->UFIni = $transporte->uf_inicio;
        $stdIde->UFFim = $transporte->uf_termino;
        $mdfe->tagide($stdIde);

        //Informações do Município de Carregamento
        $infMunCarrega = new \stdClass();
        $infMunCarrega->cMunCarrega = $this->retiraPontuacoes($transporte->codMunCarregamento);
        $infMunCarrega->xMunCarrega = $this->retiraAcentos($transporte->municipioCarregamento);
        $mdfe->taginfMunCarrega($infMunCarrega);

        //Informações dos Municípios de Percurso
        foreach (explode(' - ', $transporte->uf_percurso) as $UFPer) {
            $infPercurso = new \stdClass();
            $infPercurso->UFPer = $UFPer;
            $mdfe->taginfPercurso($infPercurso);
        }

        //Identificação do Emitente do Manifesto
        $emit = new \stdClass();
        if (strlen($emitente->cpf_cnpj) > 14) {
            $emit->CNPJ = $this->retiraPontuacoes($emitente->cpf_cnpj);
        } else {
            $emit->CPF = $this->retiraPontuacoes($emitente->cpf_cnpj);
        }
        $emit->IE = $this->retiraPontuacoes($emitente->ie);
        $emit->xNome = $this->retiraAcentos($emitente->razao);
        $emit->xFant = $this->retiraAcentos($emitente->fantasia);
        $mdfe->tagemit($emit);

        //Endereço do Emitente
        $enderEmit = new \stdClass();
        $enderEmit->xLgr = $this->retiraAcentos($emitente->endereco->rua);
        $enderEmit->nro = $emitente->endereco->numero;
        $enderEmit->xBairro = $this->retiraAcentos($emitente->endereco->bairro);
        $enderEmit->cMun = $this->retiraPontuacoes($emitente->endereco->codigoIBGE);
        $enderEmit->xMun = $this->retiraAcentos($emitente->endereco->cidade);
        $enderEmit->CEP = $this->retiraPontuacoes($emitente->endereco->cep);
        $enderEmit->UF = $emitente->endereco->uf;
        $enderEmit->fone = $this->retiraPontuacoes($emitente->celular);
        $mdfe->tagenderEmit($enderEmit);

        //Grupo de informações para Agência Reguladora
        if ($transporte->veiculoTracao->tipo_propriedade == 'Terceiro') {
            $infANTT = new \stdClass();
            $infANTT->RNTRC = $this->retiraPontuacoes($transporte->veiculoTracao->RNTRC);
            $mdfe->taginfANTT($infANTT);
        }

        //Informações do Contratante do serviço de transporte
        // $infContratante = new \stdClass();
        // $infContratante->CNPJ = 'Não é necessa´rio por momento';
        // $mdfe->taginfContratante($infContratante);

        //Dados do Veículo com a Tração
        $veicTracao = new \stdClass();
        $veicTracao->cInt = $transporte->veiculoTracao->id;
        $veicTracao->placa = $this->retiraPontuacoes($transporte->veiculoTracao->placa);
        $veicTracao->RENAVAM = $this->retiraPontuacoes($transporte->veiculoTracao->renavam);
        $veicTracao->tara = intval($transporte->veiculoTracao->tara);
        $veicTracao->capKG = intval($transporte->veiculoTracao->capacidade);
        $veicTracao->tpRod = explode('_', $transporte->veiculoTracao->tipo_rodado->name)[1];
        $veicTracao->tpCar = explode('_', $transporte->veiculoTracao->tipo_carroceria->name)[1];
        $veicTracao->UF = $transporte->veiculoTracao->uf_veiculo->value;
        $veicTracao->capM3 = intval($transporte->veiculoTracao->capacidade_m3);

        //Identificação do Motorista
        foreach ($transporte->motoristas as $cond) {
            $condutor = new \stdClass();
            $condutor->xNome = $this->retiraAcentos($cond->motorista->nome);
            $condutor->CPF = $this->retiraPontuacoes($cond->motorista->cpf);
            $veicTracao->condutor = [$condutor];
        }

        //Identificação do Proprietário do Veículo
        $prop = new \stdClass();
        $proprietario = $transporte->veiculoTracao->proprietario;
        if (strlen($proprietario->cpf_cnpj) == 14) {
            $prop->CPF = $this->retiraPontuacoes($proprietario->cpf_cnpj);
        } else {
            $prop->CNPJ = $this->retiraPontuacoes($proprietario->cpf_cnpj);
        }
        $prop->RNTRC = $this->retiraPontuacoes($this->retiraAcentos($proprietario->rntrc));
        $prop->xNome = $this->retiraAcentos($proprietario->nome_proprietario);
        $prop->IE = $this->retiraPontuacoes($proprietario->ie);
        $prop->UF = $proprietario->uf_proprietario->value;
        $prop->tpProp = explode('_', $proprietario->tipo_proprietario->name)[1];
        $veicTracao->prop = $prop;
        $mdfe->tagveicTracao($veicTracao);

        if (count($transporte->reboques) > 0) {
            foreach ($transporte->reboques as $rbq) {
                //Dados dos Reboques
                $veicReboque = new \stdClass();
                $veicReboque->cInt = $rbq->reboque->id;
                $veicReboque->placa = $this->retiraPontuacoes($rbq->reboque->placa);
                $veicReboque->RENAVAM = $this->retiraPontuacoes($rbq->reboque->renavam);
                $veicReboque->tara = intval($rbq->reboque->tara);
                $veicReboque->capKG = intval($rbq->reboque->capacidade);
                $veicReboque->capM3 = intval($rbq->reboque->capacidade_m3);
                $veicReboque->tpCar = explode('_', $rbq->reboque->tipo_carroceria->name)[1];
                $veicReboque->UF = $rbq->reboque->uf_veiculo->value;

                //Identificação do Proprietário do Reboque
                $prop = new \stdClass();
                $proprietario = $rbq->reboque->proprietario;
                if (strlen($proprietario->cpf_cnpj) == 14) {
                    $prop->CPF = $this->retiraPontuacoes($proprietario->cpf_cnpj);
                } else {
                    $prop->CNPJ = $this->retiraPontuacoes($proprietario->cpf_cnpj);
                }
                $prop->RNTRC = $this->retiraPontuacoes($this->retiraAcentos($proprietario->rntrc));
                $prop->xNome = $this->retiraAcentos($proprietario->nome_proprietario);
                $prop->IE = $this->retiraPontuacoes($proprietario->ie);
                $prop->UF = $proprietario->uf_proprietario->value;
                $prop->tpProp = explode('_', $proprietario->tipo_proprietario->name)[1];
                $veicReboque->prop = $prop;
                $mdfe->tagveicReboque($veicReboque);
            }
        }

        //Informações dos lacres de um trasnporte especial
        if ($transporte->numeroLacre) {
            $lacRodo = new \stdClass();
            $lacRodo->nLacre = $transporte->numeroLacre;
            $mdfe->taglacRodo($lacRodo);
        }

        //Informações dos Municípios de Descarregamento
        foreach ($transporte->notas as $nota) {
            $infMunDescarga = new \stdClass();
            $infMunDescarga->cMunDescarga = $this->retiraPontuacoes($nota->codMun);
            $infMunDescarga->xMunDescarga = $nota->municipio;
            $mdfe->taginfMunDescarga($infMunDescarga);
        }

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

        if ($transporte->mdfes) {
            //Informações de Transporte da MDFe
            $infMDFe = new \stdClass();
            $infMDFe->chMDFe = '0';

            //Informações das Unidades de Transporte (Carreta/Reboque/Vagão)
            $unidades = [];
            $unidades[] = $transporte->veiculoTracao;
            foreach ($transporte->reboques as $rbq) {
                $unidades[] = $rbq->reboque;
            }
            foreach ($unidades as $un) {
                $stdinfUnidTransp = new \stdClass();
                $stdinfUnidTransp->tpUnidTransp = $un->tipo_veiculo->value == 'Tração' ? '1' : '2';
                $stdinfUnidTransp->idUnidTransp = $this->retiraPontuacoes($un->placa);
            }

            // IMPLEMENTAR EM UM FURUTO PRÓXIMO
            // if ($transporte->lacres) {
            //Lacres das Unidades de Transporte
            $stdlacUnidTransp = new \stdClass();
            $stdlacUnidTransp->nLacre = [$transporte->numeroLacre];

            $stdinfUnidTransp->lacUnidTransp = $stdlacUnidTransp;

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
            // }

            //Transporte de produtos classificados pela ONU como perigosos
            // if ($transporte->prodsPrerigosos) {
            $stdperi = new \stdClass();
            $stdperi->nONU = '1234';
            $stdperi->xNomeAE = 'testeNome';
            $stdperi->xClaRisco = 'testeClaRisco';
            $stdperi->grEmb = 'teste';
            $stdperi->qTotProd = '1';
            $stdperi->qVolTipo = '1';
            $infMDFe->peri = [$stdperi];
            // }
            $mdfe->taginfMDFeTransp($infMDFe);
        }

        //Falta ajeitar daqui
        $tot = new \stdClass();
        $tot->qCTe = '0';
        $tot->qNFe = count($transporte->notas);
        $tot->qMDFe = '1';
        $tot->vCarga = $transporte->valor_total;
        $tot->cUnid = '01';
        $tot->qCarga = $transporte->peso;
        $mdfe->tagtot($tot);

        $prodPred = new \stdClass();
        $prodPred->tpCarga = explode('_', $transporte->tipo_carga->name)[1];
        $prodPred->xProd = $this->retiraAcentos($transporte->prodPred->carga_predominante);
        $prodPred->cEAN = $transporte->prodPred->codigo_gtin;
        $prodPred->NCM = $this->retiraPontuacoes($transporte->prodPred->ncm);

        $localCarrega = new \stdClass();
        $localCarrega->CEP = '00000000';
        $localCarrega->latitude = $transporte->prodPred->lat_carregamento;
        $localCarrega->longitude = $transporte->prodPred->lon_carregamento;

        $localDescarrega = new \stdClass();
        $localDescarrega->CEP = '00000000';
        $localDescarrega->latitude = $transporte->prodPred->lat_descarregamento;
        $localDescarrega->longitude = $transporte->prodPred->lon_descarregamento;

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
        $infAdic->infCpl = $transporte->info_contribuinte;
        $infAdic->infAdFisco = $transporte->info_fisco;
        $mdfe->taginfAdic($infAdic);
        try {
            $arr = [
                'xml' => $mdfe->getXML(),
                'chave' => $mdfe->getChave(),
                'nMDF' => $stdIde->nMDF,
            ];
            return $arr;
        } catch (\Exception $e) {
            return [
                'erros_xml' => $mdfe->getErrors(),
            ];
        }
    }

    public function sign($xml)
    {
        return $this->tools->signMDFe($xml);
    }

    public function transmitir($signXml, $chave, $caminho)
    {
        try {
            $idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
            $resp = $this->tools->sefazEnviaLote([$signXml], $idLote);
            $st = new Standardize();
            $std = $st->toStd($resp);
            sleep(2);
            if ($std->cStat != 103) {

                return [
                    'erro' => "[$std->cStat] - $std->xMotivo",
                ];

            }
            $recibo = $std->infRec->nRec;
            $protocolo = $this->tools->sefazConsultaRecibo($recibo);
            sleep(2);
            $xml = Complements::toAuthorize($signXml, $protocolo);
            if (!File::exists(public_path($caminho . '/'))) {
                File::makeDirectory(public_path($caminho . '/'), 755, true, true);
            }
            file_put_contents(public_path($caminho . '/') . $chave . '.xml', $xml);
            return [
                'sucesso' => $recibo,
            ];
        } catch (\Exception $e) {
            return [
                'erro' => $e->getMessage(),
            ];
        }
    }

    private function retiraAcentos($texto)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/"), explode(" ", "a A e E i I o O u U n N c"), $texto);
    }

    public function format($number, $dec = 2)
    {
        return number_format((float) $number, $dec, ".", "");
    }

    public function retiraPontuacoes($texto)
    {
        $texto = str_replace(".", "", $texto);
        $texto = str_replace("/", "", $texto);
        $texto = str_replace("-", "", $texto);
        $texto = str_replace(" ", "", $texto);
        return $texto;
    }
}
