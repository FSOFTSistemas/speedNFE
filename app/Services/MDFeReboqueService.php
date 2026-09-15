<?php

namespace App\Services;

use App\Models\MDFeReboque;

class MDFeReboqueService
{

    public function createReboque($reboque_id, $mdfe_id)
    {
        if (!MDFeReboque::where('reboque_id', $reboque_id)->where('mdfe_id', $mdfe_id)->exists()){
            return MDFeReboque::create([
                'reboque_id' => $reboque_id,
                'mdfe_id' => $mdfe_id
            ]);
        }
    }

    public function deleteReboques($reboques, $mdfeId)
    {
        $reboques_ids = array_map(function($item) {
            $parts = explode('/', $item);
            return $parts[0];
        }, $reboques);
        $ids = MDFeReboque::where('mdfe_id', $mdfeId)->get()->pluck('reboque_id')->toArray();
        $reboquesParaDeletar = array_diff($ids, $reboques_ids);
        return MDFeReboque::whereIn('reboque_id', $reboquesParaDeletar)->where('mdfe_id', $mdfeId)->delete();
    }

}