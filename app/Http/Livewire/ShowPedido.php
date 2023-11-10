<?php

namespace App\Http\Livewire;

use App\Models\Cliente;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\EmpresasService;
use App\Services\PedidosService;
use App\Services\UsersService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ShowPedido extends Component
{
    public $pedido;
    public $barras = '';
    public $empresa = '';
    public $produto = '';
    public $cliente = '';
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
    public $cfops = [];

    public function mount()
    {
        try {
            //declaração dos services para recuperar dados
            $sUsers = new UsersService();
            $sEmpresas = new EmpresasService();
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
            $this->empresas = $sEmpresas->todos($user->empresa_id);
            $this->clientes = Cliente::all()->where('empresa_id', '=', $this->empresa);
            $this->produtos = Produto::all()->where('empresa_id', '=', $this->empresa);
            $this->cfops = $sPedidos->cfopAll();
            $this->desconto = $pedido->desconto;
            $this->subtotal = $pedido->subtotal;

            foreach ($itens as $item) {
                $produto = Produto::find($item->produto_id);
                $this->vendaItens[] = [
                    'produto_id' => $produto->id,
                    'descricao' => $produto->produto,
                    'quantidade' => $item->qtde,
                    'unitario' => $produto->precovenda,
                    'desconto' => $item->desconto,
                    'total' => ($produto->precovenda * $item->qtde) - $item->desconto,
                ];
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function render()
    {
        try {
            return view('livewire.show-pedido');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }
}
