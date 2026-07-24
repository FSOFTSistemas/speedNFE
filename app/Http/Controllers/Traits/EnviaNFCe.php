<?php

namespace App\Http\Controllers\Traits;

use App\Exceptions\LimitExceededException;
use App\Exceptions\MalformedXmlException;
use App\Services\NFCeService;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Utils\FormatationUtil;

// Dependências que serão injetadas via controller
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FluxoDeCaixaService;

trait EnviaNFCe
{

    private function makeNFCeService($empresa)
    {
        $config = [
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int) $empresa->ambiente,
            "razaosocial" => $empresa->razao,
            "siglaUF" => $empresa->endereco->uf,
            "cnpj" => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "tokenIBPT" => "AAAAAAA",
            "CSC" => $empresa->csc,
            "CSCid" => "00000" . $empresa->idCsc,
            "proxyConf"   => [
                "proxyIp"   => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];
        return new NFCeService($config, $empresa);
    }
    /**
     * Contém a lógica de negócio para enviar uma NFC-e.
     * Retorna um objeto com o status e a mensagem do resultado.
     *
     * @param int $id
     * @param CupomService $cupomService
     * @param EmpresasService $empresaServices
     * @param EstoquesService $estoqueService
     * @param FluxoDeCaixaService $fluxoCaixaService
     * @return object
     */
    protected function _enviarNFCePeloId(int $id, CupomService $cupomService, EmpresasService $empresaServices, EstoquesService $estoqueService, FluxoDeCaixaService $fluxoCaixaService): object
    {
        try {
            DB::beginTransaction();
            // Agora usa as variáveis recebidas como parâmetro, não mais $this->
            $cupom = $cupomService->getCupom($id);
            $nfceService = $this->makeNFCeService($cupom->empresa); // Este método está no próprio trait, então o $this continua
            $empresaServices->incrementLastNFCe($cupom->empresa_id);
            $resultXml = $nfceService->generateXml($cupom, $cupom->empresa);
            $cupomService->updateCoupon($cupom);
            NFCeService::createNFCe($resultXml, $cupom->id, $cupom->empresa);
            $fluxoCaixaService->registrarEntradaAutomatica(
                $cupom->empresa_id,
                $cupom->total,
                'Venda NFCe #' . $cupom->nroCupom . ($cupom->cliente ? ' - ' . $cupom->cliente->nome : ''),
                $cupom->data,
                'NFCe',
                $cupom->id
            );
            DB::commit();

            // SUCESSO: Retorna um objeto de sucesso
            return (object) ['status' => 'success', 'message' => 'Cupom foi enviado com sucesso!'];

        } catch (LimitExceededException | MalformedXmlException $e) {
            DB::rollback();
            $cupomService->rejectedCoupon($id);
            // ERRO CONHECIDO: Retorna um objeto de aviso
            return (object) ['status' => 'warning', 'message' => $e->getMessage()];

        } catch (Exception $e) {
            DB::rollback();
            // ERRO GENÉRICO: Retorna um objeto de erro
            return (object) ['status' => 'error', 'message' => 'Ocorreu um erro inesperado: ' . $e->getMessage()];
        }
    }
}