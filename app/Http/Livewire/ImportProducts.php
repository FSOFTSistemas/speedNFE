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
    public $prods = [];
    public $itemProd = [];

    public function mount($data)
    {
        $categoriaService = new CategoriasService();
        $this->ide = (array) $data['nota']['ide'];
        $this->emit = (array) $data['nota']['emit'];
        $this->vNF = (string) $data['nota']['vNF'][0];
        $this->chNFe = (string) $data['nota']['chNFe'][0];
        $categorias = $categoriaService->todasCategoriasEmpresa(auth()->user()->empresa_id);
        $this->emit('setSelectData', $categorias);
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
        if (isset($item->prod->veicProd)) {
            $this->itemProd[] = ['cProd' => (string) $item->prod->cProd[0], 'xProd' => (string) $item->prod->xProd[0],
                'cEAN' => (string) $item->prod->cEAN[0], 'NCM' => (string) $item->prod->NCM[0], 'CFOP' => (string) $item->prod->CFOP[0],
                'uCom' => (string) $item->prod->uCom[0], 'qCom' => (int) $item->prod->qCom[0], 'vVendaProd' => (double) $item->prod->vUnCom[0],
                'vProd' => (double) $item->prod->vUnCom[0], 'ICMS' => (double) $item->imposto->ICMS->ICMSSN102->CSON,
                'IPI' => (string) $item->imposto->IPI->IPINT->CST, 'PIS' => (string) $item->imposto->PIS->PISOutr->vPIS,
                'COFINS' => (string) $item->imposto->COFINS->COFINSOutr->vCOFINS, 'margem' => 0, 'tpProd' => 1,
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
                'tpRest' => (string) $item->prod->veicProd->tpRest[0]
            ];
        } else {
            $this->itemProd[] = ['cProd' => (string) $item->prod->cProd[0], 'xProd' => (string) $item->prod->xProd[0],
                'cEAN' => (string) $item->prod->cEAN[0], 'NCM' => (string) $item->prod->NCM[0], 'CFOP' => (string) $item->prod->CFOP[0],
                'uCom' => (string) $item->prod->uCom[0], 'qCom' => (int) $item->prod->qCom[0], 'vVendaProd' => (double) $item->prod->vUnCom[0],
                'vProd' => (double) $item->prod->vUnCom[0], 'ICMS' => (double) $item->imposto->ICMS->ICMS10->pICMS,
                'IPI' => (string) $item->imposto->IPI->IPITrib->pIPI, 'PIS' => (string) $item->imposto->PIS->PISNT->CST,
                'COFINS' => (string) $item->imposto->COFINS->COFINSNT->CST, 'margem' => 0, 'tpProd' => 0,
            ];
        }
    }

    public function calcValorVenda($index)
    {
        if ($this->prods[$index][0]['margem']) {
            $this->prods[$index][0]['vVendaProd'] = number_format(($this->prods[$index][0]['margem'] / 100 + 1) * $this->prods[$index][0]['vProd'], 2, '.');
        }
    }

    public function render()
    {
        return view('livewire.import-products');
    }
}
