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
        $this->assertSame(1, substr_count($html, 'id="SearchClientModal"'));
        $this->assertSame(1, substr_count($html, 'id="AddProdModal"'));
        $this->assertSame(1, substr_count($html, 'name="valorTotal"'));
        $this->assertStringNotContainsString('bootstrap@5', $html);
    }

    public function test_formulario_preserva_itens_pagamentos_e_totais_sem_duplicar_campos(): void
    {
        $component = new NFCe;
        $component->customers = collect();
        $component->cliente = ['id' => 7, 'nome' => 'Cliente teste'];
        $component->itens = [[
            'prodId' => 12, 'produto' => 'Produto teste', 'codigo' => '0012',
            'qtde' => 2, 'unitario' => 25, 'desconto' => 0, 'acrescimo' => 0,
            'total' => 50, 'subtotal' => 50,
        ]];
        $component->subtotal = 50;
        $component->valorTotal = 45;
        $component->descontoTotalCalculado = 5;
        $component->valorPago = 50;
        $component->troco = 5;
        $component->aReceber = -5;
        $component->formas = ['DINHEIRO', 'PIX', 'CARTÃO/CRÉDITO', 'CARTÃO/DÉBITO'];
        $component->formasSelecionadas = ['DINHEIRO' => 30, 'PIX' => 20];
        $component->showPaymentArea = 'block';

        $html = view('livewire.n-f-ce', get_object_vars($component))->render();
        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new \DOMXPath($document);

        foreach ([
            'cliente[id]' => '7', 'itens[0][prodId]' => '12', 'itens[0][qtde]' => '2',
            'itens[0][total]' => '50', 'formas[DINHEIRO]' => '30', 'formas[PIX]' => '20',
            'valorTotal' => '45', 'subtotal' => '50', 'descontoTotal' => '5',
            'troco' => '5', 'aReceber' => '0',
        ] as $name => $value) {
            $inputs = $xpath->query('//input[@name="'.$name.'"]');
            $this->assertCount(1, $inputs, $name);
            $this->assertSame($value, $inputs->item(0)->getAttribute('value'), $name);
        }
        $this->assertCount(0, $xpath->query('//button[@id="pdv-finish"]/@disabled'));
    }
}
