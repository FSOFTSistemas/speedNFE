<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EfiPixService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Throwable;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PagamentoPixConfirmado;

class PixController extends Controller
{
    public function __construct(private EfiPixService $efi) {}



   public function pagar(Request $request)
{
    // dd($request);
    return view('faturas.payment', [
        'valor'     => $request->input('valor', null),
        'descricao' => $request->input('descricao', 'Pagamento de Mensalidade'),
        'expiracao' => $request->input('expiracao', 300),   
        'installment_id'    => $request->input('installment_id', null),
        'customer_cnpj_cpf' => $request->input('customer_cnpj_cpf', null),         
    ]);
}
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
            $expiracao = (int) ($data['expiracao'] ?? 300);

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

    public function enviarConfirmacaoEmail(Request $request)
    {
        // 1. Valida os dados que o SweetAlert (JS) enviou
        $data = $request->validate([
            'email'     => 'required|email', // <-- AGORA VALIDAMOS O E-MAIL DO MODAL
            'txid'      => 'required|string',
            'valor'     => 'required|string', 
            'descricao' => 'required|string',
        ]);

        try {
            // Pega o nome do usuário logado (só para o "Olá, Nome")
            // Mas usa o e-mail que veio do formulário
            $nomeUsuario = $request->user() ? $request->user()->name : 'Cliente';

            // 2. Envia o e-mail para o endereço DIGITADO
            Mail::to($data['email'])->send(
                new PagamentoPixConfirmado(
                    $nomeUsuario,
                    $data['txid'],
                    $data['valor'],
                    $data['descricao']
                )
            );

        } catch (Throwable $e) {
            // 3. SE FALHAR, retorne um erro 500 para o SweetAlert
            Log::error('Falha ao enviar comprovante PIX p/ email digitado', [
                'email'   => $data['email'],
                'txid'    => $data['txid'],
                'error'   => $e->getMessage(),
            ]);
            
            // Isso fará o SweetAlert mostrar a mensagem de erro
            return response()->json(['message' => 'Falha ao enviar e-mail. Verifique o endereço e suas credenciais SMTP.'], 500);
        }

        // 4. Se deu certo, retorna sucesso.
        return response()->json(['message' => 'E-mail enviado com sucesso!']);
    }
}
