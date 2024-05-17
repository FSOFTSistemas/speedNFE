<?php

namespace App\Http\Livewire;

use App\Services\ProdutosService;
use Livewire\Component;

class NFCe extends Component
{
    public $codProd = null;
    public $qtde = 1;
    public $total = 0;
    public $valorTotal = 0;

    public $produtos = [];

    public function mount(ProdutosService $produtosService)
    {
        $this->produtos = $produtosService->todos(auth()->user()->empresa_id);
    }

    public function adicionarProd()
    {
        dd($this->produtos[0]['codigo'] == $this->codProd);
    }

    public function render()
    {
        return view('livewire.n-f-ce');
    }
}
