<?php

namespace App\Services;

use App\Models\Cupom;

class CupomService
{

    public function buscarCupom($id){
        return Cupom::find($id);
    }

}
