<?php

namespace App\Http\Livewire;

use App\Models\cfop;
use App\Models\Cliente;
use App\Models\FormaPag;
use App\Models\ItemPedido;
use App\Models\Produto;
use Livewire\Component;
use App\Models\Pedido;
use App\Services\ProdutosService;
use App\Services\ClientesService;
use App\Services\EmpresasService;
use App\Services\UsersService;
use App\Services\FormaPagService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditPedido extends Component
{
    public $pedido;
    public $barras = '';
    public $empresa = '';
    public $produto = '';
    public $cliente = '';
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

    public function mount(){
        //declaração dos services para recuperar dados
        $sUsers = new UsersService();
        $sEmpresas = new EmpresasService();
        $sClientes = new ClientesService();
        $sProdutos = new ProdutosService();
        $sFormas   = new FormaPagService();


        //Recuperando empresa_id do usuario logado
        $user = $sUsers->getEmpresa(Auth::id());
        // $this->empresa = $user->empresa_id;

        $pedido = Pedido::findOrFail($this->pedido);
        $itens = ItemPedido::all()->where('pedido_id', '=', $pedido->id);

        $this->cfop     = $pedido->cfop_id;
        $this->bcfop    = cfop::findOrFail($pedido->cfop_id)->cfop;
        $this->empresa  = $pedido->empresa_id;
        $this->cliente  = $pedido->cliente_id;
        $this->pag      = $pedido->forma_pag_id;
        $this->empresas = $sEmpresas->todos($user->empresa_id);
        $this->clientes = Cliente::all()->where('empresa_id', '=', $this->empresa);
        $this->produtos = Produto::all()->where('empresa_id', '=', $this->empresa);
        $this->cfops    = cfop::all();
        $this->formas   = FormaPag::all();
        $this->desconto = $pedido->desconto;
        $this->subtotal = $pedido->subtotal;
        $this->total    = $pedido->total;



        foreach($itens as $item){
            $produto = Produto::findOrFail($item->produto_id);
            $this->vendaItens[] = [
                'produto_id'    =>      $produto->id,
                'descricao'     =>      $produto->produto,
                'quantidade'    =>      $item->qtde,
                'unitario'      =>      $produto->precocusto,
                'desconto'      =>      $item->desconto,
                'total'         =>      ($produto->precocusto*$item->qtde) - $item->desconto
            ];
        }

    }

    public function atualizarBCfop(){
        $this->bcfop = CFOP::findOrFail($this->cfop)->cfop;
    }

    public function buscaCfop(){
        $prod = DB::table('cfops')
        ->select('*')
        ->where('cfop', 'like', $this->bcfop."%")
        ->first();

        if($prod){
            $this->cfop = $prod->id;
        }else{
            $this->cfop = '';
        }
    }

    public function atualizarTot(){
        $total = $this->quantidade * $this->preco;
        $desconto = $total * $this->desconto/100;

        $this->total = $total - $desconto;

    }

    public function salvarProd(){
        $prod = Produto::findOrFail($this->produto);
        $total = $this->quantidade * $this->preco;
        $desconto = $total * $this->desconto/100;

        $this->vendaItens[] = ['produto_id' => $prod->id, 'descricao' => $prod->produto, 'quantidade' => $this->quantidade, 'unitario' => $this->preco, 'desconto' => $desconto, 'total' => $total - $desconto];

        $subtotal = 0;

        foreach($this->vendaItens as $item){
            $subtotal = $subtotal + $item['total'];
        }

    }

    public function atualizarProds(){
        $prod = Produto::findOrFail($this->produto);
        $this->barras = $prod->codigo;
        $this->preco = $prod->precovenda;
        $this->quantidade = 1;
        $this->desconto = 0;
        $this->total = $prod->precovenda;
    }

    public function buscaProd(){
        $prod = DB::table('produtos')
        ->select('*')
        ->where('codigo', '=', $this->barras)
        ->where('empresa_id', '=', $this->empresa)
        ->first();

        if($prod){
            $this->produto = $prod->id;
            $this->preco = $prod->precovenda;
            $this->quantidade = 1;
            $this->desconto = 0;
            $this->total = $prod->precovenda;
        }else{
            $this->produto = "";
            $this->barras = '';
            $this->preco = 0;
            $this->quantidade = 0;
            $this->desconto = 0;
            $this->total = 0;
        }
    }

    public function removerProduto($index){
        unset($this->vendaItens[$index]);
        $this->vendaItens = array_values($this->vendaItens);

        $subtotal = 0;

        foreach($this->vendaItens as $item){
            $subtotal = $subtotal + $item['total'];
        }
        $this->subtotal = $subtotal;
    }

    public function cancelar(){
        redirect('/vendas');
    }

    public function render()
    {
        return view('livewire.edit-pedido');
    }
}
