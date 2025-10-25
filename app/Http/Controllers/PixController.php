<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EfiPixService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Throwable;

class PixController extends Controller
{
    public function __construct(private EfiPixService $efi) {}

    // POST /pix/cob
    public function criar(Request $req)
    {
        try {
            $data = $req->validate([
                'valor'     => 'required|numeric|min:0.01',
                'descricao' => 'nullable|string|max:255',
                'expiracao' => 'nullable|integer|min:60|max:86400',
            ]);

            $valor     = number_format((float) $data['valor'], 2, '.', '');
            $expiracao = (int) ($data['expiracao'] ?? 3600);

            $payload = [
                'calendario' => ['expiracao' => $expiracao],
                'valor'      => ['original' => $valor],
                'chave'      => config('efipay.pix_key'),
                'solicitacaoPagador' => $data['descricao'] ?? 'Pagamento',
            ];

            $cobranca = $this->efi->criarCobranca($payload);
            $locId    = data_get($cobranca, 'loc.id');

            if (!$locId) {
                return response()->json([
                    'message' => 'Falha ao gerar cobrança PIX: location não retornado.'
                ], 500);
            }

            $qrcode   = $this->efi->obterQrCodePorLocId($locId);

            $criacao   = Carbon::parse(data_get($cobranca, 'calendario.criacao'));
            $expiresAt = $criacao->copy()->addSeconds($expiracao)->toIso8601String();

            return response()->json([
                'txid'         => data_get($cobranca, 'txid'),
                'status'       => data_get($cobranca, 'status'),
                'location'     => data_get($cobranca, 'loc.location'),
                'qr_base64'    => data_get($qrcode, 'imagemQrcode'),
                'copia_e_cola' => data_get($qrcode, 'qrcode'),
                'expires_at'   => $expiresAt,
            ]);
        } catch (ValidationException $ve) {
            return response()->json(['message' => $ve->getMessage(), 'errors' => $ve->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erro ao criar cobrança PIX', 'error' => $e->getMessage()], 500);
        }
    }

    // GET /pix/cob/{txid}
    public function consultar(string $txid)
    {
        try {
            $cobranca = $this->efi->consultarCobranca($txid);
            $status   = strtoupper((string) data_get($cobranca, 'status', ''));
            $pago     = in_array($status, ['CONCLUIDA', 'LIQUIDADA', 'COMPLETA']);
            $criacao  = data_get($cobranca, 'calendario.criacao');
            $exp      = (int) data_get($cobranca, 'calendario.expiracao', 3600);
            $expiresAt = $criacao ? Carbon::parse($criacao)->addSeconds($exp)->toIso8601String() : null;

            return response()->json([
                'status'      => $status,
                'pago'        => $pago,
                'txid'        => data_get($cobranca, 'txid'),
                'valor'       => data_get($cobranca, 'valor.original'),
                'expires_at'  => $expiresAt,
                'raw'         => $cobranca,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erro ao consultar cobrança PIX', 'error' => $e->getMessage()], 500);
        }
    }
}
