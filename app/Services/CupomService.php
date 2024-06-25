<?php

namespace App\Services;

use App\Enums\SituacaoEnum;
use App\Exceptions\NotFoundException;
use App\Models\Cupom;

class CupomService
{

    public function getCompanyCoupons($companyId)
    {
        return Cupom::where("empresa_id", $companyId)->get();
    }

    public function getCupom($id)
    {
        return Cupom::find($id);
    }

    public function createCupom($nroCupom, $total, $desconto, $acrescimo, $subtotal, $troco, $clientId, $empresaId)
    {
        return Cupom::create([
            'nroCupom' => $nroCupom,
            'situacao' => SituacaoEnum::ATIVO,
            'gerado_nfce' => false,
            'contingencia' => false,
            'total' => $total,
            'desconto' => $desconto,
            'acrescimo' => $acrescimo,
            'subtotal' => $subtotal,
            'troco' => $troco ?? 0,
            'cliente_id' => $clientId,
            'empresa_id' => $empresaId
        ])->id;
    }

    public function getOutstandingCouponsOfTheDay($companyId, $day)
    {
        $coupons = Cupom::whereEmpresaId($companyId)->where('data', 'like', $day . '%')->where('gerado_nfce', false)->get();
        if ($coupons->isEmpty()) {
            throw new NotFoundException("Não foram encontrados cupoms para data selecionada!");
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
        $coupon->save();
    }
}
