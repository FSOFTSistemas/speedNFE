<?php

namespace App\Services;

use App\Enums\SituacaoEnum;
use App\Models\Cupom;

class CupomService
{

    public function getCompanyCoupons($companyId)
    {
        return Cupom::where("empresa_id", $companyId)->get();
    }

    public function getCupom($id){
        return Cupom::find($id);
    }

    public function createCupom($nroCupom, $total, $desconto, $acrescimo, $subtotal, $troco, $clientId, $empresaId) {
        return Cupom::create([
            'nroCupom' => $nroCupom,
            'situacao' => SituacaoEnum::ATIVO,
            'gerado_nfce' => false,
            'contingencia' => false,
            'total' => $total,
            'desconto' => $desconto,
            'acrescimo' => $acrescimo,
            'subtotal' => $subtotal,
            'troco' => $troco > 0 ? $troco : 0,
            'cliente_id' => $clientId,
            'empresa_id' => $empresaId
        ])->id;
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
