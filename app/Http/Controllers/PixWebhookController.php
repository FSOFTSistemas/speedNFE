<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PixWebhookController extends Controller
{
    public function receber(Request $request)
    {
        $payload = $request->all();
        Log::info('PIX webhook recebido', $payload);

        // TODO: conciliar pagamento (achar txid/endToEndId e marcar como pago)
        return response()->json(['ok' => true]);
    }
}