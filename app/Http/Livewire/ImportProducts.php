<?php

namespace App\Http\Livewire;

use App\Services\CategoriasService;
use Livewire\Component;

class ImportProducts extends Component
{

    public $ide = [];
    public $emit = [];
    public $vNF = 0;
    public $chNFe = '';
    public $xml = '';
    public $prods = [];
    public $itemProd = [];
    public $categorias = [];

    public function mount($data)
    {
        $categoriaService = new CategoriasService();
        $this->ide = (array) $data['nota']['ide'];
        $this->emit = (array) $data['nota']['emit'];
        // >>> AQUI <<<
        if (!isset($this->emit['CNPJ']) && isset($this->emit['CPF'])) {
            $this->emit['CNPJ'] = $this->emit['CPF'];
        }
        $this->vNF = (string) $data['nota']['vNF'][0];
        $this->chNFe = (string) $data['nota']['chNFe'][0];
        $this->xml = $data['xml'] ?? '';
        $this->categorias = $categoriaService->todasCategoriasEmpresa(auth()->user()->empresa_id);
        $this->createProductsList($data['prods']);
    }

    protected function createProductsList($prods)
    {
        foreach ($prods as $prod) {
            $this->createProductItem($prod);
            $this->prods[] = $this->itemProd;
            $this->itemProd = [];
        }
    }

    protected function createProductItem($item)
    {
        $sscfop = substr($item->prod->CFOP[0], 0, 2);
        $cfopInterno = '5102';
        $cfopExterno = '6102';
        if ($sscfop == '51' || $sscfop == '61') {
            $cst = '000';
            $csosn = '102';
        } else if ($sscfop == '54' || $sscfop == '64') {
            $cfopInterno = '5405';
            $cfopExterno = '6405';
            $cst = '060';
            $csosn = '500';
        } else {
            $cst = '401';
            $csosn = '400';
        }
        if (isset($item->prod->veicProd)) {
            $st = isset($item->imposto->ICMS->ICMSSN102->vICMSST[0]) ? number_format((double) $item->imposto->ICMS->ICMSSN102->vICMSST[0], 2) : 0;
            $this->itemProd[] = ['cProd' => (string) $item->prod->cProd[0], 'xProd' => (string) $item->prod->xProd[0],
                'cEAN' => (string) $item->prod->cEAN[0], 'NCM' => (string) $item->prod->NCM[0], 'CFOP' => (string) $item->prod->CFOP[0],
                'CFOP_INTERNO' => $cfopInterno, 'CFOP_EXTERNO' => $cfopExterno,
                'uCom' => (string) $item->prod->uCom[0], 'qCom' => (int) $item->prod->qCom[0], 'vVendaProd' => (double) $item->prod->vUnCom[0] + $st,
                'vProd' => (double) $item->prod->vUnCom[0], 'ICMS' => isset($item->imposto->ICMS->ICMSSN102->pICMS) ? (double) $item->imposto->ICMS->ICMSSN102->pICMS : 0,
                'IPI' => '00', 'PIS' => '00', 'COFINS' => '00', 'margem' => 0, 'tpProd' => 1, 'CST' => $cst, 'CSOSN' =>  $csosn, 'ST' => $st,
                'CST_PIS' => isset($item->imposto->PIS->PISOutr->CST) ? (string) $item->imposto->PIS->PISOutr->CST : '99',
                'CST_COFINS' => isset($item->imposto->COFINS->COFINSOutr->CST) ? (string) $item->imposto->COFINS->COFINSOutr->CST : '99',
                'tpOp' => (int) $item->prod->veicProd->tpOp[0], 'chassi' => (string) $item->prod->veicProd->chassi[0],
                'cCor' => (int) $item->prod->veicProd->cCor[0], 'xCor' => (string) $item->prod->veicProd->xCor[0],
                'pot' => (double) $item->prod->veicProd->pot[0], 'cilin' => (string) $item->prod->veicProd->cilin[0],
                'pesoL' => (double) $item->prod->veicProd->pesoL[0], 'pesoB' => (double) $item->prod->veicProd->pesoB[0],
                'nSerie' => (string) $item->prod->veicProd->nSerie[0], 'tpComb' => (int) $item->prod->veicProd->tpComb[0],
                'nMotor' => (string) $item->prod->veicProd->nMotor[0], 'CMT' => (double) $item->prod->veicProd->CMT[0],
                'dist' => (double) $item->prod->veicProd->dist[0], 'anoMod' => (int) $item->prod->veicProd->anoMod[0],
                'anoFab' => (int) $item->prod->veicProd->anoFab[0], 'tpPint' => (string) $item->prod->veicProd->tpPint[0],
                'espVeic' => (string) $item->prod->veicProd->espVeic[0], 'VIN' => (string) $item->prod->veicProd->VIN[0],
                'condVeic' => (string) $item->prod->veicProd->condVeic[0], 'cMod' => (string) $item->prod->veicProd->cMod[0],
                'cCorDENATRAN' => (string) $item->prod->veicProd->cCorDENATRAN, 'lota' => (string) $item->prod->veicProd->lota[0],
                'tpRest' => (string) $item->prod->veicProd->tpRest[0], 'tpVeic' => (string) $item->prod->veicProd->tpVeic[0]
            ];
        } else {
            $st = isset($item->imposto->ICMS->ICMS10->vICMSST[0]) ? number_format((double) $item->imposto->ICMS->ICMS10->vICMSST[0], 2) : 0;
            $this->itemProd[] = ['cProd' => (string) $item->prod->cProd[0], 'xProd' => (string) $item->prod->xProd[0],
                'cEAN' => (string) $item->prod->cEAN[0], 'NCM' => (string) $item->prod->NCM[0], 'CFOP' => (string) $item->prod->CFOP[0],
                'CFOP_INTERNO' => $cfopInterno, 'CFOP_EXTERNO' => $cfopExterno,
                'uCom' => (string) $item->prod->uCom[0], 'qCom' => (int) $item->prod->qCom[0], 'vVendaProd' => (double) $item->prod->vUnCom[0] + $st,
                'vProd' => (double) $item->prod->vUnCom[0], 'ICMS' => isset($item->imposto->ICMS->ICMS10->pICMS) ? (double) $item->imposto->ICMS->ICMS10->pICMS : 0,
                'IPI' => '00', 'PIS' => '00', 'COFINS' => '00', 'margem' => 0, 'tpProd' => 0, 'CST' => $cst, 'CSOSN' => $csosn, 'ST' => $st,
                'CST_PIS' => isset($item->imposto->PIS->PISNT->CST) ? (string) $item->imposto->PIS->PISNT->CST : '99',
                'CST_COFINS' => isset($item->imposto->COFINS->COFINSNT->CST) ? (string) $item->imposto->COFINS->COFINSNT->CST : '99'
            ];
        }
    }

    public function calcValorVenda($index)
    {
        if ($this->prods[$index][0]['margem'] != null) {
            $this->prods[$index][0]['vVendaProd'] = number_format(($this->prods[$index][0]['margem'] / 100 + 1) * ($this->prods[$index][0]['vProd'] + $this->prods[$index][0]['ST']), 2, '.');
        }
    }

    public function render()
    {
        return view('livewire.import-products');
    }
}
