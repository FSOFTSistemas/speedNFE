<?php

namespace App\Services;

use App\Enums\EstadoEnum;
use App\Models\Cliente;
use App\Models\NFSe;
use App\Models\NFSeEvento;
use App\Models\NFSeXml;
use App\Models\Servico;
use App\Services\NFSe\CancelamentoXmlBuilder;
use App\Services\NFSe\DpsXmlBuilder;
use App\Services\NFSe\NFSeClient;
use App\Services\NFSe\NFSeSchemaValidator;
use App\Services\NFSe\NFSeSigner;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use Throwable;

class NFSeService
{
    private DpsXmlBuilder $dpsXmlBuilder;

    private NFSeSigner $nfseSigner;

    private NFSeSchemaValidator $schemaValidator;

    private NFSeClient $nfseClient;

    private CancelamentoXmlBuilder $cancelamentoXmlBuilder;

    public function __construct(
        DpsXmlBuilder $dpsXmlBuilder,
        NFSeSigner $nfseSigner,
        NFSeSchemaValidator $schemaValidator,
        NFSeClient $nfseClient,
        CancelamentoXmlBuilder $cancelamentoXmlBuilder
    ) {
        $this->dpsXmlBuilder = $dpsXmlBuilder;
        $this->nfseSigner = $nfseSigner;
        $this->schemaValidator = $schemaValidator;
        $this->nfseClient = $nfseClient;
        $this->cancelamentoXmlBuilder = $cancelamentoXmlBuilder;
    }

    public function todas($empresaId)
    {
        $query = NFSe::query()
            ->with(['cliente', 'servico'])
            ->latest('created_at');

        if ($empresaId != 1) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->get();
    }

    public function buscar(int $id, int $empresaId): NFSe
    {
        $query = NFSe::query()->with(['empresa.endereco', 'cliente.endereco', 'servico', 'xmls', 'eventos']);

        if ($empresaId != 1) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->findOrFail($id);
    }

    public function criar(array $data, $empresa): NFSe
    {
        return DB::transaction(function () use ($data, $empresa) {
            $payload = $this->payloadFiscal($data, $empresa);

            return NFSe::create($payload);
        });
    }

    public function atualizar(NFSe $nfse, array $data, $empresa): NFSe
    {
        return DB::transaction(function () use ($nfse, $data, $empresa) {
            $nfse->update($this->payloadFiscal($data, $empresa));

            return $nfse->refresh();
        });
    }

    public function excluir(NFSe $nfse): void
    {
        DB::transaction(function () use ($nfse) {
            $nfse->xmls()->delete();
            $nfse->eventos()->delete();
            $nfse->delete();
        });
    }

    public function emitir(NFSe $nfse): array
    {
        if ($this->transmissaoInconclusiva($nfse)) {
            return $this->reconciliar($nfse);
        }

        try {
            $nfse = $this->prepararParaEmissao($nfse);

            $xml = $this->dpsXmlBuilder->build($nfse);
            $xmlAssinado = $this->nfseSigner->signDps($xml, $nfse->empresa);
            $this->schemaValidator->validateDps($xmlAssinado);
            $this->salvarXml($nfse, 'dps', $xmlAssinado);
        } catch (Throwable $e) {
            $this->marcarRejeitada($nfse, $e->getMessage());

            return [
                'ok' => false,
                'message' => 'Falha ao preparar a DPS: '.$e->getMessage(),
            ];
        }

        $response = $this->nfseClient->emitir($xmlAssinado, $nfse->empresa);

        if ($response['ok'] && $this->possuiAutorizacao($response['body'])) {
            $this->registrarAutorizacao($nfse, $response['body']);

            return [
                'ok' => true,
                'message' => 'NFS-e transmitida e autorizada com sucesso.',
            ];
        }

        if ($this->respostaInconclusiva($response)) {
            return $this->reconciliar($nfse, true);
        }

        $message = $this->mensagemResposta($response['body']);
        $this->marcarRejeitada($nfse, $message, $response['status']);

        return [
            'ok' => false,
            'message' => 'NFS-e rejeitada pela SEFIN: '.$message,
        ];
    }

