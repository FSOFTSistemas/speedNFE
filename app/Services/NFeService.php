<?php
namespace App\Services;

use Exception;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use Illuminate\Support\Facades\File;
use NFePHP\NFe\Complements;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

class NFeService{

	private $tools;

	public function __construct($config, $emitente){
		try{
			$this->tools = new Tools(json_encode($config), Certificate::readPfx(file_get_contents(asset('storage/'.$emitente->razao.'.pfx')), $emitente->senhaCertificado));
			// $this->tools->model(55);
		}catch(Exception $e){
			dd($e->getMessage());
		}

	}

	public function gerarXml($venda, $emitente){
		// $array = [];
		// $array['venda'] = $venda;
		// $array['venda_cliente'] = $venda->cliente;
		// $array['venda_endereco_cliente'] = $venda->endereco_cliente;
		// $array['venda_itens'] = $venda->itens;
		// $array['venda_itens_produtos'] = $venda->produtos;
		// $array['venda_fatura'] = $venda->fatura;
		// $array['venda_fatura_forma'] = $array['venda_fatura'][0]->forma_pag;
		// $array['emitente'] = $emitente;
		// $array['emitente_endereco'] = $emitente->endereco;



		// return $array;
		$nfe = new Make();
		$stdInNFe = new \stdClass();
		$stdInNFe->versao = '4.00';
		$stdInNFe->Id = null;
		$stdInNFe->pk_nItem = '';
		$infNFe = $nfe->taginfNFe($stdInNFe);

		$numeroNFe = $emitente->ultimaNFe+1;
		$stdIde = new \stdClass();
		$stdIde->cUF = \App\Models\Empresa::getCUF($emitente->endereco->uf);
		$stdIde->cNF = rand(11111,99999);
		$stdIde->natOp = $venda->cfop->natureza;

		$stdIde->mod = 55;
		$stdIde->serie = $emitente->serie;
		$stdIde->nNF = (int)$numeroNFe;
		$stdIde->dhEmi = date("Y-m-d\TH:i:sP");
		$stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
		$stdIde->tpNF = 1;

		$stdIde->idDest = $emitente->endereco->uf != $venda->endereco_cliente->uf ? 2 : 1;
		$stdIde->cMunFG = $emitente->endereco->codigoIBGE;
		$stdIde->tpImp = 1;
		$stdIde->tpEmis = 1;
		$stdIde->cDV = 0;
		$stdIde->tpAmb = $emitente->ambiente;
		$stdIde->finNFe = 1;
		$stdIde->indFinal = 1;
		$stdIde->indPres = 1;
// 		$stdIde->indIntermed = 0;
		$stdIde->procEmi = '0';
		$stdIde->verProc = '3.10.31';
		$tagide = $nfe->tagide($stdIde);

		//TAG EMITENTE
		$stdEmit = new \stdClass();
		$stdEmit->xNome = $emitente->razao;
		$stdEmit->xFant = $emitente->fantasia;

		$ie = str_replace(".", "", $emitente->rg_ie);
		$ie = str_replace("/", "", $ie);
		$ie = str_replace("-", "", $ie);
		$stdEmit->IE = $ie;

		$stdEmit->CRT = 1; // Simples nacional
		$cnpj = str_replace(".", "", $emitente->cpf_cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);

		if(strlen($cnpj) == 14){
			$stdEmit->CNPJ = $cnpj;
		}else{
			$stdEmit->CPF = $cnpj;
		}
		$emit = $nfe->tagemit($stdEmit);

		// ENDERECO EMITENTE
		$stdEnderEmit = new \stdClass();
		$stdEnderEmit->xLgr = $this->retiraAcentos($emitente->endereco->rua);
		$stdEnderEmit->nro = $emitente->endereco->numero;
		$stdEnderEmit->xCpl = $this->retiraAcentos($emitente->endereco->complemento);

		$stdEnderEmit->xBairro = $this->retiraAcentos($emitente->endereco->bairro);
		$stdEnderEmit->cMun = $emitente->endereco->codigoIBGE;
		$stdEnderEmit->xMun = $this->retiraAcentos($emitente->endereco->cidade);
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
		$pFisica = false;
		$stdDest->xNome = $this->retiraAcentos($venda->cliente->nome);

		// return $venda->cliente;

		if($venda->cliente->contribuinte){
			if($venda->cliente->rg_ie == 'ISENTO'){
				$stdDest->indIEDest = "2";
			}else{
				$stdDest->indIEDest = "1";
			}

		}else{
			$stdDest->indIEDest = "9";
		}

		$cnpj_cpf = str_replace(".", "", $venda->cliente->cpf_cnpj);
		$cnpj_cpf = str_replace("/", "", $cnpj_cpf);
		$cnpj_cpf = str_replace("-", "", $cnpj_cpf);

		if(strlen($cnpj_cpf) == 14){
			$stdDest->CNPJ = $cnpj_cpf;
			$ie = str_replace(".", "", $venda->cliente->rg_ie);
			$ie = str_replace("/", "", $ie);
			$ie = str_replace("-", "", $ie);
			$stdDest->IE = $ie;
		}
		else{
			// $stdDest->CPF = $cnpj_cpf;
			$stdDest->CPF = $cnpj_cpf;
			$ie = str_replace(".", "", $venda->cliente->rg_ie);
			$ie = str_replace("/", "", $ie);
			$ie = str_replace("-", "", $ie);
			if(strtolower($ie) != "isento" && $venda->cliente->contribuinte)
				$stdDest->IE = $ie;

		}

		$dest = $nfe->tagdest($stdDest);

		//ENDEREÇO DESTINATÁRIO

		$stdEnderDest = new \stdClass();
		$stdEnderDest->xLgr = $this->retiraAcentos($venda->endereco_cliente->rua);
		$stdEnderDest->nro = $this->retiraAcentos($venda->endereco_cliente->numero);
		$stdEnderDest->xCpl = $this->retiraAcentos($venda->endereco_cliente->complemento);
		$stdEnderDest->xBairro = $this->retiraAcentos($venda->endereco_cliente->bairro);

		$telefone = $venda->cliente->celular;
		$telefone = str_replace("(", "", $telefone);
		$telefone = str_replace(")", "", $telefone);
		$telefone = str_replace("-", "", $telefone);
		$telefone = str_replace(" ", "", $telefone);
		$stdEnderDest->fone = $telefone;

		$stdEnderDest->cMun = $venda->endereco_cliente->codigoIBGE;
		$stdEnderDest->xMun = $this->retiraAcentos($venda->endereco_cliente->cidade);
		$stdEnderDest->UF = $venda->endereco_cliente->uf;

		$cep = str_replace("-", "", $venda->endereco_cliente->cep);
		$cep = str_replace(".", "", $cep);
		$stdEnderDest->CEP = $cep;
		$stdEnderDest->cPais = "1058";
		$stdEnderDest->xPais = "BRASIL";
		$enderDest = $nfe->tagenderDest($stdEnderDest);

		// return $venda->itens;

		//ITENS DA NFE
		foreach($venda->itens as $key => $i){
			//TAG DE PRODUTO
			$stdProd = new \stdClass();
			$stdProd->item = $key+1;

			$cod = $this->validate_EAN13Barcode($i->produto->codigo);

			$stdProd->cEAN = $cod ? $i->produto->codigo : 'SEM GTIN';
			$stdProd->cEANTrib = $cod ? $i->produto->codigo : 'SEM GTIN';
			$stdProd->cProd = $i->produto->id;
			$stdProd->xProd = $this->retiraAcentos($i->produto->produto);

			$ncm = $i->produto->ncm;
			$ncm = str_replace(".", "", $ncm);
			$stdProd->NCM = $ncm;

			$stdProd->CFOP = $i->cfop->cfop;

			$stdProd->uCom = $i->produto->un;
			$stdProd->qCom = $i->qtde;
			$stdProd->vUnCom = $this->format($i->unitario);
			$stdProd->vProd = $this->format(($i->qtde * $i->unitario));
			$stdProd->uTrib = $i->produto->un;
			$stdProd->qTrib = $i->qtde;
			$stdProd->vUnTrib = $this->format($i->unitario);
			$stdProd->indTot = 1;
			$prod = $nfe->tagprod($stdProd);

			$stdImposto = new \stdClass();
			$stdImposto->item = $key+1;
			$imposto = $nfe->tagimposto($stdImposto);

			//ICMS
			$stdICMS = new \stdClass();
			$stdICMS->item = $key+1;
			$stdICMS->orig = 0;
			$stdICMS->CSOSN = $i->produto->cst_csosn;
			$stdICMS->modBC = 0;
			$stdICMS->vBC = $stdProd->vProd;
			$stdICMS->pICMS = $this->format($i->produto->icms);
			$stdICMS->vICMS = $stdICMS->vBC * ($stdICMS->pICMS/100);
			$stdICMS->pCredSN = $this->format($i->produto->icms);
			$stdICMS->vCredICMSSN = $this->format($i->produto->icms);
			$ICMS = $nfe->tagICMSSN($stdICMS);

			//PIS
			$stdPIS = new \stdClass();
			$stdPIS->item = $key+1;
			$stdPIS->CST = $i->produto->cst_pis;
			$stdPIS->vBC = $this->format($i->produto->pis) > 0 ? $stdProd->vProd : 0.00;
			$stdPIS->pPIS = $this->format($i->produto->pis);
			$stdPIS->vPIS = $this->format(($stdProd->vProd) * ($i->produto->pis/100));
			$PIS = $nfe->tagPIS($stdPIS);

			//COFINS
			$stdCOFINS = new \stdClass();
			$stdCOFINS->item = $key+1;
			$stdCOFINS->CST = $i->produto->cst_cofins;
			$stdCOFINS->vBC = $this->format($i->produto->cofins) > 0 ? $stdProd->vProd : 0.00;
			$stdCOFINS->pCOFINS = $this->format($i->produto->cofins);
			$stdCOFINS->vCOFINS = $this->format(($stdProd->vProd) *
				($i->produto->cofins/100));
			$COFINS = $nfe->tagCOFINS($stdCOFINS);

			//IPI
			$std = new \stdClass();
			$std->item = $key+1;
			$std->cEnq = '999';
			$std->CST = $i->produto->ipi;
			$std->vBC = $this->format($i->produto->ipi) > 0 ? $stdProd->vProd : 0.00;
			$std->pIPI = $this->format($i->produto->ipi);
			$std->vIPI = $stdProd->vProd * $this->format(($i->produto->ipi/100));
			$nfe->tagIPI($std);

		}

		$stdTransp = new \stdClass();
		$stdTransp->modFrete = '9';

		$transp = $nfe->tagtransp($stdTransp);

		//TOTALIZADOR NFE

		$stdICMSTot = new \stdClass();
		$stdICMSTot->vProd = 0.00;
		$stdICMSTot->vBC = 0.00;
		$stdICMSTot->vICMS = 0.00;
		$stdICMSTot->vICMSDeson = 0.00;
		$stdICMSTot->vBCST = 0.00;
		$stdICMSTot->vST = 0.00;
		$stdICMSTot->vFrete = 0.00;
		$stdICMSTot->vSeg = 0.00;
		$stdICMSTot->vDesc = $this->format($venda->desconto);
		$stdICMSTot->vII = 0.00;
		$stdICMSTot->vIPI = 0.00;
		$stdICMSTot->vPIS = 0.00;
		$stdICMSTot->vCOFINS = 0.00;
		$stdICMSTot->vOutro = 0.00;
		$stdICMSTot->vTotTrib = 0.00;
		$stdICMSTot->vNF = $this->format($venda->total);

		//DUPLICATAS
		$stdFat = new \stdClass();
		$stdFat->nFat = (int)$numeroNFe;
		$stdFat->vOrig = $this->format($venda->subtotal);
		$stdFat->vDesc = $this->format($venda->desconto);
		$stdFat->vLiq = $this->format($venda->total);
		if($venda->tipo_pagamento != '90'){
			$fatura = $nfe->tagfat($stdFat);
		}

		foreach($venda->fatura as $key => $fat){
			$stdDup = new \stdClass();
			$stdDup->nDup = '00'.($key+1);
			$stdDup->dVenc = $fat->vencimento;
			$stdDup->vDup =  $this->format($fat->valor);

			$nfe->tagdup($stdDup);

			$stdPag = new \stdClass();
			$pag = $nfe->tagpag($stdPag);

			$stdDetPag = new \stdClass();
			if($fat->forma_pag->descricao == "Dinheiro"){
				$stdDetPag->tPag = '01';
			}else if($fat->forma_pag->descricao == 'Cheque'){
				$stdDetPag->tPag = '02';
			}else if($fat->forma_pag->descricao == 'Cartão de Crédito'){
				$stdDetPag->tPag = '03';
			}else if($fat->forma_pag->descricao == 'Cartão de Débito'){
				$stdDetPag->tPag = '04';
			}else if($fat->forma_pag->descricao == 'Crédito Loja'){
				$stdDetPag->tPag = '05';
			}else if($fat->forma_pag->descricao == 'Vale Alimentação'){
				$stdDetPag->tPag = '10';
			}else if($fat->forma_pag->descricao == 'Vale Refeição'){
				$stdDetPag->tPag = '11';
			}else if($fat->forma_pag->descricao == 'Vale Presente'){
				$stdDetPag->tPag = '12';
			}else if($fat->forma_pag->descricao == 'Vale Combustível'){
				$stdDetPag->tPag = '13';
			}else if($fat->forma_pag->descricao == 'Duplicata Mercantil'){
				$stdDetPag->tPag = '14';
			}else if($fat->forma_pag->descricao == 'Boleto Bancário'){
				$stdDetPag->tPag = '15';
			}else if($fat->forma_pag->descricao == 'Depósito Bancário'){
				$stdDetPag->tPag = '16';
			}else if($fat->forma_pag->descricao == 'Pagamento Instantâneo (PIX)'){
				$stdDetPag->tPag = '17';
			}else if($fat->forma_pag->descricao == 'Sem pagamento'){
				$stdDetPag->tPag = '90';
			}else if($fat->forma_pag->descricao == 'Outros'){
				$stdDetPag->tPag = '99';
			}
			$stdDetPag->vPag = $fat->forma_pag->descricao != 'Sem pagamento' ? $this->format($fat->valor) : 0.00;
			$stdDetPag->indPag = 1;
			$stdDetPag->vTroco = 0;
			if($fat->forma_pag->descricao == 'Cartão de Crédito' || $fat->forma_pag->descricao == 'Cartão de Débito'){
				$stdDetPag->tpIntegra = '2';
			}
			$detPag = $nfe->tagdetPag($stdDetPag);
		}


		//TAG AUTORIZADOR XML VARIAVEL NO ARQUIVO .ENV, ESTADO DA BAHIA OBRIGATORIO
		if(getenv('AUT_XML') != ''){
			$std = new \stdClass();
			$cnpj = getenv('AUT_XML');
			$cnpj = str_replace(".", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);
			$std->CNPJ = $cnpj;
			$aut = $nfe->tagautXML($std);
		}

		//TAG RESPONSAVEL TECNICO
		$std = new \stdClass();
		$std->CNPJ = getenv('RESP_CNPJ'); //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
		$std->xContato= getenv('RESP_NOME'); //Nome da pessoa a ser contatada
		$std->email = getenv('RESP_EMAIL'); //E-mail da pessoa jurídica a ser contatada
		$std->fone = getenv('RESP_FONE');
		$nfe->taginfRespTec($std);

		try{
			$nfe->montaNFe();
			$arr = [
				'chave' => $nfe->getChave(),
				'xml' => $nfe->getXML(),
				'nNf' => $stdIde->nNF
			];

			return $arr;
		}catch(\Exception $e){
			return [
				'erros_xml' => $nfe->getErrors()
			];
		}
	}

