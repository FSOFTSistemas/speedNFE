<?php

namespace App\Http\Livewire;

use App\Enums\FormaPagamentoEnum;
use App\Services\ClientesService;
use App\Services\ProdutosService;
use App\Utils\FormatationUtil;
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

    public $descontoTipo = 'real'; // 'real' ou 'percent'
    public $acrescimoTipo = 'real'; // 'real' ou 'percent'

    public $descontoTotalTipo = 'real'; // 'real' ou 'percent'
    public $acrescimoTotalTipo = 'real'; // 'real' ou 'percent'

    // NOVO: Propriedade para o feedback visual do cálculo no item
    public $descontoCalculadoItem = 0;
    public $acrescimoCalculadoItem = 0;

    // Propriedade para o valor final do desconto total (já existia na sugestão anterior)
    public $descontoTotalCalculado = 0;
    public $acrescimoTotalCalculado = 0;

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

    public $showPaymentArea = 'none';
    public $editProd = false;

    protected $listeners = ['selectProd', 'searchCustomers', 'searchProducts', 'clearItems'];

    public function mount(ClientesService $clienteService, ProdutosService $produtoService)
    {
        try {
            $this->customers = $clienteService->todos(Auth::user()->empresa_id)
                ->map(fn ($cliente) => (array) $cliente)
                ->values()
                ->all();
            $this->formas = array_map(
                fn (FormaPagamentoEnum $forma) => $forma->value,
                FormaPagamentoEnum::cases()
            );
            $this->cliente = ['id' => null, 'nome' => 'Consumidor Final'];
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function toggleDescontoTipo()
    {
        try {
            $this->descontoTipo = $this->descontoTipo === 'real' ? 'percent' : 'real';
            $this->desconto = 0; // Reset do valor ao alternar tipo
            $this->updateProductTotal();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function toggleAcrescimoTipo()
    {
        try {
            $this->acrescimoTipo = $this->acrescimoTipo === 'real' ? 'percent' : 'real';
            $this->acrescimo = 0; // Reset do valor ao alternar tipo
            $this->updateProductTotal();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function toggleDescontoTotalTipo()
    {
        try {
            $this->descontoTotalTipo = $this->descontoTotalTipo === 'real' ? 'percent' : 'real';
            $this->descontoTotal = 0; // Reset do valor ao alternar tipo
            $this->updateSaleTotal();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function toggleAcrescimoTotalTipo()
    {
        try {
            $this->acrescimoTotalTipo = $this->acrescimoTotalTipo === 'real' ? 'percent' : 'real';
            $this->acrescimoTotal = 0; // Reset do valor ao alternar tipo
            $this->updateSaleTotal();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function addProd()
{
    try {
        // CORREÇÃO: A verificação anterior era muito restritiva.
        // A única condição que realmente importa para poder adicionar um item
        // é se um produto foi selecionado, o que é garantido pela presença do 'prodId'.
        // Se temos um ID, podemos confiar que os outros dados foram preenchidos.
        if (isset($this->prodId)) {

            // Verificamos a duplicidade aqui dentro.
            if (!$this->existValueInSubArray($this->itens, $this->prodId, 'prodId')) {
                
                // A lógica para calcular e adicionar o item permanece a mesma.
                $valores = $this->getValoresCalculadosItem();

                $this->itens[] = [
                    'prodId' => $this->prodId,
                    'produto' => $this->prod,
                    'codigo' => $this->cod,
                    'qtde' => $this->qtde,
                    'unitario' => $this->unitario,
                    'desconto' => $valores['desconto'],
                    'acrescimo' => $valores['acrescimo'],
                    'total' => $this->total,
                    'subtotal' => $this->subtotalItem
                ];

                $this->updateSaleTotal();
                $this->cancelProd();
                
            } else {
                $this->emit('ProdutoJaInserido', 'Item já foi inserido anteriormente!');
            }
        }
        // Se nenhum prodId estiver setado, nada acontece, que é o comportamento correto.
        
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
            // AJUSTE: Pega os valores já calculados em R$
            $valores = $this->getValoresCalculadosItem();
            
            $this->itens[$this->prodIndexUpdating]['qtde'] = $this->qtde;
            $this->itens[$this->prodIndexUpdating]['desconto'] = $valores['desconto']; // Salva o valor em R$
            $this->itens[$this->prodIndexUpdating]['acrescimo'] = $valores['acrescimo']; // Salva o valor em R$
            $this->itens[$this->prodIndexUpdating]['total'] = $this->total;
            $this->itens[$this->prodIndexUpdating]['subtotal'] = $this->subtotalItem;

            $this->updateSaleTotal();
            $this->cancelProd();
        }  catch (\Exception $e) {
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
            $this->unitario = $this->itens[$index]['unitario'];
            $this->total = $this->itens[$index]['total'];
            $this->subtotalItem = $this->itens[$index]['subtotal'];

            // AJUSTE: Carrega o valor (que já está em R$) e força o tipo para 'real'
            $this->desconto = $this->itens[$index]['desconto'];
            $this->acrescimo = $this->itens[$index]['acrescimo'];
            $this->descontoTipo = 'real';
            $this->acrescimoTipo = 'real';

            // Chama o update para recalcular o feedback visual
            $this->updateProductTotal();

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
            // Reset dos tipos para o padrão
            $this->descontoTipo = 'real';
            $this->acrescimoTipo = 'real';
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function updateProductTotal()
    {
        try {
            if (isset($this->cod) && isset($this->qtde) && empty($this->formasSelecionadas)) {
                $this->subtotalItem = FormatationUtil::format($this->qtde * $this->unitario);

                $descontoValorUnitario = 0;
                if ($this->desconto > 0) {
                    if ($this->descontoTipo === 'percent') {
                        if ($this->desconto > 100) $this->desconto = 100;
                        $descontoValorUnitario = ($this->desconto / 100) * $this->unitario;
                    } else {
                        if ($this->desconto > $this->unitario) $this->desconto = $this->unitario;
                        $descontoValorUnitario = $this->desconto;
                    }
                }

                $acrescimoValorUnitario = 0;
                if ($this->acrescimo > 0) {
                    if ($this->acrescimoTipo === 'percent') {
                        $acrescimoValorUnitario = ($this->acrescimo / 100) * $this->unitario;
                    } else {
                        $acrescimoValorUnitario = $this->acrescimo;
                    }
                }

                // AJUSTE: Atualiza as propriedades de feedback visual
                $this->descontoCalculadoItem = $descontoValorUnitario * $this->qtde;
                $this->acrescimoCalculadoItem = $acrescimoValorUnitario * $this->qtde;

                $this->total = FormatationUtil::format($this->qtde * ($this->unitario - $descontoValorUnitario + $acrescimoValorUnitario));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    private function getValoresCalculadosItem(): array
    {
        $descontoFinal = 0;
        if ($this->desconto > 0) {
            if ($this->descontoTipo === 'percent') {
                $descontoFinal = ($this->desconto / 100) * $this->unitario;
            } else {
                $descontoFinal = $this->desconto;
            }
        }

        $acrescimoFinal = 0;
        if ($this->acrescimo > 0) {
            if ($this->acrescimoTipo === 'percent') {
                $acrescimoFinal = ($this->acrescimo / 100) * $this->unitario;
            } else {
                $acrescimoFinal = $this->acrescimo;
            }
        }
        return ['desconto' => $descontoFinal, 'acrescimo' => $acrescimoFinal];
    }

    public function updateSaleTotal()
    {
        try {
            if (empty($this->formasSelecionadas)) {
                $valorTotal = 0;
                foreach ($this->itens as $item) {
                    $valorTotal += $item['total'];
                }
                
                // Calcular desconto total baseado no tipo
                $descontoTotalValor = 0;
                if ($this->descontoTotal > 0) {
                    if ($this->descontoTotalTipo === 'percent') {
                        if ($this->descontoTotal > 100) {
                            $this->descontoTotal = 100;
                        }
                        $descontoTotalValor = ($this->descontoTotal / 100) * $valorTotal;
                    } else {
                        if ($this->descontoTotal > $valorTotal) {
                            $this->descontoTotal = $valorTotal;
                        }
                        $descontoTotalValor = $this->descontoTotal;
                    }
                }

                // Calcular acréscimo total baseado no tipo
                $acrescimoTotalValor = 0;
                if ($this->acrescimoTotal > 0) {
                    if ($this->acrescimoTotalTipo === 'percent') {
                        $acrescimoTotalValor = ($this->acrescimoTotal / 100) * $valorTotal;
                    } else {
                        $acrescimoTotalValor = $this->acrescimoTotal;
                    }
                }

                // AJUSTE CRÍTICO: Atribuir os valores calculados às propriedades corretas
                $this->descontoTotalCalculado = FormatationUtil::format($descontoTotalValor);
                $this->acrescimoTotalCalculado = FormatationUtil::format($acrescimoTotalValor);

                $this->subtotal = FormatationUtil::format($valorTotal);
                $this->valorTotal = FormatationUtil::format($valorTotal - $descontoTotalValor + $acrescimoTotalValor);
                $this->aReceber = FormatationUtil::format($this->valorTotal);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function searchProds(ProdutosService $produtoService)
    {
        try {
            $this->results = $produtoService->searchProdByFilter($this->prod, Auth::user()->empresa_id);
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
            $this->valorPago = FormatationUtil::format($valueReceived);
            $this->troco = FormatationUtil::format($this->valorPago - $this->valorTotal);
            $this->aReceber = FormatationUtil::format($this->valorTotal - $this->valorPago);
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

    public function removeClient()
    {
        try {
            $this->cliente = ['id' => null, 'nome' => 'Consumidor Final'];
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function searchProducts()
    {
        try {
            $this->prod = '%';
            $this->searchProds(new ProdutosService());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    public function clearItems()
    {
        try {
            if (!empty($this->formasSelecionadas)) {
                return;
            }
            $this->itens = [];
            $this->cancelProd();
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
            $this->aReceber = $this->valorTotal;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro interno, tente novamente em outro momento, Erro: ' . $e->getMessage());
        }
    }

    private function existValueInSubArray($array, $value, $field)
    {
        try {
            foreach ($array as $subArray) {
                if ($value == $subArray[$field]) {
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
                $value = $this->aReceber;
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
            if (empty($this->itens) || $this->editProd) {
                return;
            }
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