    public function reconciliar(NFSe $nfse, bool $aposFalhaDeEnvio = false): array
    {
        $nfse = $nfse->fresh(['empresa.endereco', 'cliente.endereco', 'servico']);
        if (! $nfse || ! $nfse->nDPS) {
            return [
                'ok' => false,
                'pending' => false,
                'message' => 'A NFS-e ainda não possui uma DPS transmitida para reconciliar.',
            ];
        }

        try {
            $idDps = $this->dpsXmlBuilder->dpsId($nfse);
            $response = $this->nfseClient->consultarDps($idDps, $nfse->empresa);
        } catch (Throwable $e) {
            $this->marcarPendente($nfse, 'Consulta de reconciliação falhou: '.$e->getMessage());

            return [
                'ok' => false,
                'pending' => true,
                'message' => 'Não foi possível confirmar a situação da DPS. Não retransmita; tente sincronizar novamente.',
            ];
        }

        if ($response['ok'] && $this->possuiAutorizacao($response['body'])) {
            $this->registrarAutorizacao($nfse, $response['body']);

            return [
                'ok' => true,
                'pending' => false,
                'message' => 'NFS-e localizada na SEFIN e sincronizada com sucesso.',
            ];
        }

        $motivo = $aposFalhaDeEnvio
            ? 'Transmissão inconclusiva. A SEFIN ainda não confirmou se a DPS foi processada.'
            : 'A SEFIN ainda não retornou uma NFS-e para esta DPS.';
        $detalhe = $this->mensagemResposta($response['body']);
        $this->marcarPendente($nfse, $motivo.' '.$detalhe);

        return [
            'ok' => false,
            'pending' => true,
            'message' => $motivo.' Não retransmita; use a ação Sincronizar.',
        ];
    }

    public function cancelar(NFSe $nfse, string $codigoMotivo, string $justificativa): array
    {
        $nfse = $nfse->fresh(['empresa', 'eventos']);
        $eventoPendente = $nfse->eventos
            ->first(fn (NFSeEvento $evento) => $evento->tipo_evento === 'cancelamento'
                && $evento->situacao === EstadoEnum::PENDENTE->value);
        if ($eventoPendente) {
            return $this->reconciliarCancelamento($nfse, $eventoPendente);
        }

        try {
            $xml = $this->cancelamentoXmlBuilder->build($nfse, $codigoMotivo, $justificativa);
            $xmlAssinado = $this->nfseSigner->signEvent($xml, $nfse->empresa);
            $this->schemaValidator->validateEvent($xmlAssinado);
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'pending' => false,
                'message' => 'Falha ao preparar o cancelamento: '.$e->getMessage(),
            ];
        }

        $evento = DB::transaction(function () use ($nfse, $justificativa, $xmlAssinado) {
            return NFSeEvento::updateOrCreate([
                'nfse_id' => $nfse->id,
                'tipo_evento' => 'cancelamento',
                'sequencia' => 1,
            ], [
                'codigo_evento' => '101101',
                'situacao' => EstadoEnum::PENDENTE->value,
                'justificativa' => $justificativa,
                'data_evento' => now(),
                'xml_pedido' => $xmlAssinado,
                'xml_retorno' => null,
                'cStat' => null,
                'xMotivo' => null,
            ]);
        });

        $response = $this->nfseClient->registrarEvento($nfse->chave, $xmlAssinado, $nfse->empresa);
        if ($response['ok'] && $this->possuiEventoAutorizado($response['body'])) {
            $this->registrarCancelamentoAutorizado($nfse, $evento, $response['body']);

            return [
                'ok' => true,
                'pending' => false,
                'message' => 'Cancelamento autorizado pela SEFIN Nacional.',
            ];
        }

        if ($this->respostaInconclusiva($response)) {
            return $this->reconciliarCancelamento($nfse, $evento);
        }

        $message = $this->mensagemResposta($response['body']);
        $evento->update([
            'situacao' => EstadoEnum::REJEITADO->value,
            'cStat' => $response['status'] ? (string) $response['status'] : null,
            'xMotivo' => mb_substr($message, 0, 2000),
            'xml_retorno' => $this->conteudoRetornoEvento($response['body']),
        ]);

