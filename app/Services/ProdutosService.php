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
            // dd($prod[0]);
            Produto::create([
                'categoria_id' => 1,
                'empresa_id' => $empresaId,
                'codigo' => $prod[0]['cEAN'],
                'produto' => $prod[0]['xProd'],
                'precocusto' => $prod[0]['vProd'],
                'precovenda' => $prod[0]['vVendaProd'],
                'ncm' => $prod[0]['NCM'],
                'cfop_interno' => $prod[0]['cfopinterno'] ?? 123,
                'cst_csosn' => $prod[0]['cst_csosn'] ?? 123,
                'cst_pis' => $prod[0]['cst_pis'] ?? 123,
                'cst_cofins' => $prod[0]['cst_cofins'] ?? 123,
                'cst' => $prod[0]['cst'] ?? 123,
                'icms' => $prod[0]['ICMS'],
                'pis' => $prod[0]['PIS'],
                'cofins' => $prod[0]['COFINS'],
                'ipi' => $prod[0]['IPI'],
                'cfop_externo' => $prod[0]['cfopexterno'] ?? 123,
                'un' => $prod[0]['uCom'],
                'tpProd' => $prod[0]['tpProd'] ?? 123,
                'tpVeic' => $prod[0]['tpVeic'] ?? 123,
                'chassiVeic' => $prod[0]['chassiVeic'] ?? 123,
                'renavanVeic' => $prod[0]['renavanVeic'] ?? 123,
                'anoFabVeic' => $prod[0]['anoFabVeic'] ?? 123,
                'anoModVeic' => $prod[0]['anoModVeic'] ?? 123,
                'pesoLVeic' => $prod[0]['pesoLVeic'] ?? 123,
                'pesoBVeic' => $prod[0]['pesoBVeic'] ?? 123,
                'distVeic' => $prod[0]['distVeic'] ?? 123,
                'combVeic' => $prod[0]['combVeic'] ?? 123,
                'nMotorVeic' => $prod[0]['nMotorVeic'] ?? 123,
                'cvVeic' => $prod[0]['cvVeic'] ?? 123,
                'cm3Veic' => $prod[0]['cm3Veic'] ?? 123,
                'serieVeic' => $prod[0]['serieVeic'] ?? 123,
                'tpPVeic' => $prod[0]['tpPVeic'] ?? 123,
                'corVeic' => $prod[0]['corVeic'] ?? 123,
                'cCorVeic' => $prod[0]['cCorVeic'] ?? 123,
                'cCorMontVeic' => $prod[0]['cCorMontVeic'] ?? 123,
                'cMarcaVeic' => $prod[0]['cMarcaVeic'] ?? 123,
                'condVeic' => $prod[0]['condVeic'] ?? 123,
                'espVeic' => $prod[0]['espVeic'] ?? 123,
                'vinVeic' => $prod[0]['vinVeic'] ?? 123,
                'lotVeic' => $prod[0]['lotVeic'] ?? 123,
                'restriVeic' => $prod[0]['restriVeic'] ?? 123,
                'cargaVeic' => $prod[0]['cargaVeic'] ?? 123,
                'operVeic' => $prod[0]['operVeic'] ?? 123
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