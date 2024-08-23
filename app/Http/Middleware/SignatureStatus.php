<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $customerCnpjCpf = auth()->user()->empresa->cpf_cnpj;
        $response = Http::get('https://financeiro.f-softsistemas.com.br/api/customer/' . $customerCnpjCpf);
        $body = json_decode($response->body());
        if (isset($body->due) && $body->due->expires === true) {
            $message = 'Sua licença expirou em ' . date('d/m/Y', strtotime($body->due->expired_on)) . ', efetue o pagamento para liberação da plataforma!';
            sweetalert($message, 'error');
            return redirect()->route('home');
        }
        return $next($request);
    }
}