	private function validate_EAN13Barcode($ean)
	{

		$sumEvenIndexes = 0;
		$sumOddIndexes  = 0;

		$eanAsArray = array_map('intval', str_split($ean));

		if (!$this->has13Numbers($eanAsArray)) {
			return false;
		};

		for ($i = 0; $i < count($eanAsArray)-1; $i++) {
			if ($i % 2 === 0) {
				$sumOddIndexes  += $eanAsArray[$i];
			} else {
				$sumEvenIndexes += $eanAsArray[$i];
			}
		}

		$rest = ($sumOddIndexes + (3 * $sumEvenIndexes)) % 10;

		if ($rest !== 0) {
			$rest = 10 - $rest;
		}

		return $rest === $eanAsArray[12];
	}

	private function has13Numbers(array $ean)
	{
		return count($ean) === 13;
	}

	private function retiraAcentos($texto){
		return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/", "/(ç)/"),explode(" ","a A e E i I o O u U n N c"),$texto);
	}

	public function format($number, $dec = 2){
		return number_format((float) $number, $dec, ".", "");
	}

	public function sign($xml){
		return $this->tools->signNFe($xml);
	}

	public function transmitir($signXml, $chave, $caminho){
		try{
			$idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
			$resp = $this->tools->sefazEnviaLote([$signXml], $idLote);

			$st = new Standardize();
			$std = $st->toStd($resp);
			sleep(2);
			if ($std->cStat != 103) {

				return [
					'erro' => "[$std->cStat] - $std->xMotivo"
				];

			}
			$recibo = $std->infRec->nRec;
			$protocolo = $this->tools->sefazConsultaRecibo($recibo);
			sleep(3);
			try {
				$xml = Complements::toAuthorize($signXml, $protocolo);
				if(!File::exists(public_path($caminho.'/'))){
					File::makeDirectory(public_path($caminho.'/'), 755, true, true);
				}
				file_put_contents(public_path($caminho.'/').$chave.'.xml',$xml);
				return [
					'sucesso' => $recibo
				];
				// $this->printDanfe($xml);
			} catch (\Exception $e) {
				return [
					'erro' => $e->getMessage()
				];
			}

		} catch(\Exception $e){
			return [
				'erro' => $e->getMessage()
			];
		}
	}

