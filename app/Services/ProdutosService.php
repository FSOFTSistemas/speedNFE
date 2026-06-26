<?php

namespace App\Services;

use App\Models\Produto;
use Exception;
use Illuminate\Support\Facades\DB;

class ProdutosService
{
    public function __construct() {}

    public function salvar(
        $id,
        $categoria,
        $codigo,
        $produto,
        $precocusto,
        $precovenda,
        $ncm,
        $cfopinterno,
        $cst_csosn,
        $cst_pis,
        $cst_cofins,
        $tpVeic,
        $chassiVeic,
        $renavanVeic,
        $anoFabVeic,
        $anoModVeic,
        $pesoLVeic,
        $pesoBVeic,
        $distVeic,
        $combVeic,
        $nMotorVeic,
        $cvVeic,
        $cm3Veic,
        $serieVeic,
        $tpPVeic,
        $corVeic,
        $cCorVeic,
        $cCorMontVeic,
        $cMarcaVeic,
        $condVeic,
        $espVeic,
        $vinVeic,
        $lotVeic,
        $restriVeic,
        $cargaVeic,
        $operVeic,
        $cst,
        $icms,
        $pis,
        $cofins,
        $ipi,
        $cfopexterno,
        $un,
        // --- NOVOS CAMPOS RTC (Adicionados ao final) ---
        $cClassTrib = null,
        $pIBS = null,
        $pCBS = null,
        $pIS_imposto = null,
        $cst_ibs_cbs = null
    ) {
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
            // --- VEICULOS ---
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
            'operVeic' => $operVeic,
            // --- RTC ---
            'cClassTrib' => $cClassTrib,
            'pIBS' => $pIBS,
            'pCBS' => $pCBS,
            'pIS_imposto' => $pIS_imposto,
            'cst_ibs_cbs' => $cst_ibs_cbs
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

    // public function insertProductsList($request, $empresaId)
    // {
    //     $productsList = [];
    //     foreach ($request as $prod) {
    //         if (!Produto::whereEmpresaId($empresaId)->where('chassiVeic', $prod[0]['chassi'] ?? -1)->exists()) {

    //             $descricaoProduto = trim($prod[0]['xProd']);

    //             $chassi = $prod[0]['chassi'] ?? '';

    //             if ($chassi) {
    //                 $descricaoProduto .= ' - ' . $chassi;
    //             }


    //             $produtoId = Produto::create([
    //                 'categoria_id' => $prod[0]['categoria'],
    //                 'empresa_id' => $empresaId,
    //                 'codigo' => $prod[0]['cEAN'],
    //                 'produto' => $descricaoProduto,
    //                 'precocusto' => $prod[0]['vProd'],
    //                 'precovenda' => $prod[0]['vVendaProd'],
    //                 'ncm' => $prod[0]['NCM'],
    //                 'cfop_interno' => $prod[0]['CFOP_INTERNO'] ?? 123,
    //                 'cst_csosn' => $prod[0]['CSOSN'],
    //                 'cst_pis' => $prod[0]['CST_PIS'],
    //                 'cst_cofins' => $prod[0]['CST_COFINS'],
    //                 'cst' => $prod[0]['CST'],
    //                 'icms' => $prod[0]['ICMS'],
    //                 'pis' => $prod[0]['PIS'],
    //                 'cofins' => $prod[0]['COFINS'],
    //                 'ipi' => $prod[0]['IPI'],
    //                 'cfop_externo' => $prod[0]['CFOP_EXTERNO'] ?? 123,
    //                 'un' => $prod[0]['uCom'],
    //                 'tpProd' => $prod[0]['tpProd'],
    //                 'tpVeic' => $prod[0]['tpVeic'] ?? null,
    //                 'chassiVeic' => $prod[0]['chassi'] ?? null,
    //                 'renavanVeic' => '000000000',
    //                 'anoFabVeic' => $prod[0]['anoFab'] ?? null,
    //                 'anoModVeic' => $prod[0]['anoMod'] ?? null,
    //                 'pesoLVeic' => $prod[0]['pesoL'] ?? null,
    //                 'pesoBVeic' => $prod[0]['pesoB'] ?? null,
    //                 'distVeic' => $prod[0]['dist'] ?? null,
    //                 'combVeic' => $prod[0]['tpComb'] ?? null,
    //                 'nMotorVeic' => $prod[0]['nMotor'] ?? null,
    //                 'cvVeic' => $prod[0]['pot'] ?? null,
    //                 'cm3Veic' => $prod[0]['cilin'] ?? null,
    //                 'serieVeic' => $prod[0]['nSerie'] ?? null,
    //                 'tpPVeic' => $prod[0]['tpPint'] ?? null,
    //                 'corVeic' => $prod[0]['xCor'] ?? null,
    //                 'cCorVeic' => $prod[0]['cCor'] ?? null,
    //                 'cCorMontVeic' => $prod[0]['cCorDENATRAN'] ?? null,
    //                 'cMarcaVeic' => $prod[0]['cMod'] ?? null,
    //                 'condVeic' => $prod[0]['condVeic'] ?? null,
    //                 'espVeic' => $prod[0]['espVeic'] ?? null,
    //                 'vinVeic' => $prod[0]['VIN'] ?? null,
    //                 'lotVeic' => $prod[0]['lota'] ?? null,
    //                 'restriVeic' => $prod[0]['tpRest'] ?? null,
    //                 'cargaVeic' => $prod[0]['CMT'] ?? null,
    //                 'operVeic' => $prod[0]['tpOp'] ?? null,
    //                 // Deixei NULL no insert em massa por enquanto, pois geralmente vem de XML antigo
    //                 // Se precisar importar isso de XML novo, terá que mapear aqui depois.
    //             ])->id;
    //             array_push($productsList, ['produtoId' => $produtoId, 'qtde' => $prod[0]['qCom']]);
    //         }
    //     }
    //     return $productsList;
    // }

    public function insertProductsList($request, $empresaId)
    {
        $productsList = [];

        foreach ($request as $prod) {
            $item = $prod[0];

            $chassi = trim($item['chassi'] ?? '');
            $codigoEAN = trim($item['cEAN'] ?? '');
            $codigoProduto = trim($item['cProd'] ?? '');

            $codigo = (!empty($codigoEAN) && strtoupper($codigoEAN) !== 'SEM GTIN')
                ? $codigoEAN
                : $codigoProduto;

            $descricaoProduto = trim($item['xProd'] ?? '');

            if (!empty($chassi) && stripos($descricaoProduto, $chassi) === false) {
                $descricaoProduto .= ' - CHASSI: ' . $chassi;
            }

            $dadosProduto = [
                'categoria_id' => $item['categoria'],
                'codigo' => $codigo,
                'produto' => $descricaoProduto,
                'precocusto' => $item['vProd'],
                'precovenda' => $item['vVendaProd'],
                'ncm' => $item['NCM'],
                'cfop_interno' => $item['CFOP_INTERNO'] ?? null,
                'cfop_externo' => $item['CFOP_EXTERNO'] ?? null,
                'cst_csosn' => $item['CSOSN'],
                'cst_pis' => $item['CST_PIS'],
                'cst_cofins' => $item['CST_COFINS'],
                'cst' => $item['CST'],
                'icms' => $item['ICMS'],
                'pis' => $item['PIS'],
                'cofins' => $item['COFINS'],
                'ipi' => $item['IPI'],
                'un' => $item['uCom'],
                'tpProd' => $item['tpProd'],
                'tpVeic' => $item['tpVeic'] ?? null,
                'chassiVeic' => !empty($chassi) ? $chassi : null,
                'renavanVeic' => $item['renavan'] ?? null,
                'anoFabVeic' => $item['anoFab'] ?? null,
                'anoModVeic' => $item['anoMod'] ?? null,
                'pesoLVeic' => $item['pesoL'] ?? null,
                'pesoBVeic' => $item['pesoB'] ?? null,
                'distVeic' => $item['dist'] ?? null,
                'combVeic' => $item['tpComb'] ?? null,
                'nMotorVeic' => $item['nMotor'] ?? null,
                'cvVeic' => $item['pot'] ?? null,
                'cm3Veic' => $item['cilin'] ?? null,
                'serieVeic' => $item['nSerie'] ?? null,
                'tpPVeic' => $item['tpPint'] ?? null,
                'corVeic' => $item['xCor'] ?? null,
                'cCorVeic' => $item['cCor'] ?? null,
                'cCorMontVeic' => $item['cCorDENATRAN'] ?? null,
                'cMarcaVeic' => $item['cMod'] ?? null,
                'condVeic' => $item['condVeic'] ?? null,
                'espVeic' => $item['espVeic'] ?? null,
                'vinVeic' => $item['VIN'] ?? null,
                'lotVeic' => $item['lota'] ?? null,
                'restriVeic' => $item['tpRest'] ?? null,
                'cargaVeic' => $item['CMT'] ?? null,
                'operVeic' => $item['tpOp'] ?? null,
            ];

            $produtoExistenteQuery = Produto::whereEmpresaId($empresaId);

            if (!empty($chassi)) {
                $produtoExistenteQuery->where('chassiVeic', $chassi);
            } else {
                $produtoExistenteQuery->where('codigo', $codigo);
            }

            $produtoExistente = $produtoExistenteQuery->first();

            if ($produtoExistente) {
                $produtoExistente->update($dadosProduto);
                $produtoId = $produtoExistente->id;
            } else {
                $dadosProduto['empresa_id'] = $empresaId;
                $produtoId = Produto::create($dadosProduto)->id;
            }

            array_push($productsList, [
                'produtoId' => $produtoId,
                'produto_id' => $produtoId,
                'empresa_id' => $empresaId,

                // Identificação do item na NFe
                'numero_item' => $item['nItem'] ?? null,
                'codigo_fornecedor' => $item['cProd'] ?? null,
                'codigo_barras' => $item['cEAN'] ?? null,
                'codigo_barras_tributavel' => $item['cEANTrib'] ?? null,
                'descricao' => $item['xProd'] ?? null,
                'ncm' => $item['NCM'] ?? null,
                'cest' => $item['CEST'] ?? null,
                'cfop' => $item['CFOP'] ?? null,
                'unidade' => $item['uCom'] ?? null,
                'unidade_tributavel' => $item['uTrib'] ?? null,

                // Quantidades e valores
                'qtde' => $item['qCom'] ?? 0,
                'quantidade_tributavel' => $item['qTrib'] ?? null,
                'valor_unitario' => $item['vUnCom'] ?? null,
                'valor_unitario_tributavel' => $item['vUnTrib'] ?? null,
                'valor_total' => $item['vProd'] ?? null,
                'valor_desconto' => $item['vDesc'] ?? null,
                'valor_frete' => $item['vFrete'] ?? null,
                'valor_seguro' => $item['vSeg'] ?? null,
                'valor_outros' => $item['vOutro'] ?? null,

                // ICMS
                'origem_icms' => $item['orig'] ?? null,
                'cst_icms' => $item['CST'] ?? null,
                'csosn' => $item['CSOSN'] ?? null,
                'modalidade_bc_icms' => $item['modBC'] ?? null,
                'valor_bc_icms' => $item['vBC'] ?? null,
                'aliquota_icms' => $item['pICMS'] ?? $item['ICMS'] ?? null,
                'valor_icms' => $item['vICMS'] ?? null,
                'modalidade_bc_icms_st' => $item['modBCST'] ?? null,
                'valor_bc_icms_st' => $item['vBCST'] ?? null,
                'aliquota_icms_st' => $item['pICMSST'] ?? null,
                'valor_icms_st' => $item['vICMSST'] ?? null,
                'valor_icms_desonerado' => $item['vICMSDeson'] ?? null,

                // IPI
                'cst_ipi' => $item['CST_IPI'] ?? null,
                'enquadramento_ipi' => $item['cEnq'] ?? null,
                'valor_bc_ipi' => $item['vBC_IPI'] ?? null,
                'aliquota_ipi' => $item['pIPI'] ?? $item['IPI'] ?? null,
                'valor_ipi' => $item['vIPI'] ?? null,

                // PIS
                'cst_pis' => $item['CST_PIS'] ?? null,
                'valor_bc_pis' => $item['vBC_PIS'] ?? null,
                'aliquota_pis' => $item['pPIS'] ?? $item['PIS'] ?? null,
                'valor_pis' => $item['vPIS'] ?? null,

                // COFINS
                'cst_cofins' => $item['CST_COFINS'] ?? null,
                'valor_bc_cofins' => $item['vBC_COFINS'] ?? null,
                'aliquota_cofins' => $item['pCOFINS'] ?? $item['COFINS'] ?? null,
                'valor_cofins' => $item['vCOFINS'] ?? null,

                // Pedido/compra
                'numero_pedido' => $item['xPed'] ?? null,
                'item_pedido' => $item['nItemPed'] ?? null,

                // Dados extras do XML para auditoria futura
                'informacoes_adicionais' => $item['infAdProd'] ?? null,
            ]);
        }

        return $productsList;
    }

    public function store(
        $categoria,
        $empresa,
        $codigo,
        $produto,
        $precocusto,
        $precovenda,
        $ncm,
        $cfopinterno,
        $cst_csosn,
        $cst_pis,
        $cst_cofins,
        $cst,
        $icms,
        $pis,
        $cofins,
        $ipi,
        $cfopexterno,
        $un,
        $tpProd,
        $tpVeic,
        $chassiVeic,
        $renavanVeic,
        $anoFabVeic,
        $anoModVeic,
        $pesoLVeic,
        $pesoBVeic,
        $distVeic,
        $combVeic,
        $nMotorVeic,
        $cvVeic,
        $cm3Veic,
        $serieVeic,
        $tpPVeic,
        $corVeic,
        $cCorVeic,
        $cCorMontVeic,
        $cMarcaVeic,
        $condVeic,
        $espVeic,
        $vinVeic,
        $lotVeic,
        $restriVeic,
        $cargaVeic,
        $operVeic,
        // --- NOVOS CAMPOS RTC ---
        $cClassTrib = null,
        $pIBS = null,
        $pCBS = null,
        $pIS_imposto = null,
        $cst_ibs_cbs = null
    ) {

        $descricaoProduto = trim($produto);

        if (!empty($chassiVeic)) {
            $chassiVeic = trim($chassiVeic);

            if (stripos($descricaoProduto, $chassiVeic) === false) {
                $descricaoProduto .= ' - CHASSI: ' . $chassiVeic;
            }
        }


        return Produto::create([
            'categoria_id' => $categoria,
            'empresa_id' => $empresa,
            'codigo' => $codigo,
            'produto' => $descricaoProduto,
            'precocusto' => $precocusto,
            'precovenda' => $precovenda,
            'ncm' => $ncm,
            'cfop_interno' => $cfopinterno,
            'cst_csosn' => $cst_csosn,
            'cst_pis' => $cst_pis,
            'cst_cofins' => $cst_cofins,
            'cst' => $cst_csosn,
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
            'operVeic' => $operVeic,
            // --- RTC ---
            'cClassTrib' => $cClassTrib,
            'pIBS' => $pIBS,
            'pCBS' => $pCBS,
            'pIS_imposto' => $pIS_imposto,
            'cst_ibs_cbs' => $cst_ibs_cbs
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

    public function searchProdByFilter($filter, $companyId)
    {
        $filter = empty($filter) ? '' : '%' . $filter . '%';
        return Produto::where('produto', 'like', $filter)->whereEmpresaId($companyId)->get();
    }
}
