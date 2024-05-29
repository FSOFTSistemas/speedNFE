<?php

namespace App\Http\Livewire;

use App\Enums\FormaPagamentoEnum;
use App\Services\ClientesService;
use App\Services\ProdutosService;
use Illuminate\Support\Facades\Auth;
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
    public $selectedForma = null;
    public $prodIndexUpdating = null;

    public $formasSelecionadas = [];
    public $valorTotal = 0;
    public $descontoTotal = 0;
    public $acrescimoTotal = 0;
    public $subtotal = 0;
    public $valorPago = 0;
    public $valorRecebimento = 0;
    public $troco = 0;
    public $aReceber = 0;
    public $cliente = null;
    public $itens = [];

    public $formas = [];
    public $customers = [];
    public $products = [];

    public $showPaymentArea = 'none';
    public $editProd = false;

    protected $listeners = ['selectProd', 'searchCustomers', 'searchProducts', 'clearItems'];

    public function mount(ClientesService $clienteService, ProdutosService $produtoService)
    {
        $this->customers = $clienteService->todos(Auth::user()->empresa_id);
        $this->products = $produtoService->todos(Auth::user()->empresa_id);
        $this->formas = FormaPagamentoEnum::cases();
    }

    public function addProd()
    {
        if (isset($this->prod) && isset($this->cod) && isset($this->qtde) && isset($this->unitario) && isset($this->desconto) && isset($this->acrescimo) && isset($this->total)) {
            if (!$this->existValueInSubArray($this->itens, $this->prod)) {
                $this->itens[] = ['produto' => $this->prod, 'codigo' => $this->cod, 'qtde' => $this->qtde, 'unitario' => $this->unitario, 'desconto' => $this->desconto, 'acrescimo' => $this->acrescimo, 'total' => $this->total];
                $this->updateSaleTotal();
            }
            $this->cancelProd();
        }
    }

    public function removeItem($index)
    {
        unset($this->itens[$index]);
        $this->itens = array_values($this->itens);
        $this->updateSaleTotal();
    }

    public function updateProd()
    {
        $this->itens[$this->prodIndexUpdating]['qtde'] = $this->qtde;
        $this->itens[$this->prodIndexUpdating]['desconto'] = $this->desconto;
        $this->itens[$this->prodIndexUpdating]['acrescimo'] = $this->acrescimo;
        $this->itens[$this->prodIndexUpdating]['total'] = $this->total;
        $this->updateSaleTotal();
        $this->cancelProd();
    }

    public function editItem($index)
    {
        $this->editProd = true;
        $this->prodIndexUpdating = $index;
        $this->prod = $this->itens[$index]['produto'];
        $this->cod = $this->itens[$index]['codigo'];
        $this->qtde = $this->itens[$index]['qtde'];
        $this->acrescimo = $this->itens[$index]['acrescimo'];
        $this->desconto = $this->itens[$index]['desconto'];
        $this->unitario = $this->itens[$index]['unitario'];
        $this->total = $this->itens[$index]['total'];
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
        $this->editProd = false;
    }

    public function updateProductTotal()
    {
        if (isset($this->cod) && isset($this->qtde) && isset($this->desconto) && isset($this->acrescimo) && empty($this->formasSelecionadas)) {
            $this->total = $this->qtde * ($this->unitario - $this->desconto + $this->acrescimo);
        }
    }

    public function updateSaleTotal()
    {
        if (empty($this->formasSelecionadas)) {
            $valorTotal = 0;
            foreach ($this->itens as $item) {
                $valorTotal += $item['total'];
            }
            $this->valorTotal = $valorTotal - $this->descontoTotal + $this->acrescimoTotal;
            $this->aReceber = $this->valorTotal;
            return $this->subtotal = $valorTotal;
        }
        $this->descontoTotal = 0;
        $this->acrescimoTotal = 0;
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

    public function selectClient($clientCode, $name)
    {
        if (isset($clientCode) && isset($name)) {
            $this->cliente = ['codigo' => $clientCode, 'nome' => $name];
        }
    }

    public function selectProd($prod, $codigo, $unitario)
    {
        $this->prod = $prod;
        $this->unitario = $unitario;
        $this->total = $this->qtde * $this->unitario;
        $this->cod = $codigo;
        $this->emit('CloseAddProdModal');
    }

    public function addPaymentMethod($method)
    {
        $this->selectedForma = $method;
        $this->valorRecebimento = $this->getValueReceivedByMethod($method);
        $this->emit('OpenPaymentModal');
    }

    public function updateValueReceived()
    {
        $valueReceived = $this->calculateNewValueReceived();
        if ($this->valorPago >= $this->valorTotal && $valueReceived >= $this->valorTotal) {
            return $this->emit('ErrorInPayment', 'O valor já foi totalmente liquidado!');
        } elseif ($this->selectedForma != 'DINHEIRO' && ($valueReceived > $this->valorTotal)) {
            return $this->emit('ErrorInPayment', 'Não é possível colocar valor acima do valor total com esse método ('.$this->selectedForma.')');
        }
        $this->formasSelecionadas[$this->selectedForma] = $this->valorRecebimento;
        $this->valorPago = $valueReceived;
        $this->troco = $this->valorPago - $this->valorTotal;
        $this->aReceber = $this->valorTotal - $this->valorPago;
        $this->valorRecebimento = 0;
        $this->emit('ClosePaymentModal');
    }

    public function searchCustomers()
    {
        $this->emit('OpenCustomersModal');
    }

    public function searchProducts()
    {
        $this->emit('OpenProductsModal');
    }

    public function clearItems()
    {
        $this->itens = [];
        $this->showPaymentArea = 'none';
        $this->updateSaleTotal();
    }

    public function clearMethods()
    {
        $this->formasSelecionadas = [];
        $this->valorPago = 0;
        $this->troco = 0;
        $this->showPaymentArea = 'none';
    }

    private function existValueInSubArray($array, $value)
    {
        foreach ($array as $subArray) {
            if (in_array($value, $subArray)) {
                return true;
            }
        }
        return false;
    }

    private function getValueReceivedByMethod($method)
    {
        if (isset($this->formasSelecionadas[$method])) {
            $value = $this->formasSelecionadas[$method];
        } else {
            $value = 0;
        }
        return $value;
    }

    private function calculateNewValueReceived()
    {
        $value = $this->valorRecebimento;
        foreach ($this->formasSelecionadas as $index => $forma) {
            if ($index != $this->selectedForma) {
                $value += $forma;
            }
        }
        return $value;
    }

    public function showPaymentArea()
    {
        $this->showPaymentArea = 'block';
        $this->emit('ShowPaymentArea');
    }

    public function render()
    {
        return view('livewire.n-f-ce');
    }
}
