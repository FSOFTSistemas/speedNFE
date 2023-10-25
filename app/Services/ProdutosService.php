<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Produto;
use Exception;
use Illuminate\Support\Facades\DB;

class ProdutosService{
    public function __construct()
    {

    }

    public function salvar($id, $codigo, $produto, $precocusto, $precovenda, $ncm, $cfopinterno, $cst_csosn, $cst_pis, $cst_cofins, $cst, $icms, $pis, $cofins, $ipi, $cfopexterno, $un){
        try {
            $prod = Produto::findOrFail($id);
            $prod->update([
                'codigo' => $codigo,
                'produto' => $produto,
                'precocusto' => $precocusto,
                'precovenda' => $precovenda,
                'ncm' => $ncm,
                'cfop_interno' => $cfopinterno,
                'cst_csosn' => $cst_csosn,
                'cst_pis' => $cst_pis,
                'cst_cofins' => $cst_cofins,
                'cst' => $cst,
                'icms' => $icms,
                'pis' => $pis,
                'cofins' => $cofins,
                'ipi' => $ipi,
                'cfop_externo' => $cfopexterno,
                'un' => $un
            ]);
            return 1;
        } catch (Exception $e){
            return $e;
        }
    }

    public function um($id){
        return Produto::findOrFail($id);
    }

    public function destroy($id){
        try{
            $produto = Produto::findOrFail($id);
            return $produto->delete();
        } catch (Exception $e) {
            return $e;
        }
    }

    public function store($categoria, $empresa, $codigo, $produto, $precocusto, $precovenda, $ncm, $cfopinterno, $cst_csosn, $cst_pis, $cst_cofins, $cst, $icms, $pis, $cofins, $ipi, $cfopexterno, $un){
        try {
            Produto::create([
                'categoria_id' => $categoria,
                'empresa_id' => $empresa,
                'codigo' => $codigo,
                'produto' => $produto,
                'precocusto' => $precocusto,
                'precovenda' => $precovenda,
                'ncm' => $ncm,
                'cfop_interno' => $cfopinterno,
                'cst_csosn' => $cst_csosn,
                'cst_pis' => $cst_pis,
                'cst_cofins' => $cst_cofins,
                'cst' => $cst,
                'icms' => $icms,
                'pis' => $pis,
                'cofins' => $cofins,
                'ipi' => $ipi,
                'cfop_externo' => $cfopexterno,
                'un' => $un
            ]);
            return 1;
        } catch (Exception $e){
            return $e;
        }
    }

    public function todos($id){
        if($id == 1){
            $id = '%';
        }
        return DB::table('produtos')
        ->select('produtos.*', 'empresas.fantasia', 'categorias.descricao')
        ->join('categorias', 'categorias.id', '=', 'produtos.categoria_id')
        ->join('empresas', 'empresas.id', '=', 'produtos.empresa_id')
        ->where('produtos.empresa_id', 'like', $id)
        ->get();
    }

    public function umVenda($id){
        return DB::table('produtos')
        ->select('produtos.*', 'estoques.estoque')
        ->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
        ->where('produtos.id', '=', $id)
        ->first();
    }

    public function todosVenda($id){
        return Produto::where('produtos.empresa_id', '=', $id)
        ->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
        ->get();
    }

}