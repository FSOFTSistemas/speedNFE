<?php

namespace App\Http\Middleware;

use App\Support\Modulos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Versão JSON do access.permission para a API: bloqueia o módulo se o cargo do usuário não tiver acesso.
 */
class ApiModulo
{
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        if (! Modulos::permite($request->user(), $modulo)) {
            return response()->json([
                'message' => 'Seu usuário não tem acesso a este módulo.',
            ], 403);
        }

        return $next($request);
    }
}
