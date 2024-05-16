<?php

namespace App\Services;

use App\Models\NFCe;

class NFCeService
{

    public function getCompanyNFCes($empresaId)
    {
        return NFCe::whereEmpresaId($empresaId)->get();
    }

}