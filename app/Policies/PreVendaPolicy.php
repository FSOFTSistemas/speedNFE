<?php

namespace App\Policies;

use App\Models\PreVenda;
use App\Models\User;

class PreVendaPolicy
{
    private const CARGOS_PERMITIDOS = [
        'master',
        'admin',
        'client-advanced1',
        'client-advanced2',
        'client-NFe',
        'client-NFCe',
    ];

    public function viewAny(User $user): bool
    {
        return $this->cargoPermitido($user);
    }

    public function view(User $user, PreVenda $preVenda): bool
    {
        return $this->cargoPermitido($user)
            && ((int) $user->empresa_id === 1 || (int) $user->empresa_id === (int) $preVenda->empresa_id);
    }

    public function create(User $user): bool
    {
        return $this->cargoPermitido($user);
    }

    public function update(User $user, PreVenda $preVenda): bool
    {
        return $this->view($user, $preVenda);
    }

    public function cancel(User $user, PreVenda $preVenda): bool
    {
        return $this->view($user, $preVenda);
    }

    public function convert(User $user, PreVenda $preVenda): bool
    {
        return $this->view($user, $preVenda);
    }

    private function cargoPermitido(User $user): bool
    {
        return in_array($user->cargo, self::CARGOS_PERMITIDOS, true);
    }
}
