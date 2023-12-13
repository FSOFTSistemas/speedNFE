<?php

namespace App\Services;

use App\Models\MDFeProdPred;

class ProdPredService
{

    public function createProdPred($carga_predominante, $ncm, $codigo_gtin, $lat_carregamento, $lon_carregamento, $lat_descarregamento, $lon_descarregamento)
    {
        return MDFeProdPred::create([
            'carga_predominante' => $carga_predominante,
            'ncm' => $ncm,
            'codigo_gtin' => $codigo_gtin,
            'lat_carrregamento' => $lat_carregamento,
            'lon_carrregamento' => $lon_carregamento,
            'lat_descarrregamento' => $lat_descarregamento,
            'lon_descarrregamento' => $lon_descarregamento
        ]);
    }

}