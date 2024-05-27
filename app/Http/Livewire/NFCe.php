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

    public $formaAtual = null;
    public $results = [];

    public $formasSelecionadas = [];
    public $valorTotal = 0;
    public $descontoTotal = 0;
    public $acrescimoTotal = 0;
    public $subtotal = 0;
    public $valorPago = 0;
    public $valorRecebimento = 0;
    public $troco = 0;
    public $itens = [];

    public $formas = [];

    public $showPaymentArea = 'none';

    protected $listeners = ['selectProd'];

    public function mount()
    {
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
        if (empty($this->formasSelecionadas)) {
            $valorTotal = 0;
            foreach ($this->itens as $item) {
                $valorTotal += $item['total'];
            }
            $this->valorTotal = $valorTotal - $this->descontoTotal + $this->acrescimoTotal;
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
        $this->formaAtual = $index;
        if ($this->getReceiptValueByMethod()) {
            $this->valorRecebimento = $this->getReceiptValueByMethod();
        } else {
            $this->valorRecebimento = 0;
        }
        $this->emit('OpenPaymentModal');
    }

    public function updateAmountPaid()
    {
        $valorPagoAtual = $this->calculateAmountPaid();
        if ($valorPagoAtual >= $this->valorTotal) {
            return false;
        } else if ($this->formaAtual != 0 && (($valorPagoAtual + $this->valorRecebimento) - $this->valorTotal) > 0) {
            return false;
        }
        $this->formasSelecionadas[$this->formas[$this->formaAtual]] = $this->valorRecebimento;
        $this->valorPago = $valorPagoAtual;
        $this->troco = $this->valorPago - $this->valorTotal;
        $this->valorRecebimento = 0;
        $this->emit('ClosePaymentModal');
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

    private function getReceiptValueByMethod()
    {
        if (isset($this->formasSelecionadas[$this->formas[$this->formaAtual]]))
            return $this->formasSelecionadas[$this->formas[$this->formaAtual]];
        return false;
    }

    private function calculateAmountPaid()
    {
        $valorPago = 0;
        foreach ($this->formasSelecionadas as $forma) {
            $valorPago += $forma;
        }
        return $valorPago;
    }

    public function showPaymentArea()
    {
        $this->showPaymentArea = 'block';
    }

    public function render()
    {
        return view('livewire.n-f-ce');
    }
}
