<?php

namespace App\Services;

use App\Models\Produto;
use Exception;
use Illuminate\Support\Facades\DB;

class ProdutosService
{
    public function __construct()
    {

    }

    public function salvar($id, $categoria, $codigo, $produto, $precocusto, $precovenda, $ncm, $cfopinterno, $cst_csosn, $cst_pis, $cst_cofins,
    $tpVeic, $chassiVeic, $renavanVeic, $anoFabVeic, $anoModVeic, $pesoLVeic, $pesoBVeic, $distVeic, $combVeic, $nMotorVeic, $cvVeic, $cm3Veic, $serieVeic,
    $tpPVeic, $corVeic, $cCorVeic, $cCorMontVeic, $cMarcaVeic, $condVeic, $espVeic, $vinVeic, $lotVeic, $restriVeic, $cargaVeic, $operVeic, $cst, $icms, $pis,
    $cofins, $ipi, $cfopexterno, $un)
    {
        $prod = Produto::find($id);
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
            'un' => $un,
            'categoria_id' => $categoria,
            'tpVeic' => $tpVeic,
            'chassiVeic' => $chassiVeic,
            'renavanVeic' => $renavanVeic,
            'anoFabVeic' => $anoFabVeic,
            'anoModVeic' => $anoModVeic,
            'pesoLVeic' => $pesoLVeic,
            'pesoBVeic' => $pesoBVeic,
            'distVeic' => $distVeic,
            'combVeic' => $combVeic,
            'nMotorVeic' => $nMotorVeic,
            'cvVeic' => $cvVeic,
            'cm3Veic' => $cm3Veic,
            'serieVeic' => $serieVeic,
            'tpPVeic' => $tpPVeic,
            'corVeic' => $corVeic,
            'cCorVeic' => $cCorVeic,
            'cCorMontVeic' => $cCorMontVeic,
            'cMarcaVeic' => $cMarcaVeic,
            'condVeic' => $condVeic,
            'espVeic' => $espVeic,
            'vinVeic' => $vinVeic,
            'lotVeic' => $lotVeic,
            'restriVeic' => $restriVeic,
            'cargaVeic' => $cargaVeic,
            'operVeic' => $operVeic
        ]);
        return $prod;
    }

    public function um($id)
    {
        return Produto::select('produtos.*', 'categorias.descricao', 'categorias.status', 'empresas.fantasia')
            ->join('categorias', 'categorias.id', 'produtos.categoria_id')
            ->join('empresas', 'empresas.id', 'produtos.empresa_id')
            ->where('produtos.id', $id)
            ->first();
    }

    public function destroy($id)
    {
        $produto = Produto::find($id);
        return $produto->delete();
    }

    public function store($categoria, $empresa, $codigo, $produto, $precocusto, $precovenda, $ncm, $cfopinterno, $cst_csosn, $cst_pis, $cst_cofins,
    $cst, $icms, $pis, $cofins, $ipi, $cfopexterno, $un, $tpProd, $tpVeic, $chassiVeic, $renavanVeic, $anoFabVeic, $anoModVeic, $pesoLVeic, $pesoBVeic,
    $distVeic, $combVeic, $nMotorVeic, $cvVeic, $cm3Veic, $serieVeic, $tpPVeic, $corVeic, $cCorVeic, $cCorMontVeic, $cMarcaVeic, $condVeic, $espVeic,
    $vinVeic, $lotVeic, $restriVeic, $cargaVeic, $operVeic)
    {
            return Produto::create([
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
                'un' => $un,
                'tpProd' => $tpProd,
                'tpVeic' => $tpVeic,
                'chassiVeic' => $chassiVeic,
                'renavanVeic' => $renavanVeic,
                'anoFabVeic' => $anoFabVeic,
                'anoModVeic' => $anoModVeic,
                'pesoLVeic' => $pesoLVeic,
                'pesoBVeic' => $pesoBVeic,
                'distVeic' => $distVeic,
                'combVeic' => $combVeic,
                'nMotorVeic' => $nMotorVeic,
                'cvVeic' => $cvVeic,
                'cm3Veic' => $cm3Veic,
                'serieVeic' => $serieVeic,
                'tpPVeic' => $tpPVeic,
                'corVeic' => $corVeic,
                'cCorVeic' => $cCorVeic,
                'cCorMontVeic' => $cCorMontVeic,
                'cMarcaVeic' => $cMarcaVeic,
                'condVeic' => $condVeic,
                'espVeic' => $espVeic,
                'vinVeic' => $vinVeic,
                'lotVeic' => $lotVeic,
                'restriVeic' => $restriVeic,
                'cargaVeic' => $cargaVeic,
                'operVeic' => $operVeic
            ]);
    }

    public function todosProdutos()
    {
        return Produto::select('produtos.*', 'categorias.descricao', 'empresas.fantasia', 'empresas.cpf_cnpj', 'empresas.celular')
            ->join('categorias', 'categorias.id', 'produtos.categoria_id')
            ->join('empresas', 'empresas.id', 'produtos.empresa_id')
            ->get();
    }

    public function todos($id)
    {
        if ($id == 1) {
            $id = '%';
        }
        return DB::table('produtos')
            ->select('produtos.*', 'empresas.fantasia', 'categorias.descricao')
            ->join('categorias', 'categorias.id', '=', 'produtos.categoria_id')
            ->join('empresas', 'empresas.id', '=', 'produtos.empresa_id')
            ->where('produtos.empresa_id', 'like', $id)
            ->get();
    }

    public function umVenda($id)
    {
        return DB::table('produtos')
            ->select('produtos.*', 'estoques.estoque')
            ->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
            ->where('produtos.id', '=', $id)
            ->first();
    }

    public function todosVenda($id)
    {
        return Produto::where('produtos.empresa_id', '=', $id)
            ->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
            ->get();
    }

    public function contagemProdutos($empresa)
    {
        return Produto::where('empresa_id', '=', $empresa)
            ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE)')
            ->whereRaw('YEAR(created_at) = YEAR(CURRENT_DATE)')
            ->count();
    }

}