<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\FaturaPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\EmpresasService;
use App\Services\FaturaService;
use App\Services\ItemService;
use App\Services\NFeService;
use App\Services\PedidosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use NFePHP\DA\NFe\Daevento;
use NFePHP\DA\NFe\Danfe;

class PedidosController extends Controller
{
    private PedidosService $pedidoServices;
    private EmpresasService $empresaServices;
    private FaturaService $faturaServices;
    private ItemService $itemServices;

    public function __construct(PedidosService $pedidoServices, EmpresasService $empresaServices, ItemService $itemServices, FaturaService $faturaServices)
    {
        $this->pedidoServices = $pedidoServices;
        $this->empresaServices = $empresaServices;
        $this->itemServices = $itemServices;
        $this->faturaServices = $faturaServices;
    }

    public function imprimirCorrecao($id)
    {
        try {
            $venda = $this->pedidoServices->buscarPedido($id);
            $emitente = $this->empresaServices->buscarEmpresa($venda->empresa_id);
            $xml = file_get_contents($emitente->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/CCe/' . $venda->chave . '.xml');
            $daevento = new Daevento($xml, $emitente);
            $daevento->debugMode(true);
            $pdf = $daevento->render();
            return response($pdf)->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            session()->flash("erro", $e->getMessage());
            return back();
        }
    }

    public function inutil()
    {
        try {
            $user = Auth::user();
            return view('notas.inutilizar', ['empresa' => $user->empresa_id]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function inutilizar(Request $request)
    {
        try {
            $emitente = Empresa::find($request->empresa_id);
            if ($emitente == null) {
                return redirect('/inutilizar')->with('error', 'Configure o emitente');
            }
            $cnpj = str_replace(".", "", $emitente->cpf_cnpj);
            $cnpj = str_replace("/", "", $cnpj);
            $cnpj = str_replace("-", "", $cnpj);
            $cnpj = str_replace(" ", "", $cnpj);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int) $emitente->ambiente,
                "razaosocial" => $emitente->razao,
                "siglaUF" => $emitente->endereco->uf,
                "cnpj" => $cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $emitente->csc,
                "CSCid" => '00000' . $emitente->idCsc,
            ], $emitente);
            $result = $nfe_service->inutilizarNum($emitente->serie, $request->numI, $request->numI, $request->justificativa, $emitente->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Inutilizacoes');
            if (!isset($result['erro'])) {
                return redirect('/inutilizar')->with('success', 'Inutilização feita com sucesso');
            } else {
                return redirect('/inutilizar')->with('success', $result['data']);
            }
        } catch (\Exception $e) {
            return redirect('/inutilizar')->with('error', $e->getMessage());
        }
    }

    public function cartaCorrecao(Request $request)
    {
        try {
            $venda = Pedido::find($request->venda_id);
            $emitente = Empresa::find($venda->empresa_id);

            if ($emitente == null) {
                return response()->json('Configure o emitente', 404);
            }

            $cnpj = str_replace(".", "", $emitente->cpf_cnpj);
            $cnpj = str_replace("/", "", $cnpj);
            $cnpj = str_replace("-", "", $cnpj);
            $cnpj = str_replace(" ", "", $cnpj);

            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int) $emitente->ambiente,
                "razaosocial" => $emitente->razao,
                "siglaUF" => $emitente->endereco->uf,
                "cnpj" => $cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $emitente->csc,
                "CSCid" => '00000' . $emitente->idCsc,
            ], $emitente);

            $result = $nfe_service->cartaCorrecao($venda, $request->justificativa, $emitente->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/CCe');
            if (!isset($result['erro'])) {
                return redirect('/venda')->with('success', 'Carta de Correção feita com sucesso');
            } else {
                return redirect('/venda')->with('error', $result['data']);
            }

        } catch (\Exception $e) {
            return redirect('/venda')->with('error', $e->getMessage());
        }
    }

