<?php

namespace App\Services;

use App\Enums\SituacaoEnum;
use App\Exceptions\NotFoundException;
use App\Models\Cupom;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CupomService
{
    /**
     * Cria um cupom (venda de PDV) pela API, com os itens e as formas de
     * pagamento associados, e baixa o estoque imediatamente. Espelha a
     * orquestração de CupomController::store(), reaproveitando os mesmos
     * services (ItemCupomService, CupomFormaService, EstoquesService) para
     * não duplicar a lógica de negócio.
     *
     * Diferente do PedidosService::criarApi(), aqui os totais de cada item
     * são calculados a partir de quantidade/unitário/desconto/acréscimo em
     * vez de confiar em subtotal/total vindos do cliente.
     *
     * @param  array  $data  Payload já validado por StoreCupomRequest (chaves: cliente_id, troco,
     *                       itens, formas).
     */
    public function criarApi(
        array $data,
        int $empresaId,
        ItemCupomService $itemCupomService,
        CupomFormaService $cupomFormaService,
        EstoquesService $estoqueService,
        EmpresasService $empresaService
    ): Cupom {
        return DB::transaction(function () use ($data, $empresaId, $itemCupomService, $cupomFormaService, $estoqueService, $empresaService) {
            $subtotal = 0;
            $descontoTotal = 0;
            $acrescimoTotal = 0;
            $itens = [];

            foreach ($data['itens'] as $item) {
                $itemSubtotal = $item['quantidade'] * $item['unitario'];
                $itemDesconto = $item['desconto'] ?? 0;
                $itemAcrescimo = $item['acrescimo'] ?? 0;

                $subtotal += $itemSubtotal;
                $descontoTotal += $itemDesconto;
                $acrescimoTotal += $itemAcrescimo;

                $itens[] = [
                    'qtde' => $item['quantidade'],
                    'unitario' => $item['unitario'],
                    'desconto' => $itemDesconto,
                    'acrescimo' => $itemAcrescimo,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemSubtotal - $itemDesconto + $itemAcrescimo,
                    'prodId' => $item['produto_id'],
                ];
            }

            $valorTotal = $subtotal - $descontoTotal + $acrescimoTotal;

            $cupomId = $this->createCupom(
                $empresaService->incrementCupomSequence($empresaId),
                $valorTotal,
                $descontoTotal,
                $acrescimoTotal,
                $subtotal,
                $data['troco'] ?? 0,
                $data['cliente_id'] ?? null,
                $empresaId
            );

            $itemCupomService->createItemsCupom($itens, $cupomId);
            $cupomFormaService->createCupomFormas($data['formas'], $cupomId);

            foreach ($itens as $item) {
                $estoqueService->out($item['prodId'], $item['qtde']);
            }

            return $this->getCupom($cupomId)->load('itens.produto', 'formasPagamento', 'cliente');
        });
    }

    public function getCompanyCoupons($companyId, $dataInicio = null, $dataFim = null, $situacao = null)
    {
        $query = Cupom::with(['cliente', 'nfce'])->where('empresa_id', $companyId);

        if ($dataInicio && $dataFim) {
            $query->whereBetween('data', [
                Carbon::parse($dataInicio)->startOfDay(),
                Carbon::parse($dataFim)->endOfDay(),
            ]);
        }

        if ($situacao) {
            $query->where('situacao', $situacao);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function getCupom($id)
    {
        return Cupom::find($id);
    }

    public function createCupom($nroCupom, $total, $desconto, $acrescimo, $subtotal, $troco, $clientId, $empresaId)
    {
        return Cupom::create([
            'nroCupom' => $nroCupom,
            'data' => now(),
            'situacao' => SituacaoEnum::ATIVO,
            'gerado_nfce' => false,
            'contingencia' => false,
            'total' => $total,
            'desconto' => $desconto,
            'acrescimo' => $acrescimo,
            'subtotal' => $subtotal,
            'troco' => $troco ?? 0,
            'cliente_id' => $clientId,
            'empresa_id' => $empresaId,
        ])->id;
    }

    public function getOutstandingCouponsOfTheDay($companyId, $day)
    {
        $coupons = Cupom::whereEmpresaId($companyId)->where('data', 'like', $day.'%')->where('gerado_nfce', false)->get();
        if ($coupons->isEmpty()) {
            throw new NotFoundException('Não foram encontrados cupoms para data selecionada!');
        }

        return $coupons;
    }

    public function rejectedCoupon($couponId)
    {
        $coupon = $this->getCupom($couponId);
        $coupon->situacao = SituacaoEnum::REJEITADO;
        $coupon->save();
    }

    public function cancelCoupon($couponId)
    {
        $coupon = $this->getCupom($couponId);
        $coupon->situacao = SituacaoEnum::CANCELADO;
        $coupon->save();
    }

    public function updateCoupon($coupon)
    {
        $coupon->gerado_nfce = true;
        $coupon->contingencia = true;
        $coupon->situacao = SituacaoEnum::ATIVO;
        $coupon->save();
    }
}
