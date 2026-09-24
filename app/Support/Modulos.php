<?php

namespace App\Support;

use App\Models\User;

/**
 * Módulos liberados por cargo (users.cargo), espelhando o 'can' do menu em config/adminlte.php
 * e o middleware access.permission das rotas web. Usado pela API v1 (app mobile).
 */
class Modulos
{
    public const CARGOS = [
        'cadastros' => ['master', 'admin', 'client-NFe', 'client-NFCe', 'client-advanced1', 'client-advanced2'],
        'nfe' => ['master', 'admin', 'client-NFe', 'client-advanced1', 'client-advanced2'],
        'nfce' => ['master', 'admin', 'client-NFCe', 'client-advanced2'],
        'mdfe' => ['master', 'admin', 'client-MDFe', 'client-advanced1', 'client-advanced3'],
        'cte' => ['master', 'admin', 'client-CTe', 'client-advanced3'],
        'nfse' => ['master', 'admin', 'client-NFSe'],
        'nfcom' => ['master', 'admin', 'client-NFCom'],
    ];

    public static function permite(?User $user, string $modulo): bool
    {
        return $user !== null && in_array($user->cargo, self::CARGOS[$modulo] ?? [], true);
    }

    /** @return array<string, bool> */
    public static function doUsuario(User $user): array
    {
        return array_map(fn (array $cargos) => in_array($user->cargo, $cargos, true), self::CARGOS);
    }
}
