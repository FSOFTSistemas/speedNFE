<?php

namespace App\Services;

use App\Enums\EstadoEnum;
use App\Models\NFSe;
use App\Models\NFSeEvento;
use App\Models\NFSeXml;
use App\Models\Servico;
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

    public function __construct(
        DpsXmlBuilder $dpsXmlBuilder,
        NFSeSigner $nfseSigner,
        NFSeSchemaValidator $schemaValidator,
        NFSeClient $nfseClient
    ) {
        $this->dpsXmlBuilder = $dpsXmlBuilder;
        $this->nfseSigner = $nfseSigner;
        $this->schemaValidator = $schemaValidator;
        $this->nfseClient = $nfseClient;
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

        if ($response['ok']) {
            $this->registrarAutorizacao($nfse, $response['body']);

            return [
                'ok' => true,
                'message' => 'NFS-e transmitida e autorizada com sucesso.',
            ];
        }

        $message = $this->mensagemResposta($response['body']);
        $this->marcarRejeitada($nfse, $message, $response['status']);

        return [
            'ok' => false,
            'message' => 'NFS-e rejeitada pela SEFIN: '.$message,
        ];
    }

    public function registrarCancelamentoLocal(NFSe $nfse, string $justificativa): NFSeEvento
    {
        return DB::transaction(function () use ($nfse, $justificativa) {
            $sequencia = ((int) $nfse->eventos()->where('tipo_evento', 'cancelamento')->max('sequencia')) + 1;

            $evento = NFSeEvento::create([
                'nfse_id' => $nfse->id,
                'tipo_evento' => 'cancelamento',
                'codigo_evento' => '101101',
                'sequencia' => $sequencia,
                'situacao' => EstadoEnum::PENDENTE->value,
                'justificativa' => $justificativa,
                'data_evento' => now(),
            ]);

            $nfse->situacao = EstadoEnum::CANCELADO->value;
            $nfse->save();

            return $evento;
        });
    }

    public function podeEditar(NFSe $nfse): bool
    {
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

            $empresa = $nfse->empresa;

            if (! $nfse->nDPS) {
                $nfse->nDPS = (string) (((int) $empresa->ultimaDPS) + 1);
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

            $nfse->chave = $body['chaveAcesso'] ?? $nfse->chave;
            $nfse->nProtocolo = $body['idDps'] ?? $body['protocolo'] ?? $nfse->nProtocolo;
            $nfse->nro = $dadosXml['nNFSe'] ?? $nfse->nro;
            $nfse->nDFSe = $dadosXml['nDFSe'] ?? $nfse->nDFSe;
            $nfse->cStat = $dadosXml['cStat'] ?? '100';
            $nfse->xMotivo = $this->mensagemAlertas($body['alertas'] ?? null) ?: 'Autorizado o uso da NFS-e.';
            $nfse->situacao = EstadoEnum::AUTORIZADO->value;
            $nfse->data_processamento = isset($dadosXml['dhProc']) ? Carbon::parse($dadosXml['dhProc']) : now();
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
            'nDFSe' => $this->xpathValue($xpath, '//*[local-name()="nDFSe"]'),
            'cStat' => $this->xpathValue($xpath, '//*[local-name()="cStat"]'),
            'dhProc' => $this->xpathValue($xpath, '//*[local-name()="dhProc"]'),
        ];
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
        $servico = Servico::find($data['servico_id']);
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
        $vTotNF = $vLiq + $vIBS + $vCBS;

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
