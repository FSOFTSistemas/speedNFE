<?php

namespace App\Services;

use App\Models\Cidade;
use App\Models\LatLon;

class CidadeService {

    public function buscarCidades()
    {
        return Cidade::all();
    }

    public function buscarCidadesPorUf($uf)
    {
        return Cidade::where('uf', $uf)->get();
    }

    public function buscarCidade($cidade)
    {
        return Cidade::where('cidade', $cidade)->first();
    }

    public function buscarLatLonByUf($uf) {
        return LatLon::where('uf', $uf)->get();
    }

    public function buscarLatLonByMunicipio($municipio) {
        return LatLon::where('municipio', $municipio)->first();
    }

}