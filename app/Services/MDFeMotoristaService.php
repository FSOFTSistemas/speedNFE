<?php

namespace App\Services;

use App\Models\MDFeMotorista;

class MDFeMotoristaService
{

    public function createMotorista($motorista_id, $mdfe_id)
    {
        if (!MDFeMotorista::where('motorista_id', $motorista_id)->where('mdfe_id', $mdfe_id)->exists()) {
            return MDFeMotorista::create([
                'motorista_id' => $motorista_id,
                'mdfe_id' => $mdfe_id
            ]);
        }
    }

    public function deleteMotoristas($motoristas, $mdfeId)
    {
        $motoristas_ids = array_map(function($item) {
            $parts = explode('/', $item);
            return $parts[0];
        }, $motoristas);
        $ids = MDFeMotorista::where('mdfe_id', $mdfeId)->get()->pluck('id')->toArray();
        $motoristasParaDeletar = array_diff($ids, $motoristas_ids);
        return MDFeMotorista::whereIn('id', $motoristasParaDeletar)->delete();
    }

}