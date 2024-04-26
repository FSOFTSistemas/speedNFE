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

    public function insertProductsList($request, $empresaId)
    {
        foreach ($request as $prod) {
            Produto::create([
                'categoria_id' => 1,
                'empresa_id' => $empresaId,
                'codigo' => $prod->prod->cEAN,
                'produto' => $prod->prod->xProd,
                'precocusto' => $prod->precoCusto,
                'precovenda' => $prod->precovenda,
                'ncm' => $prod->prod->NCM,
                'cfop_interno' => $prod->cfopinterno,
                'cst_csosn' => $prod->cst_csosn,
                'cst_pis' => $prod->cst_pis,
                'cst_cofins' => $prod->cst_cofins,
                'cst' => $prod->cst,
                'icms' => $prod->icms,
                'pis' => $prod->pis,
                'cofins' => $prod->cofins,
                'ipi' => $prod->ipi,
                'cfop_externo' => $prod->cfopexterno,
                'un' => $prod->prod->uCom,
                'tpProd' => $prod->tpProd,
                'tpVeic' => $prod->tpVeic,
                'chassiVeic' => $prod->chassiVeic,
                'renavanVeic' => $prod->renavanVeic,
                'anoFabVeic' => $prod->anoFabVeic,
                'anoModVeic' => $prod->anoModVeic,
                'pesoLVeic' => $prod->pesoLVeic,
                'pesoBVeic' => $prod->pesoBVeic,
                'distVeic' => $prod->distVeic,
                'combVeic' => $prod->combVeic,
                'nMotorVeic' => $prod->nMotorVeic,
                'cvVeic' => $prod->cvVeic,
                'cm3Veic' => $prod->cm3Veic,
                'serieVeic' => $prod->serieVeic,
                'tpPVeic' => $prod->tpPVeic,
                'corVeic' => $prod->corVeic,
                'cCorVeic' => $prod->cCorVeic,
                'cCorMontVeic' => $prod->cCorMontVeic,
                'cMarcaVeic' => $prod->cMarcaVeic,
                'condVeic' => $prod->condVeic,
                'espVeic' => $prod->espVeic,
                'vinVeic' => $prod->vinVeic,
                'lotVeic' => $prod->lotVeic,
                'restriVeic' => $prod->restriVeic,
                'cargaVeic' => $prod->cargaVeic,
                'operVeic' => $prod->operVeic
            ]);
        }
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