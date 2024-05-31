<?php

namespace App\Http\Livewire;

use App\Enums\FormaPagamentoEnum;
use App\Services\ClientesService;
use App\Services\ProdutosService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NFCe extends Component
{
    public $prodId = null;
    public $prod = null;
    public $cod = null;
    public $unitario = 0;

    public $qtde = 1;
    public $desconto = 0;
    public $acrescimo = 0;
    public $total = 0;
    public $subtotalItem = 0;

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
        try {
            $this->customers = $clienteService->todos(Auth::user()->empresa_id);
            $this->products = $produtoService->todos(Auth::user()->empresa_id);
            $this->formas = FormaPagamentoEnum::cases();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function addProd()
    {
        try {
            if (isset($this->prodId) && isset($this->prod) && isset($this->cod) && isset($this->qtde) && isset($this->unitario) && isset($this->desconto) && isset($this->acrescimo) && isset($this->total) && isset($this->subtotalItem)) {
                if (!$this->existValueInSubArray($this->itens, $this->prod)) {
                    $this->itens[] = ['prodId' => $this->prodId, 'produto' => $this->prod, 'codigo' => $this->cod, 'qtde' => $this->qtde, 'unitario' => $this->unitario, 'desconto' => $this->desconto, 'acrescimo' => $this->acrescimo, 'total' => $this->total, 'subtotal' => $this->subtotalItem];
                    $this->updateSaleTotal();
                } else {
                    $this->emit('ProdutoJaInserido', 'Item já foi inserido anteriormente!');
                }
                $this->cancelProd();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function removeItem($index)
    {
        try {
            unset($this->itens[$index]);
            $this->itens = array_values($this->itens);
            $this->updateSaleTotal();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function updateProd()
    {
        try {
            $this->itens[$this->prodIndexUpdating]['qtde'] = $this->qtde;
            $this->itens[$this->prodIndexUpdating]['desconto'] = $this->desconto;
            $this->itens[$this->prodIndexUpdating]['acrescimo'] = $this->acrescimo;
            $this->itens[$this->prodIndexUpdating]['total'] = $this->total;
            $this->itens[$this->prodIndexUpdating]['subtotal'] = $this->subtotalItem;
            $this->updateSaleTotal();
            $this->cancelProd();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function editItem($index)
    {
        try {
            $this->editProd = true;
            $this->prodIndexUpdating = $index;
            $this->prod = $this->itens[$index]['produto'];
            $this->cod = $this->itens[$index]['codigo'];
            $this->qtde = $this->itens[$index]['qtde'];
            $this->acrescimo = $this->itens[$index]['acrescimo'];
            $this->desconto = $this->itens[$index]['desconto'];
            $this->unitario = $this->itens[$index]['unitario'];
            $this->total = $this->itens[$index]['total'];
            $this->subtotalItem = $this->itens[$index]['subtotal'];
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function cancelProd()
    {
        try {
            $this->prodId = null;
            $this->prod = null;
            $this->cod = null;
            $this->unitario = 0;
            $this->qtde = 1;
            $this->desconto = 0;
            $this->acrescimo = 0;
            $this->total = 0;
            $this->subtotalItem = 0;
            $this->editProd = false;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function updateProductTotal()
    {
        try {
            if (isset($this->cod) && isset($this->qtde) && isset($this->desconto) && isset($this->acrescimo) && isset($this->total) && isset($this->subtotalItem) && empty($this->formasSelecionadas)) {
                $this->subtotalItem = $this->qtde * $this->unitario;
                $this->total = $this->qtde * ($this->unitario - $this->desconto + $this->acrescimo);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function updateSaleTotal()
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function searchProds(ProdutosService $produtoService)
    {
        try {
            $this->results = $produtoService->searchProdByFilter($this->prod);
            $this->emit('OpenAddProdModal', $this->results);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function clearQuery()
    {
        try {
            $this->results = [];
            $this->prod = null;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function selectClient($clientId, $name)
    {
        try {
            if (isset($clientId) && isset($name)) {
                $this->cliente = ['id' => $clientId, 'nome' => $name];
                $this->emit('CloseCustomersModal');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function selectProd($prodId, $prod, $codigo, $unitario)
    {
        try {
            $this->prodId = $prodId;
            $this->prod = $prod;
            $this->unitario = $unitario;
            $this->subtotalItem = $this->qtde * $this->unitario;
            $this->total = $this->subtotalItem;
            $this->cod = $codigo;
            $this->emit('CloseAddProdModal');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function addPaymentMethod($method)
    {
        try {
            $this->selectedForma = $method;
            $this->valorRecebimento = $this->getValueReceivedByMethod($method);
            $this->emit('OpenPaymentModal');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function updateValueReceived()
    {
        try {
            $valueReceived = $this->calculateNewValueReceived();
            if ($this->valorPago >= $this->valorTotal && $valueReceived >= $this->valorTotal) {
                return $this->emit('ErrorInPayment', 'O valor já foi totalmente liquidado!');
            } elseif ($this->selectedForma != 'DINHEIRO' && ($valueReceived > $this->valorTotal)) {
                return $this->emit('ErrorInPayment', 'Não é possível colocar valor acima do valor total com esse método (' . $this->selectedForma . ')');
            }
            $this->formasSelecionadas[$this->selectedForma] = $this->valorRecebimento;
            $this->valorPago = $valueReceived;
            $this->troco = $this->valorPago - $this->valorTotal;
            $this->aReceber = $this->valorTotal - $this->valorPago;
            $this->valorRecebimento = 0;
            $this->emit('ClosePaymentModal');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function searchCustomers()
    {
        try {
            $this->emit('OpenCustomersModal');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function searchProducts()
    {
        try {
            $this->emit('OpenProductsModal');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function clearItems()
    {
        try {
            $this->itens = [];
            $this->showPaymentArea = 'none';
            $this->updateSaleTotal();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function clearMethods()
    {
        try {
            $this->formasSelecionadas = [];
            $this->valorPago = 0;
            $this->troco = 0;
            $this->showPaymentArea = 'none';
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    private function existValueInSubArray($array, $value)
    {
        try {
            foreach ($array as $subArray) {
                if (in_array($value, $subArray)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    private function getValueReceivedByMethod($method)
    {
        try {
            if (isset($this->formasSelecionadas[$method])) {
                $value = $this->formasSelecionadas[$method];
            } else {
                $value = 0;
            }
            return $value;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    private function calculateNewValueReceived()
    {
        try {
            $value = $this->valorRecebimento;
            foreach ($this->formasSelecionadas as $index => $forma) {
                if ($index != $this->selectedForma) {
                    $value += $forma;
                }
            }
            return $value;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function showPaymentArea()
    {
        try {
            $this->showPaymentArea = 'block';
            $this->emit('ShowPaymentArea');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.n-f-ce');
    }
}
