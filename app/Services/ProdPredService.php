<?php

namespace App\Services;

use App\Models\MDFeProdPred;

class ProdPredService
{

    public function createProdPred($carga_predominante, $ncm, $codigo_gtin, $lat_carregamento, $lon_carregamento, $lat_descarregamento, $lon_descarregamento, $mdfe)
    {
        return MDFeProdPred::create([
            'carga_predominante' => $carga_predominante,
            'ncm' => $ncm,
            'codigo_gtin' => is_int($codigo_gtin) ? $codigo_gtin : 'SEM GTIN',
            'lat_carregamento' => $lat_carregamento,
            'lon_carregamento' => $lon_carregamento,
            'lat_descarregamento' => $lat_descarregamento,
            'lon_descarregamento' => $lon_descarregamento,
            'mdfe_id' => $mdfe
        ]);
    }

    public function updateProdPred($carga_predominante, $ncm, $codigo_gtin, $lat_carregamento, $lon_carregamento, $lat_descarregamento, $lon_descarregamento, $proPred_id)
    {
        $proPred = MDFeProdPred::find($proPred_id);
        return $proPred->update([
            'carga_predominante' => $carga_predominante,
            'ncm' => $ncm,
            'codigo_gtin' => is_int($codigo_gtin) ? $codigo_gtin : 'SEM GTIN',
            'lat_carregamento' => $lat_carregamento,
            'lon_carregamento' => $lon_carregamento,
            'lat_descarregamento' => $lat_descarregamento,
            'lon_descarregamento' => $lon_descarregamento,
        ]);
    }

}