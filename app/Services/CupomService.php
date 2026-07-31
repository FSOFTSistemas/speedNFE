<?php

namespace App\Services;

use App\Enums\SituacaoEnum;
use App\Exceptions\NotFoundException;
use App\Models\Cupom;
use Carbon\Carbon;

class CupomService
{
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
