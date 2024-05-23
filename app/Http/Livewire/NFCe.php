<?php

namespace App\Http\Livewire;

use App\Enums\FormaPagamentoEnum;
use App\Services\ProdutosService;
use Livewire\Component;

class NFCe extends Component
{
    public $prod = null;
    public $cod = null;
    public $unitario = 0;

    public $qtde = 1;
    public $desconto = 0;
    public $acrescimo = 0;
    public $total = 0;

    public $results = [];

    public $forma = null;
    public $valorTotal = 0;
    public $itens = [];

    public $formas = [];

    protected $listeners = ['selectProd'];

    public function mount() {
        $this->formas = FormaPagamentoEnum::cases();
    }

    public function addProd()
    {
        if (isset($this->prod) && isset($this->cod) && isset($this->qtde) && isset($this->unitario) && isset($this->desconto) && isset($this->acrescimo) && isset($this->total)) {
            $this->itens[] = ['produto' => $this->prod, 'codigo' => $this->cod, 'qtde' => $this->qtde, 'unitario' => $this->unitario, 'desconto' => $this->desconto, 'acrescimo' => $this->acrescimo, 'total' => $this->total];
            $this->updateSaleTotal();
            $this->cancelProd();
        }
    }

    public function cancelProd()
    {
        $this->prod = null;
        $this->cod = null;
        $this->unitario = 0;
        $this->qtde = 1;
        $this->desconto = 0;
        $this->acrescimo = 0;
        $this->total = 0;
    }

    public function updateProductTotal()
    {
        if (isset($this->cod) && isset($this->qtde) && isset($this->desconto) && isset($this->acrescimo)) {
            $this->total = $this->qtde * ($this->unitario - $this->desconto + $this->acrescimo);
        }
    }

    public function updateSaleTotal()
    {
        $valorTotal = 0;
        foreach ($this->itens as $item) {
            $valorTotal += $item['total'];
        }
        $this->valorTotal = $valorTotal;
    }

    public function searchProds(ProdutosService $produtoService)
    {
        $this->results = $produtoService->searchProdByFilter($this->prod);
        $this->emit('OpenAddProdModal', $this->results);
    }

    public function clearQuery()
    {
        $this->results = [];
        $this->prod = null;
    }

    public function selectProd($prod, $codigo, $unitario)
    {
        $this->prod = $prod;
        $this->unitario = $unitario;
        $this->total = $this->qtde * $this->unitario;
        $this->cod = $codigo;
        $this->emit('CloseAddProdModal');
    }

    public function addPaymentMethod($index)
    {
        $this->forma = $this->formas[$index];
    }

    public function selectPaymentMethod()
    {
        $this->emit('OpenSelectPaymentMethodModal');
    }

    public function render()
    {
        return view('livewire.n-f-ce');
    }
}
