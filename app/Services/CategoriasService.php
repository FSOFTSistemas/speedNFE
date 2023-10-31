<?php

namespace App\Services;

use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class CategoriasService
{

    public function status($id)
    {
        $cat = Categoria::findOrFail($id);
        if ($cat->status == 0) {
            $cat->update([
                'status' => 1,
            ]);
        } else {
            $cat->update([
                'status' => 0,
            ]);
        }
        return 1;
    }

    public function store($descricao, $empresa)
    {
        return Categoria::create([
            'descricao' => $descricao,
            'empresa_id' => $empresa,
            'status' => 1,
        ]);
    }

    public function todas($empresa)
    {
        return DB::table('categorias')
            ->select('categorias.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'categorias.empresa_id')
            ->where('categorias.empresa_id', $empresa)
            ->where('categorias.status', '1')
            ->get();
    }

    public function todasCategorias()
    {
        return Categoria::select('categorias.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'categorias.empresa_id')
            ->get();
    }
}
