<?php

namespace Tests\Feature;

use App\Http\Livewire\NFCe;
use Tests\TestCase;

class NFCeViewTest extends TestCase
{
    public function test_tela_do_pdv_renderiza_sem_clientes_ou_itens(): void
    {
        $component = new NFCe;
        $component->customers = collect();
        $component->cliente = ['id' => null, 'nome' => 'Consumidor Final'];

        $html = view('livewire.n-f-ce', get_object_vars($component))->render();

        $this->assertStringContainsString('Ponto de Venda', $html);
        $this->assertStringContainsString('SearchClientModal', $html);
    }
}
