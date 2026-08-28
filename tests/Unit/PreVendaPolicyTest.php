<?php

namespace Tests\Unit;

use App\Models\PreVenda;
use App\Models\User;
use App\Policies\PreVendaPolicy;
use Tests\TestCase;

class PreVendaPolicyTest extends TestCase
{
    public function test_permite_usuario_habilitado_da_mesma_empresa(): void
    {
        $user = new User(['cargo' => 'client-NFe', 'empresa_id' => 10]);
        $preVenda = new PreVenda(['empresa_id' => 10]);
        $policy = new PreVendaPolicy;

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->view($user, $preVenda));
        $this->assertTrue($policy->update($user, $preVenda));
    }

    public function test_bloqueia_acesso_a_pre_venda_de_outra_empresa(): void
    {
        $user = new User(['cargo' => 'client-NFCe', 'empresa_id' => 10]);
        $preVenda = new PreVenda(['empresa_id' => 20]);

        $this->assertFalse((new PreVendaPolicy)->view($user, $preVenda));
    }

    public function test_master_pode_acessar_outras_empresas(): void
    {
        $user = new User(['cargo' => 'master', 'empresa_id' => 1]);
        $preVenda = new PreVenda(['empresa_id' => 20]);

        $this->assertTrue((new PreVendaPolicy)->view($user, $preVenda));
    }

    public function test_bloqueia_cargo_nao_habilitado(): void
    {
        $user = new User(['cargo' => 'client-MDFe', 'empresa_id' => 10]);

        $this->assertFalse((new PreVendaPolicy)->viewAny($user));
    }
}
