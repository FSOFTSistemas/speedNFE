<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplyRamoAtividadeMenu
{
    /**
     * Ajusta o item de menu "Produtos" para "Motos" (com ícone de moto)
     * quando o ramo de atividade da empresa do usuário logado for "motos".
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response)  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && optional(Auth::user()->empresa)->ramo_atividade === 'motos') {
            $menu = config('adminlte.menu', []);

            foreach ($menu as $index => $item) {
                if (($item['url'] ?? null) === '/produto') {
                    $menu[$index]['text'] = 'Motos';
                    $menu[$index]['icon'] = 'fas fa-motorcycle';
                }
            }

            config(['adminlte.menu' => $menu]);
        }

        return $next($request);
    }
}
