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

    /**
     * O retorno de eventos (CCe/cancelamento) do NFeService ora vem como o
     * array do XML padronizado, ora como a mensagem de uma exceção (string),
     * dependendo de onde a falha ocorreu. Aqui extraímos o xMotivo quando
     * disponível, sem arriscar acessar índice de array numa string.
     */
    protected function extrairMensagemEventoNFe($data)
    {
        if (is_array($data)) {
            return $data['retEvento']['infEvento']['xMotivo'] ?? $data;
        }

        return $data;
    }

    /**
     * Cancela a NFe na SEFAZ e reflete o cancelamento localmente (estorna
     * estoque e fluxo de caixa). Extraído de PedidosController::cancelarNFe()
     * para ser reaproveitado tanto pela tela web quanto pela API.
     */
    protected function _cancelarNFePeloId(int $id, string $justificativa, PedidosService $pedidoService, EmpresasService $empresaServices, EstoquesService $estoqueService, FluxoDeCaixaService $fluxoCaixaService): object
    {
        try {
            $venda = $pedidoService->buscarPedido($id);

            if (! $venda) {
                return (object) ['status' => 'error', 'type' => 'not_found', 'title' => null, 'message' => 'Nota não encontrada.'];
            }

            $empresa = $empresaServices->buscarEmpresa($venda->empresa_id);

            if ($empresa === null) {
                return (object) ['status' => 'error', 'type' => 'emitente_nao_configurado', 'title' => null, 'message' => 'Configure o emitente'];
            }

            $nfe_service = $this->makeNFeService($empresa);
            $nfe = $nfe_service->cancelar($venda, $justificativa);

            if (! isset($nfe['erro'])) {
                $venda->status = 0;
                $venda->estado = 'Cancelado';
                $venda->total = 0;
                $venda->save();

                foreach ($venda->itens as $item) {
                    $estoqueService->reverseStock($item->produto_id, $item->qtde);
                }
                $fluxoCaixaService->estornarPorOrigem('NFe', $venda->id);

                return (object) ['status' => 'success', 'type' => 'success', 'title' => null, 'message' => 'Nota cancelada com sucesso'];
            }

            return (object) [
                'status' => 'error',
                'type' => 'rejeitado_sefaz',
                'title' => null,
                'message' => NFeErroUtil::formatar($this->extrairMensagemEventoNFe($nfe['data'])),
            ];
        } catch (ValidatorException $e) {
            return (object) ['status' => 'warning', 'type' => 'validator_exception', 'title' => null, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            return (object) ['status' => 'error', 'type' => 'exception', 'title' => null, 'message' => 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e->getMessage()];
        }
    }

    /**
     * Emite uma Carta de Correção Eletrônica para a NFe. Extraído de
     * PedidosController::cartaCorrecao() para ser reaproveitado tanto pela
     * tela web quanto pela API.
     */
    protected function _cartaCorrecaoPeloId(int $id, string $justificativa, PedidosService $pedidoService, EmpresasService $empresaServices): object
    {
        try {
            $venda = $pedidoService->buscarPedido($id);

            if (! $venda) {
                return (object) ['status' => 'error', 'type' => 'not_found', 'title' => null, 'message' => 'Nota não encontrada.'];
            }

            $empresa = $empresaServices->buscarEmpresa($venda->empresa_id);

            if ($empresa === null) {
                return (object) ['status' => 'error', 'type' => 'emitente_nao_configurado', 'title' => null, 'message' => 'Configure o emitente'];
            }

            $nfe_service = $this->makeNFeService($empresa);
            $result = $nfe_service->cartaCorrecao($venda, $justificativa);

            if (! isset($result['erro'])) {
                return (object) ['status' => 'success', 'type' => 'success', 'title' => null, 'message' => 'Carta de Correção feita com sucesso'];
            }

            return (object) [
                'status' => 'error',
                'type' => 'rejeitado_sefaz',
                'title' => null,
                'message' => NFeErroUtil::formatar($this->extrairMensagemEventoNFe($result['data'])),
            ];
        } catch (ValidatorException $e) {
            return (object) ['status' => 'warning', 'type' => 'validator_exception', 'title' => null, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            return (object) ['status' => 'error', 'type' => 'exception', 'title' => null, 'message' => 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e->getMessage()];
        }
    }

    /**
     * Inutiliza uma faixa de numeração de NFe na SEFAZ. Extraído de
     * PedidosController::inutilizar() para ser reaproveitado tanto pela tela
     * web quanto pela API.
     */
    protected function _inutilizarNumeracao(int $empresaId, int $numeroInicial, int $numeroFinal, string $justificativa, EmpresasService $empresaServices): object
    {
        try {
            $empresa = $empresaServices->buscarEmpresa($empresaId);

            if ($empresa === null) {
                return (object) ['status' => 'error', 'type' => 'emitente_nao_configurado', 'title' => null, 'message' => 'Configure o emitente'];
            }

            $nfe_service = $this->makeNFeService($empresa);
            $result = $nfe_service->inutilizarNum(
                $empresa->serie,
                $numeroInicial,
                $numeroFinal,
                $justificativa,
                $empresa->fantasia.'/'.date('Y').'/'.date('m').'/notas/Inutilizacoes'
            );

            if (! isset($result['erro'])) {
                return (object) ['status' => 'success', 'type' => 'success', 'title' => null, 'message' => 'Inutilização feita com sucesso'];
            }

            return (object) [
                'status' => 'error',
                'type' => 'rejeitado_sefaz',
                'title' => null,
                'message' => NFeErroUtil::formatar($result['data']),
            ];
        } catch (ValidatorException $e) {
            return (object) ['status' => 'warning', 'type' => 'validator_exception', 'title' => null, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            return (object) ['status' => 'error', 'type' => 'exception', 'title' => null, 'message' => 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e->getMessage()];
        }
    }
}
