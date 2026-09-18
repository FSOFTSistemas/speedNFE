<?php

namespace App\Http\Controllers\Traits;

use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FluxoDeCaixaService;
use App\Services\NFeService;
use App\Services\PedidosService;
use App\Utils\FormatationUtil;
use App\Utils\NFeErroUtil;
use Exception;
use Illuminate\Support\Facades\DB;
use NFePHP\Common\Exception\ValidatorException;

trait EnviaNFe
{
    private function makeNFeService($empresa)
    {
        $config = [
            'atualizacao' => date('Y-m-d h:i:s'),
            'tpAmb' => (int) $empresa->ambiente,
            'razaosocial' => $empresa->razao,
            'siglaUF' => $empresa->endereco->uf,
            'cnpj' => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
            'schemes' => 'PL_010_V1.30',
            'versao' => '4.00',
            'tokenIBPT' => 'AAAAAAA',
            'CSC' => $empresa->csc,
            'CSCid' => '00000'.$empresa->idCsc,
        ];

        return new NFeService($config, $empresa);
    }

    /**
     * Contém a lógica de negócio para gerar, assinar e transmitir uma NFe.
     * Extraído de PedidosController::enviarNFe() para ser reaproveitado
     * tanto pela tela web quanto pela API, sem duplicar a lógica fiscal.
     *
     * Retorna um objeto {status, type, title, message}: `status` é
     * 'success'|'warning'|'error' (uso simples, ex.: mapear pra HTTP na API);
     * `type` identifica a origem exata do resultado, pra quem consome poder
     * reconstruir a UX original (Alert com título vs. flash simples).
     */
    protected function _enviarNFePeloId(int $id, PedidosService $pedidoService, EmpresasService $empresaServices, EstoquesService $estoqueService, FluxoDeCaixaService $fluxoCaixaService): object
    {
        try {
            DB::beginTransaction();
            $venda = $pedidoService->buscarPedido($id);
            $empresa = $empresaServices->buscarEmpresa($venda->empresa_id);
            $nfe_service = $this->makeNFeService($empresa);

            if ($venda->estado->value == 'Rejeitado' || $venda->estado->value == 'Pendente') {
                $result = $nfe_service->gerarXml($venda, $empresa);

                if (! isset($result['erros_xml'])) {
                    $signed = $nfe_service->sign($result['xml']);
                    $resultado = $nfe_service->transmitir($signed, $result['chave'], $venda->id);

                    if (isset($resultado['sucesso'])) {
                        DB::beginTransaction();

                        $venda->chave = $result['chave'];
                        $venda->status = 1;
                        $venda->estado = 'Autorizado';
                        $venda->numero_nfe = $result['nNf'];
                        $venda->save();
                        $empresa->update(['ultimaNFe' => $empresa->ultimaNFe + 1]);

                        if ($venda->tpNF) {
                            foreach ($venda->itens as $item) {
                                $estoqueService->out($item->produto_id, $item->qtde);
                            }
                            $fluxoCaixaService->registrarEntradaAutomatica(
                                $venda->empresa_id,
                                $venda->total,
                                'Venda NFe #'.$venda->numero_nfe.($venda->cliente ? ' - '.$venda->cliente->nome : ''),
                                $venda->data,
                                'NFe',
                                $venda->id
                            );
                        } else {
                            foreach ($venda->itens as $item) {
                                $estoqueService->reverseStock($item->produto_id, $item->qtde);
                            }
                        }

                        DB::commit();

                        return (object) [
                            'status' => 'success',
                            'type' => 'success',
                            'title' => null,
                            'message' => 'Nota enviada com sucesso',
                        ];
                    }

                    DB::beginTransaction();

                    $venda->status = 3;
                    $venda->estado = 'Rejeitado';
                    $venda->save();

                    DB::commit();

                    return (object) [
                        'status' => 'warning',
                        'type' => 'rejeitado_sefaz',
                        'title' => 'A NFe foi rejeitada pela SEFAZ',
                        'message' => NFeErroUtil::formatar($resultado['erro']),
                    ];
                }

                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                return (object) [
                    'status' => 'error',
                    'type' => 'erro_xml',
                    'title' => 'Não foi possível gerar a NFe',
                    'message' => NFeErroUtil::formatar($result['erros_xml']),
                ];
            }

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return (object) [
                'status' => 'error',
                'type' => 'estado_invalido',
                'title' => null,
                'message' => 404,
            ];
        } catch (ValidatorException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return (object) [
                'status' => 'warning',
                'type' => 'validator_exception',
                'title' => null,
                'message' => $e->getMessage(),
            ];
        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return (object) [
                'status' => 'error',
                'type' => 'exception',
                'title' => null,
                'message' => 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e->getMessage(),
            ];
        }
    }
}
