<?php

namespace App\Http\Controllers\Traits;

use App\Exceptions\LimitExceededException;
use App\Exceptions\MalformedXmlException;
use App\Services\NFCeService;
use Exception;
use Illuminate\Support\Facades\DB;

trait EnviaNFCe
{
    /**
     * Contém a lógica de negócio para enviar uma NFC-e.
     * Retorna um objeto com o status e a mensagem do resultado.
     *
     * @param int $id
     * @return object
     */
    protected function _enviarNFCePeloId($id): object
    {
        try {
            DB::beginTransaction();
            $cupom = $this->cupomService->getCupom($id);
            $nfceService = $this->makeNFCeService($cupom->empresa);
            $this->empresaServices->incrementLastNFCe($cupom->empresa_id);
            $resultXml = $nfceService->generateXml($cupom, $cupom->empresa);
            $this->cupomService->updateCoupon($cupom);
            NFCeService::createNFCe($resultXml, $cupom->id, $cupom->empresa);
            foreach ($cupom->itens as $item) {
                $this->estoqueService->out($item->produto_id, $item->qtde);
            }
            DB::commit();

            // SUCESSO: Retorna um objeto de sucesso
            return (object) ['status' => 'success', 'message' => 'Cupom foi enviado com sucesso!'];

        } catch (LimitExceededException | MalformedXmlException $e) {
            DB::rollback();
            $this->cupomService->rejectedCoupon($id);
            // ERRO CONHECIDO: Retorna um objeto de aviso
            return (object) ['status' => 'warning', 'message' => $e->getMessage()];

        } catch (Exception $e) {
            DB::rollback();
            // ERRO GENÉRICO: Retorna um objeto de erro
            return (object) ['status' => 'error', 'message' => 'Ocorreu um erro inesperado: ' . $e->getMessage()];
        }
    }
}