    public function cancelarNFe(Request $request)
    {
        try {
            $venda = Pedido::find($request->venda_id);
            $emitente = Empresa::find($venda->empresa_id);
            if ($emitente == null) {
                return response()->json('Configure o emitente', 404);
            }
            $cnpj = str_replace(".", "", $emitente->cpf_cnpj);
            $cnpj = str_replace("/", "", $cnpj);
            $cnpj = str_replace("-", "", $cnpj);
            $cnpj = str_replace(" ", "", $cnpj);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int) $emitente->ambiente,
                "razaosocial" => $emitente->razao,
                "siglaUF" => $emitente->endereco->uf,
                "cnpj" => $cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $emitente->csc,
                "CSCid" => '00000' . $emitente->idCsc,
            ], $emitente);
            $nfe = $nfe_service->cancelar($venda, $request->justificativa, $emitente->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Canceladas');
            if (!isset($nfe['erro'])) {
                $venda->status = 0;
                $venda->estado = 'Cancelado';
                $venda->total = 0;
                $venda->save();
                return redirect('/venda')->with('success', 'Nota cancelada com sucesso');
            } else {
                return redirect('/venda')->with('error', $nfe['data']);
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect('/venda')->with('error', $e->getMessage());
        }
    }

    public function imprimirCancelamento($id)
    {
        try {
            $venda = Pedido::find($id);
            $empresa = Empresa::find($venda->empresa_id);
            $xml = file_get_contents(public_path($empresa->fantasia . '/' . date_format($venda->created_at, 'Y') . '/' . date_format($venda->created_at, 'm') . '/notas/Canceladas/') . $venda->chave . '.xml');
            $dadosEmitente = Empresa::find($venda->empresa_id);
            $daevento = new Daevento($xml, $dadosEmitente->toArray());
            $daevento->debugMode(true);
            $pdf = $daevento->render();
            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            session()->flash("erro", $e->getMessage());
            return redirect()->back();
        }
    }

    public function imprimir($id)
    {
        try {
            $venda = Pedido::find($id);
            $empresa = Empresa::find($venda->empresa_id);
            $xml = file_get_contents(public_path($empresa->fantasia . '/' . date_format($venda->created_at, 'Y') . '/' . date_format($venda->created_at, 'm') . '/notas/Autorizadas/') . $venda->chave . '.xml');
            $danfe = new Danfe($xml);
            $pdf = $danfe->render();
            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            session()->flash("erro", $e->getMessage());
            return redirect()->back();
        }
    }

    public function enviarNFe($id)
    {
        try {
            $venda = $this->pedidoServices->buscarPedido($id);
            $empresa = $this->empresaServices->buscarEmpresa($venda->empresa_id);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int) $empresa->ambiente,
                "razaosocial" => $empresa->razao,
                "siglaUF" => $empresa->endereco->uf,
                "cnpj" => '42879649000174',
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $empresa->csc,
                "CSCid" => "00000" . $empresa->idCsc,
            ], $empresa);
            if ($venda->estado->value == 'Rejeitado' || $venda->estado->value == 'Pendente') {
                $result = $nfe_service->gerarXml($venda, $empresa);
                // dd($result);
                if (!isset($result['erros_xml'])) {
                    $signed = $nfe_service->sign($result['xml']);
                    // dd($signed);
                    $resultado = $nfe_service->transmitir($signed, $result['chave'], $empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Autorizadas');
                    // dd($resultado);
                    if (isset($resultado['sucesso'])) {
                        $venda->chave = $result['chave'];
                        $venda->status = 1;
                        $venda->estado = 'Autorizado';
                        $venda->numero_nfe = $result['nNf'];
                        $venda->save();
                        $empresa->update(['ultimaNFe' => $empresa->ultimaNFe + 1]);
                        return redirect('/vendas')->with('success', 'Nota enviada com sucesso');
                    } else {
                        $venda->status = 3;
                        $venda->estado = 'Rejeitado';
                        $venda->save();
                        return redirect('/vendas')->with('error', $resultado['erro']);
                    }
                } else {
                    return redirect('/vendas')->with('error', $result['erros_xml']);
                }
            } else {
                return redirect('/vendas')->with("error", 404);
            }
            return redirect('/vendas')->with('success', $venda);
        } catch (Exception $e) {
            dd($e);
            return redirect('/vendas')->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $pedido)
    {
        $venda = Pedido::find($pedido);

        if ($venda->chave == '') {
            // return $request;
            DB::table('item_pedidos')->where('pedido_id', '=', $venda->id)->delete();

            $subtotal = 0;
            $desconto = 0;

            foreach ($request->vendaItens as $item) {
                $prod = Produto::find($item['produto_id']);
                $desconto = $desconto + $item['desconto'];
                $subtotal = $subtotal + ($item['total']);
            }

            foreach ($request->vendaItens as $item) {
                $prod = Produto::find($item['produto_id']);

                $desconto = 0;

                ItemPedido::create([
                    'pedido_id' => $venda->id,
                    'produto_id' => $prod->id,
                    'qtde' => $item['quantidade'],
                    'empresa_id' => $venda->empresa_id,
                    'desconto' => $item['desconto'],
                    'acrescimo' => 0,
                    'unitario' => $item['unitario'],
                ]);
            }

            FaturaPedido::create([
                'valor' => $venda->total,
                'vencimento' => today(),
                'venda_id' => $venda->id,
                'forma_pag_id' => $request->forma,
                'empresa_id' => $venda->empresa_id,
            ]);

            $venda->update([
                'cliente_id' => $request->cliente,
                'data' => today(),
                'status' => 0,
                'subtotal' => $subtotal,
                'desconto' => $desconto,
                'total' => $subtotal,
                'forma_pag_id' => $request->forma,
                'cfop' => $request->cfop,
            ]);

            return redirect('vendas')->with('success', 'Nota editada com sucesso.');

        } else {
            return redirect('vendas')->with('alert', 'Já foi emitida a NFe desse venda, não é possível realizar alterações.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'empresa' => 'required|numeric',
                'cliente' => 'required|numeric',
                'cfop' => 'required|numeric',
                'vendaItens' => 'required'
            ]);
            $subtotal = 0;
            $desconto = 0;
            //Se ainda não atingiu o limite de Notas ou é janaina que está fazendo, permito a criação de uma nova nota, caso contrário faço o bloqueio da ação
            if ($this->pedidoServices->limiteDeNotas($request->empresa) < $this->empresaServices->buscarEmpresa($request->empresa)->limNotas || $request->empresa == 1) {
                foreach ($request->vendaItens as $item) {
                    $prod = Produto::find($item['produto_id']);
                    $desconto = $desconto + $item['desconto'];
                    $subtotal = $subtotal + ($item['total']);
                }
                $pedido = $this->pedidoServices->create(
                    Auth::id(),
                    $request->cliente,
                    $subtotal,
                    $desconto,
                    $request->empresa,
                    $request->cfop
                );
                foreach ($request->vendaItens as $item) {
                    $prod = Produto::find($item['produto_id']);
                    $this->itemServices->create(
                        $pedido,
                        $prod,
                        $item['quantidade'],
                        $request->empresa,
                        $item['desconto'],
                        $item['unitario']
                    );
                }
                $this->faturaServices->create(
                    $subtotal,
                    $pedido,
                    $request->empresa
                );
                return redirect()->route('vendas.index')->with('success', "Nota criada com sucesso");
            } else {
                return redirect()->route('vendas.index')->with('warning', 'Limite de notas Atingido');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento!, Erro: '. $e);
        }
    }

    public function destroyPedido(Request $request)
    {
        try {
            $this->pedidoServices->delete($request->pedido_id);
            return redirect()->route('vendas.index')->with('success', 'Nota deletada com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Não foi possível deletar a nota, tente novamente');
        }
    }

    public function todos()
    {
        try {
            $pedidos = $this->pedidoServices->formatedVenda(Auth::user()->empresa_id);
            return view('vendas.todos', ['pedidos' => $pedidos]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function visualizar($id)
    {
        try {
            $pedido = $this->pedidoServices->buscarPedido($id);
            return view('vendas.visualizar', ['pedido' => $pedido]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function new ()
    {
        try {
            return view('vendas.create');
        } catch (Exception $e) {
            return back();
        }
    }
}
