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
            'cst_csosn' => '00', 'cst_pis' => '01', 'cst_cofins' => '01',
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

    public function test_padrao_desconta_o_desconto_da_base()
    {
        $fiscal = (new ItemFiscalService())->padrao($this->produto([]), 1, 100, '5102', 3, 10);

        $this->assertEquals(90, $fiscal['icms_base']);
        $this->assertEquals(16.2, $fiscal['icms_valor']);
        $this->assertEquals(90, $fiscal['pis_base']);
    }

    public function test_cst_10_calcula_st_com_mva_deduzindo_icms_proprio()
    {
        $service = new ItemFiscalService();
        $fiscal = $service->padrao($this->produto([]), 1, 100, '5405', 3);

        $fiscal['cst_csosn'] = '10';
        $fiscal = $service->aplicarRegras($fiscal, 'cst_csosn', 100, 0, false, $this->produto([]));
        $fiscal['icms_st_mva'] = 40;
        $fiscal = $service->aplicarRegras($fiscal, 'icms_st_mva', 100, 0, false, $this->produto([]));

        // Base ST = 100 x 1,40 = 140; ST = 140 x 18% - 18 = 7,20
        $this->assertEquals(140, $fiscal['icms_st_base']);
        $this->assertEquals(18, $fiscal['icms_st_aliquota']);
        $this->assertEquals(7.2, $fiscal['icms_st_valor']);
    }

    public function test_cst_20_reducao_de_base_nos_dois_sentidos()
    {
        $service = new ItemFiscalService();
        $fiscal = $service->padrao($this->produto(['cst_csosn' => '20']), 1, 200, '5102', 3);

        $fiscal['icms_reducao'] = 25;
        $fiscal = $service->aplicarRegras($fiscal, 'icms_reducao', 200, 0, false);
        $this->assertEquals(150, $fiscal['icms_base']);
        $this->assertEquals(27, $fiscal['icms_valor']);

        $fiscal['icms_base'] = 100;
        $fiscal = $service->aplicarRegras($fiscal, 'icms_base', 200, 0, false);
        $this->assertEquals(50, $fiscal['icms_reducao']);
        $this->assertEquals(18, $fiscal['icms_valor']);
    }

    public function test_troca_para_cst_sem_icms_zera_icms_e_st()
    {
        $service = new ItemFiscalService();
        $fiscal = $service->padrao($this->produto([]), 1, 100, '5102', 3);

        $fiscal['cst_csosn'] = '40';
        $fiscal = $service->aplicarRegras($fiscal, 'cst_csosn', 100, 0, false);

        $this->assertEquals(0, $fiscal['icms_base']);
        $this->assertEquals(0, $fiscal['icms_valor']);
        $this->assertEquals(0, $fiscal['icms_st_valor']);
    }

    public function test_pis_cofins_monofasico_zera_valores()
    {
        $service = new ItemFiscalService();
        $fiscal = $service->padrao($this->produto([]), 1, 100, '5102', 3);

        $fiscal['cst_pis'] = '04';
        $fiscal = $service->aplicarRegras($fiscal, 'cst_pis', 100, 0, false);

        $this->assertEquals(0, $fiscal['pis_base']);
        $this->assertEquals(0, $fiscal['pis_aliquota']);
        $this->assertEquals(0, $fiscal['pis_valor']);
        $this->assertEquals(7.6, $fiscal['cofins_valor']);
    }

    public function test_simples_202_deduz_icms_proprio_pela_aliquota_interna()
    {
        $service = new ItemFiscalService();
        $fiscal = $service->padrao($this->produto(['cst_csosn' => '202', 'icms' => 1.25]), 1, 100, '5405', 1);

        // No Simples a alíquota ST não vem do produto (ICMS do produto é o % de crédito)
        $this->assertEquals(0, $fiscal['icms_st_aliquota']);

        $fiscal['icms_st_mva'] = 50;
        $fiscal = $service->aplicarRegras($fiscal, 'icms_st_mva', 100, 0, true);
        $fiscal['icms_st_aliquota'] = 18;
        $fiscal = $service->aplicarRegras($fiscal, 'icms_st_aliquota', 100, 0, true);

        // Base ST = 150; ST = 150 x 18% - 100 x 18% = 27 - 18 = 9
        $this->assertEquals(150, $fiscal['icms_st_base']);
        $this->assertEquals(9, $fiscal['icms_st_valor']);
    }
}