	public function inutilizarNum($serie, $numI, $numF, $xJust, $caminho){
		try{
			$response = $this->tools->sefazInutiliza($serie, $numI, $numF, $xJust);
			sleep(2);
			$stdCl = new Standardize($response);
			$std = $stdCl->toStd();
			$arr = $stdCl->toArray();
			$json = $stdCl->toJson();


			if ($std->infInut->cStat == 102 || $std->infInut->cStat == 563){
				$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
				if(!File::exists(public_path($caminho.'/'))){
					File::makeDirectory(public_path($caminho.'/'), 755, true, true);
				}
				file_put_contents(public_path($caminho.'/').$std->infInut->attributes->Id.'.xml',$xml);


				return $json;
			} else {
				['erro' => true, 'data' => $arr];
			}

		} catch (\Exception $e){
			return ['erro' => true, 'data' => $e->getMessage()];
		}
	}

	public function cartaCorrecao($venda, $justificativa, $caminho){
		try {

			$chave = $venda->chave;
			$xCorrecao = $justificativa;
			$nSeqEvento = $venda->sequencia_evento+1;
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
					if(!File::exists(public_path($caminho.'/'))){
						File::makeDirectory(public_path($caminho.'/'), 755, true, true);
					}
					file_put_contents(public_path($caminho.'/').$chave.'.xml',$xml);

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

	public function cancelar($venda, $justificativa, $caminho){
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
				if ($cStat == '101' || $cStat == '135' || $cStat == '155' ) {
					$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
					if(!File::exists(public_path($caminho.'/'))){
						File::makeDirectory(public_path($caminho.'/'), 755, true, true);
					}
					file_put_contents(public_path($caminho.'/').$chave.'.xml',$xml);

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