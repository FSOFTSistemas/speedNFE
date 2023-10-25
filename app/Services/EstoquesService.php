<?php
namespace App\Services;

use App\Models\Estoque;
use Exception;
use Illuminate\Support\Facades\DB;

class EstoquesService{
    public function __construct(){}

    public function update($id, $estoque){
        $est = Estoque::findOrFail($id);
        $est->update([
            'estoque' => $estoque
        ]);

        return 1;
    }

    public function um($id){
        return Estoque::findOrFail($id);
    }

    public function destroy($id){
        try{
            $estoque = Estoque::findOrFail($id);

            return $estoque->delete();
        } catch (Exception $e) {
            return $e;
        }
    }

    public function store($id_empresa, $produto, $estoque){
        try{
            Estoque::create([
                'empresa_id' => $id_empresa,
                'produto_id' => $produto,
                'estoque' => $estoque,
                'entradas' => $estoque,
                'saidas' => 0
            ]);
            return 1;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function show($id_empresa){
        if ($id_empresa == 1){
            return DB::table('estoques')
            ->select('estoques.*', 'produtos.produto', 'empresas.fantasia')
            ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
            ->join('empresas', 'empresas.id', '=', 'estoques.empresa_id')
            ->get();
            ;
        } else {
            return DB::table('estoques')
            ->select('estoques.*', 'produtos.produto', 'empresas.fantasia')
            ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
            ->join('empresas', 'empresas.id', '=', 'estoques.empresa_id')
            ->where('estoques.empresa_id', '=', $id_empresa)
            ->get();
        }
    }
}