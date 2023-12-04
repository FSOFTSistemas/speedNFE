<?php

namespace App\Services;

use App\Models\MDFE;

class NotasService
{

    public function buscarMDFes($empresaId)
    {
        return MDFE::select('m_d_f_e_s.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', 'm_d_f_e_s.empresaId')
            ->where('m_d_f_e_s.empresaId', $empresaId)
            ->get();
    }

}