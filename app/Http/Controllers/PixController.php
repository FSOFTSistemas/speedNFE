<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EfiPixService;
use Carbon\Carbon;

class PixController extends Controller
{
    public function __construct(private EfiPixService $efi) {}

    // POST /pix/cob
    public function criar(Request $req)
    {
        $valor = number_format((float)$req->input('valor', 0), 2, '.', '');
        $payload = [
            'calendario' => ['expiracao' => (int)$req->input('expiracao', 3600)],
            'valor'      => ['original' => $valor],
            'chave'      => config('efipay.pix_key'),
            'solicitacaoPagador' => $req->input('descricao', 'Pagamento'),
        ];

        $cobranca = $this->efi->criarCobranca($payload);
        $locId    = data_get($cobranca, 'loc.id');
        $qrcode   = $this->efi->obterQrCodePorLocId($locId);

        $criacao   = Carbon::parse(data_get($cobranca, 'calendario.criacao'));
        $expSeg    = (int) ($req->input('expiracao', 3600));
        $expiresAt = $criacao->copy()->addSeconds($expSeg)->toIso8601String();

        return response()->json([
            'txid'         => data_get($cobranca, 'txid'),
            'status'       => data_get($cobranca, 'status'),
            'location'     => data_get($cobranca, 'loc.location'),
            'qr_base64'    => data_get($qrcode, 'imagemQrcode'),
            'copia_e_cola' => data_get($qrcode, 'qrcode'),
            'expires_at'   => $expiresAt,
        ]);
    }

    // GET /pix/cob/{txid}
    public function consultar(string $txid)
    {
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
    }
}