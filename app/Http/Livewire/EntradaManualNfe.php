<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Entrada;
use App\Models\Produto;
use App\Models\ItensEntrada;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class EntradaManualNfe extends Component
{
    public $dataEmissao;
    public $dataEntrada;
    public $numeroNota;
    public $fornecedor;
    public $chave;
    public $valor;
    public $empresa_id;

    public $produtos = [];
    public $produto_id;
    public $qtde;

    public $itens = [];

    public function mount()
    {
        $this->produtos = Produto::all();
        $this->dataEmissao = now()->toDateString();
        $this->dataEntrada = now()->toDateString();
        $this->empresa_id = Auth::user()->empresa_id;
    }

    public function adicionarProduto()
    {
        if ($this->produto_id && $this->qtde > 0) {
            $produto = Produto::find($this->produto_id);

            $this->itens[] = [
                'produto_id' => $produto->id,
                'produto_nome' => $produto->produto,
                'qtde' => $this->qtde
            ];

            $this->produto_id = null;
            $this->qtde = null;
        }
    }

    public function removerItem($index)
    {
        unset($this->itens[$index]);
        $this->itens = array_values($this->itens); // Reindexar
    }

    public function salvar()
    {
        try {
            $this->validate([
                'dataEmissao' => 'required|date',
                'valor' => 'required|numeric',
            ]);

            $entrada = Entrada::create([
                'dataEmissao' => $this->dataEmissao,
                'dataEntrada' => $this->dataEntrada,
                'numeroNota' => $this->numeroNota,
                'fornecedor' => $this->fornecedor,
                'chave' => $this->chave,
                'valor' => $this->valor,
                'empresa_id' => $this->empresa_id,
            ]);

            if ($this->itens) {
                foreach ($this->itens as $item) {
                    ItensEntrada::create([
                        'entrada_id' => $entrada->id,
                        'produto_id' => $item['produto_id'],
                        'qtde' => $item['qtde'],
                        'empresa_id' => $this->empresa_id,
                    ]);

                    // Atualiza o estoque após criar o ItensEntrada
                    $estoque = \App\Models\Estoque::firstOrNew([
                        'produto_id' => $item['produto_id'],
                        'empresa_id' => $this->empresa_id,
                    ]);

                    $estoque->estoque_anterior = $estoque->estoque_atual ?? 0;
                    $estoque->estoque_atual = $estoque->estoque_anterior + $item['qtde'];
                    $estoque->entradas = ($estoque->entradas ?? 0) + $item['qtde'];
                    $estoque->save();
                }
            }

            session()->flash('success', 'Entrada salva com sucesso!');
            return redirect()->route('entradas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar entrada: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.entrada-manual-nfe');
    }
}
