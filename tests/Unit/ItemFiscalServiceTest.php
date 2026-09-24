<?php

namespace Tests\Unit;

use App\Models\Produto;
use App\Services\ItemFiscalService;
use PHPUnit\Framework\TestCase;

class ItemFiscalServiceTest extends TestCase
{
    private function produto(array $atributos): Produto
    {
        return (new Produto())->forceFill(array_merge([
            'cst_csosn' => '0',
            'icms' => 18,
            'cst_pis' => '01',
            'pis' => 1.65,
            'cst_cofins' => '01',
            'cofins' => 7.6,
        ], $atributos));
    }

    public function test_padrao_regime_normal_usa_tributos_do_produto_e_cfop_do_pedido()
    {
        $fiscal = (new ItemFiscalService())->padrao($this->produto([]), 2, 50, '5102', 3);

        $this->assertSame('5102', $fiscal['cfop']);
        $this->assertSame('00', $fiscal['cst_csosn']);
        $this->assertEquals(100, $fiscal['icms_base']);
        $this->assertEquals(18, $fiscal['icms_valor']);
        $this->assertEquals(1.65, $fiscal['pis_valor']);
        $this->assertEquals(7.6, $fiscal['cofins_valor']);
        $this->assertEquals(0, $fiscal['icms_st_valor']);
    }

    public function test_padrao_simples_sem_credito_nao_destaca_icms()
    {
        $fiscal = (new ItemFiscalService())->padrao($this->produto(['cst_csosn' => '102', 'pis' => 0, 'cofins' => 0]), 1, 10, '5102', 1);

        $this->assertSame('102', $fiscal['cst_csosn']);
        $this->assertEquals(0, $fiscal['icms_base']);
        $this->assertEquals(0, $fiscal['icms_valor']);
        $this->assertEquals(0, $fiscal['pis_base']);
    }

    public function test_para_colunas_mapeia_cfop_e_marca_personalizado()
    {
        $colunas = (new ItemFiscalService())->paraColunas([
            'cfop' => '5405',
            'cst_csosn' => '10',
            'icms_st_base' => '150,50',
            'icms_st_aliquota' => '18',
            'icms_st_valor' => '9.09',
        ]);

        $this->assertTrue($colunas['fiscal_personalizado']);
        $this->assertSame('5405', $colunas['cfop_item']);
        $this->assertArrayNotHasKey('cfop', $colunas);
        $this->assertEquals(150.5, $colunas['icms_st_base']);
        $this->assertEquals(0, $colunas['pis_valor']);
    }

    public function test_para_colunas_sem_fiscal_mantem_padrao_do_produto()
    {
        $this->assertSame(['fiscal_personalizado' => false], (new ItemFiscalService())->paraColunas(null));
        $this->assertSame(['fiscal_personalizado' => false], (new ItemFiscalService())->paraColunas([]));
    }

    public function test_reajuste_mantem_proporcao_da_base_e_recalcula_valores()
    {
        $fiscal = (new ItemFiscalService())->reajustarParaNovoValor([
            'icms_base' => 80, 'icms_aliquota' => 18, 'icms_valor' => 14.4,
            'icms_st_base' => 0, 'icms_st_aliquota' => 0, 'icms_st_valor' => 0,
            'pis_base' => 100, 'pis_aliquota' => 1.65, 'pis_valor' => 1.65,
            'cofins_base' => 100, 'cofins_aliquota' => 7.6, 'cofins_valor' => 7.6,
        ], 100, 200);

        $this->assertEquals(160, $fiscal['icms_base']);
        $this->assertEquals(28.8, $fiscal['icms_valor']);
        $this->assertEquals(200, $fiscal['pis_base']);
        $this->assertEquals(3.3, $fiscal['pis_valor']);
    }
}
