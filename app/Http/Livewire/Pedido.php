<?php

namespace App\Http\Livewire;

use App\Models\Cliente;
use App\Models\FormaPag;
use App\Models\Produto;
use App\Services\ClientesService;
use App\Services\EmpresasService;
use App\Services\FormaPagService;
use App\Services\PedidosService;
use App\Services\ProdutosService;
use App\Services\UsersService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pedido extends Component
{
    public $barras = '';
    public $empresaL = '';
    public $empresa = '';
    public $produto = '';
    public $cfop = '';
    public $bcfop = '';
    public $forma = '';

    public $desconto = 0;
    public $subtotal = 0;
    public $preco = 0;
    public $quantidade = 1;
    public $total = 0;
    public $valPag = 0;

    public $empresas = [];
    public $clientes = [];
    public $vendaItens = [];
    public $formasVenda = [];
    public $produtos = [];
    public $formas = [];
    public $cfops = [];

    public function mount()
    {
        //declaração dos services para recuperar dados
        try {
            $sUsers = new UsersService();
            $sEmpresas = new EmpresasService();
            $sClientes = new ClientesService();
            $sProdutos = new ProdutosService();
            $sFormas = new FormaPagService();
            $sPedidos = new PedidosService();
            //Recuperando empresa_id do usuario logado
            $user = $sUsers->getEmpresa(Auth::id());
            $this->empresaL = $user->empresa_id;
            // $this->empresa = $user->empresa_id;

            //preenchendo arrays com os valores da empresa selecionada, caso a empresa não seja a fsoft
            if ($user->empresa_id != 1) {
                $this->empresa = $user->empresa_id;
                $this->empresas = $sEmpresas->todos($user->empresa_id);
                $this->clientes = $sClientes->todos($user->empresa_id);
                $this->produtos = $sProdutos->todos($user->empresa_id);
                $this->formas = $sFormas->todos();
                $this->cfops = $sPedidos->cfopAll();
            } else { //empresa fsoft carrega apenas a lista de empresas, para que seja selecionada uma
                $this->empresas = $sEmpresas->todas();
                $this->clientes = $sClientes->todosClientes();
                $this->produtos = $sProdutos->todosProdutos();
                $this->formas = $sFormas->todos();
                $this->cfops = $sPedidos->cfopAll();
            }
            $this->cfop = '';
            $this->vendaItens = [];
            $this->formasVenda = [];
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function atualizarBCfop()
    {
        try {
            $this->bcfop = DB::table('cfop')->where('id', $this->cfop)->get()->cfop;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function buscaCfop()
    {

        try {
            $prod = DB::table('cfop')
                ->select('*')
                ->where('cfop', $this->bcfop)
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
                $prod = Produto::find($this->produto);
                $total = $this->quantidade * $this->preco;
                $desconto = $total * $this->desconto / 100;
                $this->vendaItens[] = ['produto_id' => $prod->id, 'descricao' => $prod->produto, 'quantidade' => $this->quantidade, 'unitario' => $this->preco, 'desconto' => $desconto, 'total' => $total - $desconto];
                $subtotal = 0;
                foreach ($this->vendaItens as $item) {
                    $subtotal = $subtotal + $item['total'];
                }
                $this->subtotal = $subtotal;
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

    public function salvarForma()
    {
        try {
            $prod = FormaPag::find($this->forma);
            $total = $this->valPag;
            $this->formasVenda[] = ['forma_id' => $prod->id, 'descricao' => $prod->descricao, 'total' => $total];
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function atualizarProds()
    {
        try {

            $prod = Produto::findOrFail($this->produto);
            $this->barras = $prod->codigo;
            $this->preco = $prod->precovenda;
            $this->quantidade = 1;
            $this->desconto = 0;
            $this->total = $prod->precovenda;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function atualizarArrays()
    {
        try {
            if ($this->empresaL == 1) {
                $this->clientes = Cliente::all()->where('empresa_id', '=', $this->empresa);
                $this->produtos = Produto::all()->where('empresa_id', '=', $this->empresa);
                $this->formas = FormaPag::all();
            }
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

    public function removerForma($index)
    {
        try {
            unset($this->formasVenda[$index]);
            $this->formasVenda = array_values($this->formasVenda);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function render()
    {
        return view('livewire.pedido');
    }
}
