<?php

namespace Tests\Unit;

use App\Enums\SituacaoEnum;
use App\Http\Controllers\Traits\EnviaNFCe;
use App\Models\Cupom;
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FluxoDeCaixaService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EnviaNFCeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function tearDown(): void
    {
        DB::clearResolvedInstance('db');
        parent::tearDown();
    }

    /** @dataProvider cuponsBloqueados */
    public function test_nao_transmite_cupom_inexistente_cancelado_ou_emitido(?array $dados, bool $temNota): void
    {
        $db = Mockery::mock();
        DB::swap($db);
        $db->shouldReceive('beginTransaction')->once()->ordered();
        $db->shouldReceive('rollBack')->once()->ordered();

        $cupom = $dados === null ? null : new Cupom($dados);
        if ($cupom) {
            $cupom->setRelation('nfce', $temNota ? new \App\Models\NFCe : null);
        }
        $cupons = Mockery::mock(CupomService::class);
        $cupons->shouldReceive('getCupomForUpdate')->once()->with(10)->andReturn($cupom);

        $emissor = new class
        {
            use EnviaNFCe { _enviarNFCePeloId as public enviar; }

            private function makeNFCeService($empresa)
            {
                throw new \LogicException('Não deve acessar certificado nem transmitir este cupom.');
            }
        };

        $resultado = $emissor->enviar(
            10, $cupons, Mockery::mock(EmpresasService::class),
            Mockery::mock(EstoquesService::class), Mockery::mock(FluxoDeCaixaService::class)
        );

        self::assertSame('warning', $resultado->status);
    }

    public static function cuponsBloqueados(): array
    {
        return [
            'inexistente' => [null, false],
            'cancelado' => [['situacao' => 'CANCELADO', 'gerado_nfce' => false], false],
            'emitido' => [['situacao' => 'ATIVO', 'gerado_nfce' => true], false],
            'xml existente' => [['situacao' => 'ATIVO', 'gerado_nfce' => false], true],
        ];
    }

    public function test_autorizacao_normal_nao_marca_contingencia(): void
    {
        $cupom = Mockery::mock(Cupom::class)->makePartial();
        $cupom->shouldReceive('save')->once()->andReturn(true);

        (new CupomService)->updateCoupon($cupom);

        self::assertTrue($cupom->gerado_nfce);
        self::assertFalse($cupom->contingencia);
        self::assertSame(SituacaoEnum::ATIVO->value, $cupom->situacao);
    }
}
