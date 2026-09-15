<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SignatureStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
{
    $customerCnpjCpf = Auth::user()->empresa->cpf_cnpj;
    $response = Http::get('https://financeiro.f-softsistemas.com.br/api/customer/' . $customerCnpjCpf);
    $body = json_decode($response->body());

    if (isset($body->due) && $body->due->expires === true) {
        $dataVencimento = Carbon::parse($body->due->expired_on);
        
        // Adiciona 3 dias de carência à data de vencimento
        $dataLimiteComCarencia = $dataVencimento->copy()->addDays(3);

        // Só bloqueia se a data atual (agora) for maior que a data limite
        if (now()->greaterThan($dataLimiteComCarencia)) {
            $message = 'Sua licença expirou em ' . $dataVencimento->format('d/m/Y') . ', efetue o pagamento para liberação da plataforma!';
            sweetalert($message, 'error');
            return redirect()->route('faturas.index');
        }
    }

    return $next($request);
}
}
