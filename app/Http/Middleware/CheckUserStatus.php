<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe a classe Auth

class CheckUserStatus
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
        // Verifica se o usuário está logado E se o status dele é 'inativo'
        if (Auth::check() && Auth::user()->status === 'inativo') {
            // Se for inativo, faz o logout
            Auth::logout();

            // Invalida a sessão e gera um novo token para segurança
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redireciona para a página de login com uma mensagem de erro
            return redirect('/login')->with('error', 'Sua conta foi inativada. Por favor, entre em contato com o suporte.');
        }

        // Se o usuário estiver ativo ou não estiver logado, a requisição continua normalmente
        return $next($request);
    }
}