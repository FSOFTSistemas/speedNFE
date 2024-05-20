<?php

namespace App\Http\Livewire;

use App\Services\ProdutosService;
use Livewire\Component;

class NFCe extends Component
{
    public $prod = null;
    public $qtde = 1;
    public $desconto = 0;
    public $acrescimo = 0;
    public $total = 0;

    public $results = [];

    public $valorTotal = 0;

    public $produtos = [];

    public function mount(ProdutosService $produtoService)
    {
        $this->produtos = $produtoService->todos(auth()->user()->empresa_id);
    }

    public function addProd()
    {
        dd($this->produtos[0]['codigo']);
    }

    public function searchProds(ProdutosService $produtoService)
    {
        $this->results = $produtoService->searchProdByFilter($this->prod);
        $this->emit('OpenAddProdModal');
    }

    public function clearQuery()
    {
        $this->results = [];
        $this->prod = null;
    }

    public function render()
    {
        return view('livewire.n-f-ce');
    }
}
