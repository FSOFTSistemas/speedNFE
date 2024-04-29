<?php

namespace App\Http\Livewire;

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
        $this->ide = (array) $data['nota']['ide'];
        $this->emit = (array) $data['nota']['emit'];
        $this->vNF = (string) $data['nota']['vNF'][0];
        $this->chNFe = (string) $data['nota']['chNFe'][0];
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
        $this->itemProd[] = ['xProd' => $item['xProd'], 'cEAN' => $item['cEAN']];
    }

    public function render()
    {
        return view('livewire.import-products');
    }
}
