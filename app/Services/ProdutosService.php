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
        $productsList = [];
        foreach ($request as $prod) {
            if (!Produto::whereEmpresaId($empresaId)->where('chassiVeic', $prod[0]['chassi'] ?? -1)->exists()) {
                $produtoId = Produto::create([
                    'categoria_id' => $prod[0]['categoria'],
                    'empresa_id' => $empresaId,
                    'codigo' => $prod[0]['cEAN'],
                    'produto' => $prod[0]['xProd'],
                    'precocusto' => $prod[0]['vProd'],
                    'precovenda' => $prod[0]['vVendaProd'],
                    'ncm' => $prod[0]['NCM'],
                    'cfop_interno' => $prod[0]['CFOP_INTERNO'] ?? 123,
                    'cst_csosn' => $prod[0]['CSOSN'],
                    'cst_pis' => $prod[0]['CST_PIS'],
                    'cst_cofins' => $prod[0]['CST_COFINS'],
                    'cst' => $prod[0]['CST'],
                    'icms' => $prod[0]['ICMS'],
                    'pis' => $prod[0]['PIS'],
                    'cofins' => $prod[0]['COFINS'],
                    'ipi' => $prod[0]['IPI'],
                    'cfop_externo' => $prod[0]['CFOP_EXTERNO'] ?? 123,
                    'un' => $prod[0]['uCom'],
                    'tpProd' => $prod[0]['tpProd'],
                    'tpVeic' => $prod[0]['tpVeic'] ?? null,
                    'chassiVeic' => $prod[0]['chassi'] ?? null,
                    'renavanVeic' => '000000000',
                    'anoFabVeic' => $prod[0]['anoFab'] ?? null,
                    'anoModVeic' => $prod[0]['anoMod'] ?? null,
                    'pesoLVeic' => $prod[0]['pesoL'] ?? null,
                    'pesoBVeic' => $prod[0]['pesoB'] ?? null,
                    'distVeic' => $prod[0]['dist'] ?? null,
                    'combVeic' => $prod[0]['tpComb'] ?? null,
                    'nMotorVeic' => $prod[0]['nMotor'] ?? null,
                    'cvVeic' => $prod[0]['pot'] ?? null,
                    'cm3Veic' => $prod[0]['cilin'] ?? null,
                    'serieVeic' => $prod[0]['nSerie'] ?? null,
                    'tpPVeic' => $prod[0]['tpPint'] ?? null,
                    'corVeic' => $prod[0]['xCor'] ?? null,
                    'cCorVeic' => $prod[0]['cCor'] ?? null,
                    'cCorMontVeic' => $prod[0]['cCorDENATRAN'] ?? null,
                    'cMarcaVeic' => $prod[0]['cMod'] ?? null,
                    'condVeic' => $prod[0]['condVeic'] ?? null,
                    'espVeic' => $prod[0]['espVeic'] ?? null,
                    'vinVeic' => $prod[0]['VIN'] ?? null,
                    'lotVeic' => $prod[0]['lota'] ?? null,
                    'restriVeic' => $prod[0]['tpRest'] ?? null,
                    'cargaVeic' => $prod[0]['CMT'] ?? null,
                    'operVeic' => $prod[0]['tpOp'] ?? null
                ])->id;
                array_push($productsList, ['produtoId' => $produtoId, 'qtde' => $prod[0]['qCom']]);
            }
        }
        return $productsList;
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

    public function searchProdByFilter($filter)
    {
        $filter = $filter ?? '%';
        return Produto::where('produto', 'like', $filter)->get();
    }

}