        return [
            'ok' => false,
            'pending' => false,
            'message' => 'Cancelamento rejeitado pela SEFIN: '.$message,
        ];
    }

    private function reconciliarCancelamento(NFSe $nfse, NFSeEvento $evento): array
    {
        $response = $this->nfseClient->consultarEvento($nfse->chave, '101101', 1, $nfse->empresa);
        if ($response['ok'] && $this->possuiEventoAutorizado($response['body'])) {
            $this->registrarCancelamentoAutorizado($nfse, $evento, $response['body']);

            return [
                'ok' => true,
                'pending' => false,
                'message' => 'Cancelamento localizado na SEFIN e sincronizado.',
            ];
        }

        $evento->update([
            'situacao' => EstadoEnum::PENDENTE->value,
            'xMotivo' => 'Transmissão do cancelamento inconclusiva. Consulte novamente antes de reenviar.',
        ]);

        return [
            'ok' => false,
            'pending' => true,
            'message' => 'Não foi possível confirmar o cancelamento. A nota continua autorizada localmente; consulte novamente antes de reenviar.',
        ];
    }

    private function registrarCancelamentoAutorizado(NFSe $nfse, NFSeEvento $evento, array $body): void
    {
        DB::transaction(function () use ($nfse, $evento, $body) {
            $xmlRetorno = $this->conteudoRetornoEvento($body);
            $dados = $this->dadosXmlEvento($body['eventoXml'] ?? null);

            $evento->update([
                'situacao' => EstadoEnum::AUTORIZADO->value,
                'nProtocolo' => $body['idEvento'] ?? $body['protocolo'] ?? $evento->nProtocolo,
                'cStat' => $dados['cStat'] ?? '100',
                'xMotivo' => $dados['xMotivo'] ?? 'Cancelamento autorizado pela SEFIN Nacional.',
                'xml_retorno' => $xmlRetorno,
            ]);

            $nfse->situacao = EstadoEnum::CANCELADO->value;
            $nfse->cStat = $dados['cStat'] ?? $nfse->cStat;
            $nfse->xMotivo = $dados['xMotivo'] ?? 'Cancelamento autorizado pela SEFIN Nacional.';
            $nfse->save();
        });
    }

    public function podeEditar(NFSe $nfse): bool
    {
        if ($nfse->situacao === EstadoEnum::PENDENTE->value && $nfse->cStat === 'PENDENTE') {
            return false;
        }

        return in_array($nfse->situacao, [
            EstadoEnum::PENDENTE->value,
            EstadoEnum::REJEITADO->value,
            'Rascunho',
        ], true);
    }

    public function podeExcluir(NFSe $nfse): bool
    {
        return $this->podeEditar($nfse);
    }

    public function baixarDanfse(NFSe $nfse): array
    {
        if (! $nfse->chave) {
            return [
                'ok' => false,
                'message' => 'A NFS-e ainda não possui chave de acesso para gerar o DANFSe.',
            ];
        }

        $response = $this->nfseClient->baixarDanfse($nfse->chave);
        if (! $response['ok'] || ! str_starts_with($response['body'], '%PDF-')) {
            return [
                'ok' => false,
                'message' => 'Não foi possível baixar o DANFSe no Ambiente de Dados Nacional.',
            ];
        }

        return [
            'ok' => true,
            'content' => $response['body'],
            'filename' => 'danfse_'.$nfse->chave.'.pdf',
        ];
    }

    private function prepararParaEmissao(NFSe $nfse): NFSe
    {
        return DB::transaction(function () use ($nfse) {
            $nfse = NFSe::query()
                ->with(['empresa.endereco', 'cliente.endereco', 'servico'])
                ->lockForUpdate()
                ->findOrFail($nfse->id);

            if (! $this->podeEditar($nfse)) {
                throw new \RuntimeException('Apenas NFS-e pendentes ou rejeitadas podem ser transmitidas.');
            }

            $empresa = $nfse->empresa()->lockForUpdate()->firstOrFail();
            $empresa->loadMissing('endereco');

            if (! $nfse->nDPS) {
                $nfse->nDPS = (string) (((int) $empresa->ultimaDPS) + 1);
                $empresa->ultimaDPS = (int) $nfse->nDPS;
                $empresa->save();
            }

            if (! $nfse->serieDPS) {
                $nfse->serieDPS = $empresa->serieNFSe ?: $empresa->serie;
            }

            if (! $nfse->serie) {
                $nfse->serie = $empresa->serieNFSe ?: $empresa->serie;
            }

            if (! $nfse->tpAmb) {
                $nfse->tpAmb = $empresa->ambiente;
            }

            if (! $nfse->tpEmit) {
                $nfse->tpEmit = 1;
            }

            if (! $nfse->data_emissao) {
                $nfse->data_emissao = now();
            }

            if (! $nfse->cLocEmi) {
                $nfse->cLocEmi = $empresa->endereco->codigoIBGE ?? null;
            }

            if (! $nfse->cLocPrestacao) {
                $nfse->cLocPrestacao = $empresa->endereco->codigoIBGE ?? null;
            }

            $nfse->situacao = EstadoEnum::PENDENTE->value;
            $nfse->xMotivo = null;
            $nfse->save();

            return $nfse->refresh()->load(['empresa.endereco', 'cliente.endereco', 'servico']);
        });
    }

    private function registrarAutorizacao(NFSe $nfse, array $body): void
    {
        DB::transaction(function () use ($nfse, $body) {
            $dadosXml = $this->dadosXmlAutorizado($body['nfseXml'] ?? null);

            if (! empty($body['nfseXml'])) {
                $this->salvarXml($nfse, 'autorizado', $body['nfseXml']);
            }

            $nfse->chave = $body['chaveAcesso'] ?? $dadosXml['chNFSe'] ?? $nfse->chave;
            $nfse->nProtocolo = $body['idDps'] ?? $body['protocolo'] ?? $nfse->nProtocolo;
            $nfse->nro = $dadosXml['nNFSe'] ?? $nfse->nro;
            $nfse->nDFSe = $dadosXml['nDFSe'] ?? $nfse->nDFSe;
            $nfse->cStat = $dadosXml['cStat'] ?? '100';
            $nfse->xMotivo = $this->mensagemAlertas($body['alertas'] ?? null) ?: 'Autorizado o uso da NFS-e.';
            $nfse->situacao = EstadoEnum::AUTORIZADO->value;
            $nfse->data_processamento = isset($dadosXml['dhProc']) ? Carbon::parse($dadosXml['dhProc']) : now();
            $nfse->vIBS = $dadosXml['vIBS'] ?? $nfse->vIBS;
            $nfse->vCBS = $dadosXml['vCBS'] ?? $nfse->vCBS;
            $nfse->vTotNF = $dadosXml['vTotNF'] ?? $nfse->vTotNF;
            $nfse->save();

            $empresa = $nfse->empresa()->lockForUpdate()->first();
            if ($empresa && is_numeric($nfse->nDPS)) {
                $empresa->ultimaDPS = max((int) $empresa->ultimaDPS, (int) $nfse->nDPS);
            }
            if ($empresa && is_numeric($nfse->nro)) {
                $empresa->ultimaNFSe = max((int) $empresa->ultimaNFSe, (int) $nfse->nro);
            }
            if ($empresa) {
                $empresa->save();
            }
        });
    }

    private function marcarRejeitada(NFSe $nfse, string $motivo, $status = null): void
    {
        $nfse->situacao = EstadoEnum::REJEITADO->value;
        $nfse->cStat = $status ? (string) $status : $nfse->cStat;
        $nfse->xMotivo = mb_substr($motivo, 0, 2000);
        $nfse->save();
    }

    private function marcarPendente(NFSe $nfse, string $motivo): void
    {
        $nfse->situacao = EstadoEnum::PENDENTE->value;
        $nfse->cStat = 'PENDENTE';
        $nfse->xMotivo = mb_substr($motivo, 0, 2000);
        $nfse->save();
    }

    private function salvarXml(NFSe $nfse, string $tipo, string $xml): NFSeXml
    {
        return NFSeXml::updateOrCreate(
            [
                'nfse_id' => $nfse->id,
                'tipo' => $tipo,
            ],
            [
                'xml' => $xml,
            ]
        );
    }

    private function dadosXmlAutorizado(?string $xml): array
    {
        if (! $xml) {
            return [];
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;

        if (! @$dom->loadXML($xml)) {
            return [];
        }

        $xpath = new DOMXPath($dom);

        return [
            'nNFSe' => $this->xpathValue($xpath, '//*[local-name()="nNFSe"]'),
            'chNFSe' => $this->xpathValue($xpath, '//*[local-name()="chNFSe"]'),
            'nDFSe' => $this->xpathValue($xpath, '//*[local-name()="nDFSe"]'),
            'cStat' => $this->xpathValue($xpath, '//*[local-name()="cStat"]'),
            'dhProc' => $this->xpathValue($xpath, '//*[local-name()="dhProc"]'),
            'vIBS' => $this->xpathValue($xpath, '//*[local-name()="vIBS"]'),
            'vCBS' => $this->xpathValue($xpath, '//*[local-name()="vCBS"]'),
            'vTotNF' => $this->xpathValue($xpath, '//*[local-name()="vTotNF"]'),
        ];
    }

    private function dadosXmlEvento(?string $xml): array
    {
        if (! $xml) {
            return [];
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        if (! @$dom->loadXML($xml)) {
            return [];
        }

        $xpath = new DOMXPath($dom);

        return [
            'cStat' => $this->xpathValue($xpath, '//*[local-name()="cStat"]'),
            'xMotivo' => $this->xpathValue($xpath, '//*[local-name()="xMotivo"]'),
        ];
    }

    private function conteudoRetornoEvento(array $body): ?string
    {
        if (! empty($body['eventoXml'])) {
            return $body['eventoXml'];
        }

        $json = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $json !== false ? $json : null;
    }

    private function possuiAutorizacao(array $body): bool
    {
        return empty($body['erros']) && (! empty($body['nfseXml']) || ! empty($body['chaveAcesso']));
    }

    private function possuiEventoAutorizado(array $body): bool
    {
        return empty($body['erros']) && (! empty($body['eventoXml']) || ! empty($body['idEvento']) || ! empty($body['protocolo']));
    }

    private function respostaInconclusiva(array $response): bool
    {
        if (! empty($response['body']['erros'])) {
            return false;
        }

        return $response['status'] === null || (int) $response['status'] >= 500 || ($response['ok'] && ! $this->possuiAutorizacao($response['body']));
    }

    private function transmissaoInconclusiva(NFSe $nfse): bool
    {
        return $nfse->situacao === EstadoEnum::PENDENTE->value
            && $nfse->cStat === 'PENDENTE'
            && $nfse->xmlDps()->exists();
    }

    private function xpathValue(DOMXPath $xpath, string $query): ?string
    {
        $value = trim((string) $xpath->evaluate('string('.$query.')'));

        return $value !== '' ? $value : null;
    }

    private function mensagemResposta(array $body): string
    {
        $mensagens = $this->flattenMessages($body['erros'] ?? $body['mensagens'] ?? $body['raw'] ?? $body);

        return implode(' | ', array_unique(array_filter($mensagens))) ?: 'Erro desconhecido na comunicação NFS-e.';
    }

    private function mensagemAlertas($alertas): ?string
    {
        if (! $alertas) {
            return null;
        }

        $mensagens = $this->flattenMessages($alertas);

        return implode(' | ', array_unique(array_filter($mensagens))) ?: null;
    }

    private function flattenMessages($value): array
    {
        if (is_string($value) || is_numeric($value)) {
            return [(string) $value];
        }

        if (! is_array($value)) {
            return [];
        }

        $messages = [];
        foreach ($value as $key => $item) {
            if (is_string($item) || is_numeric($item)) {
                $label = is_string($key) && ! is_numeric($key) ? $key.': ' : '';
                $messages[] = $label.$item;

                continue;
            }

            $messages = array_merge($messages, $this->flattenMessages($item));
        }

        return $messages;
    }

    private function payloadFiscal(array $data, $empresa): array
    {
        $servicoQuery = Servico::query()->whereKey($data['servico_id']);
        $clienteQuery = Cliente::query()->whereKey($data['cliente_id']);
        if ((int) $empresa->id !== 1) {
            $servicoQuery->where('empresa_id', $empresa->id);
            $clienteQuery->where('empresa_id', $empresa->id);
        }
        $servico = $servicoQuery->firstOrFail();
        $clienteQuery->firstOrFail();
        $value = fn (string $key, $default = null) => $data[$key] ?? $default;
        $vServ = (float) $value('vServ', 0);
        $vDescIncond = (float) $value('vDescIncond', 0);
        $vDescCond = (float) $value('vDescCond', 0);
        $vDeducaoReducao = (float) $value('vDeducaoReducao', 0);
        $vTotalRet = (float) $value('vTotalRet', 0);
        $pAliq = $value('pAliq') !== null && $value('pAliq') !== '' ? (float) $value('pAliq') : (float) ($servico?->pAliqISSQN ?? 0);
        $vBC = max(0, $vServ - $vDescIncond - $vDeducaoReducao);
        $vISSQN = $value('vISSQN') !== null && $value('vISSQN') !== ''
            ? (float) $value('vISSQN')
            : round($vBC * ($pAliq / 100), 2);
        $vIBS = round($vBC * (0.1 / 100), 2);
        $vCBS = round($vBC * (0.9 / 100), 2);
        $vLiq = max(0, $vServ - $vDescIncond - $vDescCond - $vTotalRet);
        $competenceYear = Carbon::parse($data['data_competencia'])->year;
        $vTotNF = $competenceYear >= 2027 ? $vLiq + $vIBS + $vCBS : $vLiq;

        return [
            'empresa_id' => $empresa->id,
            'cliente_id' => $data['cliente_id'],
            'servico_id' => $data['servico_id'],
            'serie' => $empresa->serieNFSe ?: $empresa->serie,
            'serieDPS' => $empresa->serieNFSe ?: $empresa->serie,
            'situacao' => EstadoEnum::PENDENTE->value,
            'tpAmb' => $empresa->ambiente,
            'tpEmit' => 1,
            'data_competencia' => $data['data_competencia'],
            'data_emissao' => $data['data_emissao'] ?? now(),
            'cLocEmi' => $empresa->endereco->codigoIBGE ?? null,
            'cLocPrestacao' => $value('cLocPrestacao') ?: ($empresa->endereco->codigoIBGE ?? null),
            'cLocIncid' => $value('cLocIncid') ?: null,
            'cTribNac' => $value('cTribNac') ?: $servico?->cTribNac,
            'cTribMun' => $value('cTribMun') ?: $servico?->cTribMun,
            'cNBS' => $value('cNBS') ?: $servico?->cNBS,
            'cIndOp' => $value('cIndOp') ?: $servico?->cIndOp,
            'cClassTrib' => $value('cClassTrib') ?: $servico?->cClassTrib,
            'cst_ibs_cbs' => $value('cst_ibs_cbs') ?: $servico?->cst_ibs_cbs,
            'finNFSe' => $value('finNFSe', $servico?->finNFSe ?? '0'),
            'indFinal' => $value('indFinal', $servico?->indFinal ?? '0'),
            'indDest' => $value('indDest', $servico?->indDest ?? '0'),
            'vServ' => $vServ,
            'vDescIncond' => $vDescIncond,
            'vDescCond' => $vDescCond,
            'vDeducaoReducao' => $vDeducaoReducao,
            'vBC' => $vBC,
            'pAliq' => $pAliq ?: null,
            'vISSQN' => $vISSQN,
            'vTotalRet' => $vTotalRet,
            'vLiq' => $vLiq,
            'vIBS' => $vIBS,
            'vCBS' => $vCBS,
            'vTotNF' => $vTotNF,
            'discriminacao' => $data['discriminacao'],
            'informacoes_complementares' => $value('informacoes_complementares'),
        ];
    }
}
