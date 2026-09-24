<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\EditaItensVenda;
use App\Models\Cliente;
use App\Models\FormaPag;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\EmpresasService;
use App\Services\PedidosService;
use App\Services\UsersService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditPedido extends Component
{
    use EditaItensVenda;

    public $pedido;
    public $barras = '';
    public $empresa = '';
    public $produto = '';
    public $cliente = '';
    public $info_complementares = '';
    public $pag = '';
    public $cfop = '';
    public $bcfop = '';

    public $desconto = 0;
    public $subtotal = 0;
    public $preco = 0;
    public $quantidade = 1;
    public $total = 0;

    public $empresas = [];
    public $clientes = [];
    public $vendaItens = [];
    public $produtos = [];
    public $formas = [];
    public $cfops = [];
    public $referenciaItemHabilitada = false;

    public function mount()
    {
        $this->referenciaItemHabilitada = PedidosService::referenciaItemDevolucaoHabilitada();

        try {
            //declaração dos services para recuperar dados
            $sUsers = new UsersService();
            $sEmpresas = new EmpresasService();
            // $sClientes = new ClientesService();
            // $sProdutos = new ProdutosService();
            // $sFormas = new FormaPagService();
            $sPedidos = new PedidosService();

            //Recuperando empresa_id do usuario logado
            $user = $sUsers->getEmpresa(Auth::id());
            // $this->empresa = $user->empresa_id;
            $pedido = Pedido::find($this->pedido->id);
            $itens = ItemPedido::all()->where('pedido_id', '=', $pedido->id);

            $this->cfop = $pedido->cfop;
            $this->bcfop = $sPedidos->findCfop($pedido->cfop)->cfop;
            $this->empresa = $pedido->empresa_id;
            $this->cliente = $pedido->cliente_id;
            $this->info_complementares = $pedido->info_complementares;
            $this->pag = $pedido->forma_pag_id;
            $this->empresas = $sEmpresas->todos($user->empresa_id);
            $this->clientes = Cliente::all()->where('empresa_id', '=', $this->empresa);
            $this->produtos = Produto::all()->where('empresa_id', '=', $this->empresa);
            $this->cfops = $sPedidos->cfopAll();
            $this->formas = FormaPag::all();
            $this->desconto = $pedido->desconto;
            $this->subtotal = $pedido->subtotal;

            foreach ($itens as $item) {
                $produto = Produto::find($item->produto_id);
                $this->vendaItens[] = [
                    'produto_id' => $produto->id,
                    'descricao' => $produto->produto,
                    'quantidade' => $item->qtde,
                    'unitario' => $item->unitario,
                    'desconto' => $item->desconto,
                    'total' => ($item->unitario * $item->qtde) - $item->desconto + $item->acrescimo,
                    'dfe_referenciado_chave' => $item->dfe_referenciado_chave ?: $pedido->ref_nfe,
                    'dfe_referenciado_n_item' => $item->dfe_referenciado_n_item ?: '',
                ];
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function atualizarBCfop()
    {
        try {
            $sPedidos = new PedidosService();
            $this->bcfop = $sPedidos->findCfop($this->cfop)->cfop;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function buscaCfop()
    {
        try {
            $prod = DB::table('cfops')
                ->select('*')
                ->where('cfop', 'like', $this->bcfop . "%")
                ->first();
            if ($prod) {
                $this->cfop = $prod->id;
            } else {
                $this->cfop = '';
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function atualizarTot()
    {
        try {
            $total = $this->quantidade * $this->preco;
            $desconto = $total * $this->desconto / 100;
            $this->total = $total - $desconto;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }

    }

    public function salvarProd()
    {
        try {
            if ($this->produto) {
                if ($this->containsProd($this->vendaItens, $this->produto) == -1) {
                    $prod = Produto::find($this->produto);
                    $total = $this->quantidade * $this->preco;
                    $desconto = $total * $this->desconto / 100;
                    $this->vendaItens[] = [
                        'produto_id' => $prod->id,
                        'descricao' => $prod->produto,
                        'quantidade' => $this->quantidade,
                        'unitario' => $this->preco,
                        'desconto' => $desconto,
                        'total' => $total - $desconto,
                        'dfe_referenciado_chave' => '',
                        'dfe_referenciado_n_item' => '',
                    ];
                    $subtotal = 0;
                    foreach ($this->vendaItens as $item) {
                        $subtotal = $subtotal + $item['total'];
                    }
                    $this->subtotal = $subtotal;
                }
                $this->limparProdutos();
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function limparProdutos()
    {
        try {
            $this->produto = '';
            $this->desconto = 0;
            $this->total = 0;
            $this->preco = 0;
            $this->quantidade = 1;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function atualizarProds()
    {
        try {
            $prod = Produto::find($this->produto);
            $this->barras = $prod->codigo;
            $this->preco = $prod->precovenda;
            $this->quantidade = 1;
            $this->desconto = 0;
            $this->total = $prod->precovenda;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function buscaProd()
    {
        try {
            $prod = DB::table('produtos')
                ->select('*')
                ->where('codigo', '=', $this->barras)
                ->where('empresa_id', '=', $this->empresa)
                ->first();
            if ($prod) {
                $this->produto = $prod->id;
                $this->preco = $prod->precovenda;
                $this->quantidade = 1;
                $this->desconto = 0;
                $this->total = $prod->precovenda;
            } else {
                $this->produto = "";
                $this->barras = '';
                $this->preco = 0;
                $this->quantidade = 0;
                $this->desconto = 0;
                $this->total = 0;
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function removerProduto($index)
    {
        try {
            $this->cancelarEdicaoItem();
            unset($this->vendaItens[$index]);
            $this->vendaItens = array_values($this->vendaItens);
            $subtotal = 0;
            foreach ($this->vendaItens as $item) {
                $subtotal = $subtotal + $item['total'];
            }
            $this->subtotal = $subtotal;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function containsProd($array, $value)
    {
        foreach ($array as $index => $arr) {
            if (in_array($value, $arr)) {
                return $index;
            }
        }
        return -1;
    }

    public function render()
    {
        try {
            return view('livewire.edit-pedido');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }
}
