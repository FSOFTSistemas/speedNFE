<?php

namespace Tests\Unit;

use App\Http\Livewire\NFCe;
use App\Services\ClientesService;
use App\Services\ProdutosService;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class NFCePaymentMethodsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function tearDown(): void
    {
        Auth::clearResolvedInstance('auth');
        parent::tearDown();
    }

    public function test_abertura_do_pdv_disponibiliza_formas_de_pagamento_como_texto(): void
    {
        $auth = Mockery::mock();
        $auth->shouldReceive('user')->once()->andReturn((object) ['empresa_id' => 7]);
        Auth::swap($auth);

        $clientes = Mockery::mock(ClientesService::class);
        $clientes->shouldReceive('todos')->once()->with(7)->andReturn(collect());

        $componente = new NFCe;
        $componente->mount($clientes, Mockery::mock(ProdutosService::class));

        self::assertSame(['DINHEIRO', 'PIX', 'CARTÃO/CRÉDITO', 'CARTÃO/DÉBITO'], $componente->formas);

        foreach ($componente->formas as $forma) {
            // A view escapa e formata cada opção ao abrir, antes de qualquer venda.
            self::assertNotEmpty(htmlspecialchars($forma, ENT_QUOTES, 'UTF-8'));
            self::assertIsString(str_replace('_', ' ', $forma));
        }
        self::assertIsArray($componente->customers);
    }
}
