<?php

namespace App\Services;

use App\Models\MDFeReboque;

class MDFeReboqueService
{

    public function createReboque($reboque_id, $mdfe_id)
    {
        return MDFeReboque::create([
            'reboque_id' => $reboque_id,
            'mdfe_id' => $mdfe_id
        ]);
    }

}