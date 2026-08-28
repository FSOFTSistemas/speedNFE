<?php

namespace Tests\Unit;

use App\Services\PreVendaCalculator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PreVendaCalculatorTest extends TestCase
{
    public function test_calcula_itens_descontos_acrescimos_e_total(): void
    {
        $resultado = (new PreVendaCalculator)->calcular([
            [
                'produto_id' => 1,
                'quantidade' => 2,
                'valor_unitario' => 10,
                'desconto' => 2,
                'acrescimo' => 1,
            ],
            [
                'produto_id' => 2,
                'quantidade' => 1.5,
                'valor_unitario' => 20,
                'desconto' => 0,
                'acrescimo' => 0,
            ],
        ], 3, 2);

        $this->assertSame(50.0, $resultado['subtotal']);
        $this->assertSame(3.0, $resultado['desconto']);
        $this->assertSame(2.0, $resultado['acrescimo']);
        $this->assertSame(48.0, $resultado['total']);
        $this->assertSame(19.0, $resultado['itens'][0]['total']);
        $this->assertSame(30.0, $resultado['itens'][1]['total']);
    }

    public function test_rejeita_desconto_maior_que_o_valor_do_item(): void
    {
        $this->expectException(ValidationException::class);

        (new PreVendaCalculator)->calcular([
            [
                'produto_id' => 1,
                'quantidade' => 1,
                'valor_unitario' => 10,
                'desconto' => 11,
            ],
        ]);
    }

    public function test_rejeita_desconto_global_maior_que_o_total(): void
    {
        $this->expectException(ValidationException::class);

        (new PreVendaCalculator)->calcular([
            [
                'produto_id' => 1,
                'quantidade' => 1,
                'valor_unitario' => 10,
            ],
        ], 11);
    }
}
