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
    public $finalidade = 1;
    public $tipo = 1;
    public $referenciaItemHabilitada = false;

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

    public $buscaCliente = '';
    public $clientesModal = [];
    public $cliente;

    public $buscaProduto = '';
    public $produtosModal = [];

    protected $listeners = ['abrirModalClientes', 'fecharModalClientes', 'abrirModalProdutos', 'fecharModalProdutos', 'selecionarProduto'];


    public function mount()
    {
        $this->referenciaItemHabilitada = PedidosService::referenciaItemDevolucaoHabilitada();

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

                $this->produtosModal = $sProdutos->todos($user->empresa_id)->toarray();
                $this->clientesModal = $sClientes->todos($user->empresa_id)->toarray();

                // $this->formas = $sFormas->todos();
                $this->cfops = $sPedidos->cfopAll();
            } else { //empresa fsoft carrega apenas a lista de empresas, para que seja selecionada uma
                $this->empresas = $sEmpresas->todos($user->empresa_id);
                $this->clientes = $sClientes->todosClientes();
                $this->produtos = $sProdutos->todosProdutos();

                $this->produtosModal = $sProdutos->todosProdutos()->toarray();
                $this->clientesModal = $sClientes->todosClientes()->toarray();
                // $this->formas = $sFormas->todos();
                $this->cfops = $sPedidos->cfopAll();
            }
            // dd($this->clientesModal);
            $this->cfop = '';
            $this->vendaItens = [];
            $this->formasVenda = [];

            // Se voltamos aqui após uma falha de validação no envio do formulário,
            // restaura os dados que o usuário já tinha preenchido (inclusive os itens
            // já adicionados) em vez de reiniciar o formulário do zero.
            if (old('vendaItens')) {
                $this->empresa = old('empresa', $this->empresa);
                $this->cliente = old('cliente');
                $this->cfop = old('cfop', '');
                $this->finalidade = old('finalidade', $this->finalidade);
                $this->tipo = old('tipo', $this->tipo);

                if ($this->cfop) {
                    $cfopEncontrado = $sPedidos->findCfop($this->cfop);
                    $this->bcfop = $cfopEncontrado->cfop ?? '';
                }

                $subtotal = 0;
                foreach (old('vendaItens') as $item) {
                    $produto = Produto::find($item['produto_id']);
                    $total = ($item['unitario'] * $item['quantidade']) - $item['desconto'];
                    $this->vendaItens[] = [
                        'produto_id' => $item['produto_id'],
                        'descricao' => $produto->produto ?? '',
                        'quantidade' => $item['quantidade'],
                        'unitario' => $item['unitario'],
                        'desconto' => $item['desconto'],
                        'total' => $total,
                        'dfe_referenciado_chave' => $item['dfe_referenciado_chave'] ?? '',
                        'dfe_referenciado_n_item' => $item['dfe_referenciado_n_item'] ?? '',
                    ];
                    $subtotal += $total;
                }
                $this->subtotal = $subtotal;
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function atualizarBCfop()
    {
        try {
            $this->bcfop = DB::table('cfops')->where('id', $this->cfop)->get()->cfop;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: ' . $e);
        }
    }

    public function buscaCfop()
    {

        try {
            $prod = DB::table('cfops')
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
            // $desconto = $total * $this->desconto / 100;
            $desconto = $this->desconto;
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
                    $desconto =  $this->desconto;
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

    public function containsProd($array, $value)
    {
        foreach ($array as $index => $arr) {
            if (in_array($value, $arr)) {
                return $index;
            }
        }
        return -1;
    }

    public function refNFeSection()
    {
        switch ($this->finalidade) {
            case 1:
                $this->tipo = 1;
                break;
            case 0:
                $this->tipo = 0;
                break;
            case 4:
                $this->tipo = 1;
                break;
        }
        return $this->emit('section_nfe', $this->finalidade);
    }

    public function render()
    {
        return view('livewire.pedido');
    }

    public function abrirModalClientes()
    {
        $this->dispatchBrowserEvent('abrirModalClientes');
    }

    public function fecharModalClientes()
    {
        $this->dispatchBrowserEvent('fecharModalClientes');
    }

    public function updatedBuscaCliente()
    {
        $termo = '%' . $this->buscaCliente . '%';
        $this->clientesModal = Cliente::where('nome', 'like', $termo)
            ->orWhere('cpf_cnpj', 'like', $termo)
            ->get()
            ->toarray();
    }

    public function selecionarCliente($id)
    {
        $this->cliente = $id;
        $this->dispatchBrowserEvent('fecharModalClientes');
    }

    public function abrirModalProdutos()
    {
        $this->dispatchBrowserEvent('abrirModalProdutos');
    }

    public function fecharModalProdutos()
    {
        $this->dispatchBrowserEvent('fecharModalProdutos');
    }

    public function updatedBuscaProduto()
    {
        $termo = '%' . $this->buscaProduto . '%';
        $this->produtosModal = Produto::where('produto', 'like', $termo)
        ->where('empresa_id', $this->empresa)
        ->get()
        ->toarray();
    }

    public function selecionarProduto($id)
    {
        $this->produto = $id;
        $this->atualizarProds();
        $this->dispatchBrowserEvent('fecharModalProdutos');
    }
}
