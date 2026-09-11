<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FaturaService;
use App\Services\FluxoDeCaixaService;
use App\Services\ItemService;
use App\Services\NFeService;
use App\Services\PedidosService;
use App\Services\ProdutosService;
use App\Utils\FormatationUtil;
use App\Utils\NFeErroUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use NFePHP\Common\Exception\ValidatorException;
use NFePHP\DA\NFe\Daevento;
use NFePHP\DA\NFe\Danfe;
use RealRashid\SweetAlert\Facades\Alert;

class PedidosController extends Controller
{
    private PedidosService $pedidoServices;
    private EmpresasService $empresaServices;
    private FaturaService $faturaServices;
    private ItemService $itemServices;
    private ProdutosService $produtoServices;
    private EstoquesService $estoqueService;
    private FluxoDeCaixaService $fluxoCaixaService;

    public function __construct(PedidosService $pedidoServices, EmpresasService $empresaServices, ItemService $itemServices, FaturaService $faturaServices, ProdutosService $produtoServices, EstoquesService $estoqueService, FluxoDeCaixaService $fluxoCaixaService)
    {
        $this->pedidoServices = $pedidoServices;
        $this->empresaServices = $empresaServices;
        $this->itemServices = $itemServices;
        $this->faturaServices = $faturaServices;
        $this->produtoServices = $produtoServices;
        $this->estoqueService = $estoqueService;
        $this->fluxoCaixaService = $fluxoCaixaService;
    }

    public function imprimirCorrecao($id)
    {
        try {
            $venda = $this->pedidoServices->buscarPedido($id);
            $emitente = $this->empresaServices->buscarEmpresa($venda->empresa_id);
            $xmlRow = $venda->xmlCce;
            if (!$xmlRow) {
                return back()->with('error', 'XML de correção não encontrado.');
            }
            $daevento = new Daevento($xmlRow->xml, $emitente);
            $daevento->debugMode(true);
            $pdf = $daevento->render();
            return response($pdf)->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function inutil()
    {
        try {
            $user = Auth::user();
            return view('notas.inutilizar', ['empresa' => $user->empresa_id, 'mode' => 'nfe']);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
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
                "cnpj" => FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj),
                "schemes" => "PL_010_V1.30",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $emitente->csc,
                "CSCid" => '00000' . $emitente->idCsc,
            ], $emitente);
            $result = $nfe_service->inutilizarNum($emitente->serie, $request->numI, $request->numI, $request->justificativa, $emitente->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Inutilizacoes');
            if (!isset($result['erro'])) {
                return redirect('/inutilizar')->with('success', 'Inutilização feita com sucesso');
            } else {
                return redirect('/inutilizar')->with('warning', NFeErroUtil::formatar($result['data']));
            }
        } catch (ValidatorException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function cartaCorrecao(Request $request)
    {
        try {
            $venda = Pedido::find($request->venda_id_cce);
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
                "cnpj" => FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj),
                "schemes" => "PL_010_V1.30",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $emitente->csc,
                "CSCid" => '00000' . $emitente->idCsc,
            ], $emitente);

            $result = $nfe_service->cartaCorrecao($venda, $request->justificativa);
            if (!isset($result['erro'])) {
                return redirect('/venda')->with('success', 'Carta de Correção feita com sucesso');
            } else {
                return redirect('/venda')->with('warning', NFeErroUtil::formatar($this->extrairMensagemEventoNFe($result['data'])));
            }
        } catch (ValidatorException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function cancelarNFe(Request $request)
    {
        try {

            $venda = Pedido::find($request->venda_id_cancelar);
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
                "cnpj" => FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj),
                "schemes" => "PL_010_V1.30",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $emitente->csc,
                "CSCid" => '00000' . $emitente->idCsc,
            ], $emitente);
            $nfe = $nfe_service->cancelar($venda, $request->justificativa);
            if (!isset($nfe['erro'])) {
                $venda->status = 0;
                $venda->estado = 'Cancelado';
                $venda->total = 0;
                $venda->save();
                foreach ($venda->itens as $item) {
                    $this->estoqueService->reverseStock($item->produto_id, $item->qtde);
                }
                $this->fluxoCaixaService->estornarPorOrigem('NFe', $venda->id);
                return redirect('/venda')->with('success', 'Nota cancelada com sucesso');
            } else {
                return redirect('/venda')->with('error', NFeErroUtil::formatar($this->extrairMensagemEventoNFe($nfe['data'])));
            }
        } catch (ValidatorException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function imprimirCancelamento($id)
    {
        try {
            $venda = Pedido::find($id);
            $xmlRow = $venda->xmlCancelado;
            if (!$xmlRow) {
                return back()->with('error', 'XML de cancelamento não encontrado.');
            }
            $dadosEmitente = Empresa::find($venda->empresa_id);
            $daevento = new Daevento($xmlRow->xml, $dadosEmitente->toArray());
            $daevento->debugMode(true);
            $pdf = $daevento->render();
            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            session()->flash("erro", $e->getMessage());
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function imprimir($id)
    {
        try {
            $venda = Pedido::find($id);
            $xmlRow = $venda->xmlAutorizado;
            if (!$xmlRow) {
                return back()->with('error', 'XML da nota não encontrado.');
            }
            $danfe = new Danfe($xmlRow->xml);
            $danfe->creditsIntegratorFooter('SpeedNFE - www.f-softsistemas.com.br', false);
            $pdf = $danfe->render();
            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
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
                "cnpj" => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
                // "schemes" => "PL_009_V4",
                "schemes" => "PL_010_V1.30",
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
                    $resultado = $nfe_service->transmitir($signed, $result['chave'], $venda->id);
                    // dd($resultado);
                    if (isset($resultado['sucesso'])) {
                        DB::beginTransaction();

                        $venda->chave = $result['chave'];
                        $venda->status = 1;
                        $venda->estado = 'Autorizado';
                        $venda->numero_nfe = $result['nNf'];
                        $venda->save();
                        $empresa->update(['ultimaNFe' => $empresa->ultimaNFe + 1]);
                        if ($venda->tpNF) {
                            foreach ($venda->itens as $item) {
                                $this->estoqueService->out($item->produto_id, $item->qtde);
                            }
                            $this->fluxoCaixaService->registrarEntradaAutomatica(
                                $venda->empresa_id,
                                $venda->total,
                                'Venda NFe #' . $venda->numero_nfe . ($venda->cliente ? ' - ' . $venda->cliente->nome : ''),
                                $venda->data,
                                'NFe',
                                $venda->id
                            );
                        } else {
                            foreach ($venda->itens as $item) {
                                $this->estoqueService->reverseStock($item->produto_id, $item->qtde);
                            }
                        }

                        DB::commit();
                        return redirect('/vendas')->with('success', 'Nota enviada com sucesso');
                    } else {
                        DB::beginTransaction();

                        $venda->status = 3;
                        $venda->estado = 'Rejeitado';
                        $venda->save();

                        DB::commit();
                        Alert::warning('A NFe foi rejeitada pela SEFAZ', NFeErroUtil::formatar($resultado['erro']))->persistent();
                        return redirect('/vendas');
                    }
                } else {
                    if (DB::transactionLevel() > 0) {
                        DB::rollBack();
                    }
                    Alert::error('Não foi possível gerar a NFe', NFeErroUtil::formatar($result['erros_xml']))->persistent();
                    return redirect('/vendas');
                }
            } else {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                return redirect('/vendas')->with("error", 404);
            }
        } catch (ValidatorException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $pedido = $this->pedidoServices->buscarPedido($id);
            return view('vendas.edit', ['pedido' => $pedido]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $venda = $this->pedidoServices->buscarPedido($id);
            $isDevolucao = (int) $venda->finNF === 4;
            $usarReferenciaPorItem = $isDevolucao && PedidosService::referenciaItemDevolucaoHabilitada();

            if ($usarReferenciaPorItem) {
                $this->normalizarReferenciasDosItens($request);
            }

            $request->validate(array_merge([
                'cliente' => 'required|numeric',
                'cfop' => 'required|numeric',
                'vendaItens' => 'required|array|min:1',
                'info_complementares' => 'nullable'
            ], $this->regrasReferenciasDosItens($usarReferenciaPorItem)), $this->mensagensReferenciasDosItens());

            $this->validarReferenciasDuplicadas($request, $usarReferenciaPorItem);

            if (!$venda->chave) {
                $this->itemServices->deleteItems($venda->id);
                $subtotal = 0;
                $desconto = 0;
                foreach ($request->vendaItens as $item) {
                    $prod = $this->produtoServices->um($item['produto_id']);
                    $desconto = $desconto + $item['desconto'];
                    $subtotal = $subtotal + ($item['total']);
                }
                foreach ($request->vendaItens as $item) {
                    $prod = $this->produtoServices->um($item['produto_id']);
                    $desconto = 0;
                    $this->itemServices->create(
                        $id,
                        $prod,
                        $item['quantidade'],
                        $venda->empresa_id,
                        $item['desconto'],
                        $item['unitario'],
                        $usarReferenciaPorItem ? $item['dfe_referenciado_chave'] : null,
                        $usarReferenciaPorItem ? $item['dfe_referenciado_n_item'] : null
                    );
                }
                $this->faturaServices->update(
                    $subtotal,
                    $venda->fatura[0]->id,
                );
                $this->pedidoServices->update(
                    $venda->id,
                    $request->cliente,
                    $subtotal,
                    $desconto,
                    $request->cfop,
                    $request->info_complementares
                );
                return redirect()->route('vendas.editar', [$venda->id])->with('success', 'Nota atualizada com sucesso.');
            } else {
                return redirect()->route('vendas.index')->with('warning', 'Já foi emitida a NFe desse venda, não é possível realizar alterações.');
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function store(Request $request)
    {
        try {
            $isDevolucao = (int) $request->finalidade === 4;
            $usarReferenciaPorItem = $isDevolucao && PedidosService::referenciaItemDevolucaoHabilitada();

            if ($usarReferenciaPorItem) {
                $this->normalizarReferenciasDosItens($request);
            }

            $request->validate(array_merge([
                'empresa' => 'required|numeric',
                'finalidade' => 'required|numeric',
                'tipo' => 'required|numeric',
                'ref_nfe' => $isDevolucao && !$usarReferenciaPorItem ? 'required' : 'nullable',
                'cliente' => 'required|numeric',
                'cfop' => 'required|numeric',
                'vendaItens' => 'required|array|min:1',
                'info_complementares' => 'nullable|max:255',
                'aut_xml' => 'nullable|string|max:18',
            ], $this->regrasReferenciasDosItens($usarReferenciaPorItem)), array_merge([
                'required' => 'O campo :attribute é obrigatório!',
                'vendaItens.required' => 'Deve existir pelo menos um item no pedido!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
                'max' => 'O campo :attribute deve conter no máximo :max caracteres'
            ], $this->mensagensReferenciasDosItens()));

            $this->validarReferenciasDuplicadas($request, $usarReferenciaPorItem);

            $autXml = preg_replace('/\D/', '', $request->aut_xml ?? '');

            if (!empty($autXml) && !in_array(strlen($autXml), [11, 14])) {
                return back()
                    ->withInput()
                    ->with('warning', 'CPF/CNPJ autorizado para XML deve ter 11 ou 14 dígitos.');
            }

            DB::beginTransaction();
            $subtotal = 0;
            $desconto = 0;
            //Se ainda não atingiu o limite de Notas ou é janaina que está fazendo, permito a criação de uma nova nota, caso contrário faço o bloqueio da ação
            if ($this->pedidoServices->limiteDeNotas($request->empresa) < $this->empresaServices->buscarEmpresa($request->empresa)->limNFes || $request->empresa == 1) {
                foreach ($request->vendaItens as $item) {
                    $prod = $this->produtoServices->um($item['produto_id']);
                    $desconto = $desconto + $item['desconto'];
                    $subtotal = $subtotal + ($item['quantidade'] * $item['unitario']);
                }
                $pedido = $this->pedidoServices->create(
                    Auth::id(),
                    $request->cliente,
                    $subtotal,
                    $desconto,
                    $request->empresa,
                    $request->cfop,
                    $request->finalidade == 4 ? 4 : 1,
                    $usarReferenciaPorItem ? null : $request->ref_nfe,
                    $request->tipo,
                    $request->info_complementares,
                    $autXml
                );
                foreach ($request->vendaItens as $item) {
                    $prod = $this->produtoServices->um($item['produto_id']);

                    if ($request->finalidade == 1) {
                        $this->itemServices->verificaVendaPorProduto($request->empresa, $prod);
                    }

                    $this->itemServices->create(
                        $pedido->id,
                        $prod,
                        $item['quantidade'],
                        $request->empresa,
                        $item['desconto'],
                        $item['unitario'],
                        $usarReferenciaPorItem ? $item['dfe_referenciado_chave'] : null,
                        $usarReferenciaPorItem ? $item['dfe_referenciado_n_item'] : null
                    );
                }
                $this->faturaServices->create(
                    $subtotal - $desconto,
                    $pedido->id,
                    $pedido->finNF,
                    $request->empresa
                );
                DB::commit();
                return redirect()->route('vendas.index')->with('success', "Nota criada com sucesso");
            } else {
                DB::rollBack();
                return redirect()->route('vendas.index')->with('warning', 'Limite de notas Atingido');
            }
        } catch (ValidationException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getmessage());
        }
    }

    /**
     * O retorno de eventos (CCe/cancelamento) do NFeService ora vem como o
     * array do XML padronizado, ora como a mensagem de uma exceção (string),
     * dependendo de onde a falha ocorreu. Aqui extraímos o xMotivo quando
     * disponível, sem arriscar acessar índice de array numa string.
     */
    private function extrairMensagemEventoNFe($data)
    {
        if (is_array($data)) {
            return $data['retEvento']['infEvento']['xMotivo'] ?? $data;
        }

        return $data;
    }

    private function normalizarReferenciasDosItens(Request $request): void
    {
        $itens = $request->input('vendaItens');

        if (!is_array($itens)) {
            return;
        }

        foreach ($itens as &$item) {
            if (array_key_exists('dfe_referenciado_chave', $item)) {
                $item['dfe_referenciado_chave'] = preg_replace(
                    '/\D/',
                    '',
                    (string) $item['dfe_referenciado_chave']
                );
            }
        }
        unset($item);

        $request->merge(['vendaItens' => $itens]);
    }

    private function regrasReferenciasDosItens(bool $usarReferenciaPorItem): array
    {
        $required = $usarReferenciaPorItem ? 'required' : 'nullable';

        return [
            'vendaItens.*.dfe_referenciado_chave' => [$required, 'digits:44'],
            'vendaItens.*.dfe_referenciado_n_item' => [$required, 'integer', 'between:1,990'],
        ];
    }

    private function mensagensReferenciasDosItens(): array
    {
        return [
            'vendaItens.*.dfe_referenciado_chave.required' => 'Informe a chave da NF-e de origem em cada item da devolução.',
            'vendaItens.*.dfe_referenciado_chave.digits' => 'A chave da NF-e de origem deve conter 44 dígitos.',
            'vendaItens.*.dfe_referenciado_n_item.required' => 'Informe o número do item correspondente na NF-e de origem.',
            'vendaItens.*.dfe_referenciado_n_item.integer' => 'O número do item da NF-e de origem deve ser inteiro.',
            'vendaItens.*.dfe_referenciado_n_item.between' => 'O número do item da NF-e de origem deve estar entre 1 e 990.',
        ];
    }

    private function validarReferenciasDuplicadas(Request $request, bool $usarReferenciaPorItem): void
    {
        if (!$usarReferenciaPorItem) {
            return;
        }

        $referencias = [];

        foreach ($request->input('vendaItens', []) as $index => $item) {
            $referencia = ($item['dfe_referenciado_chave'] ?? '') . ':' . ($item['dfe_referenciado_n_item'] ?? '');

            if (isset($referencias[$referencia])) {
                throw ValidationException::withMessages([
                    "vendaItens.$index.dfe_referenciado_n_item" => 'A mesma chave e o mesmo item da NF-e de origem foram informados mais de uma vez.',
                ]);
            }

            $referencias[$referencia] = true;
        }
    }

    public function destroyPedido(Request $request)
    {
        try {
            $this->pedidoServices->delete($request->pedido_id);
            return redirect()->route('vendas.index')->with('success', 'Nota deletada com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function todos(Request $request)
    {
        try {
            $dataInicio = $request->filled('data_inicio')
                ? $request->input('data_inicio')
                : now()->subMonths(2)->startOfDay()->format('Y-m-d');
            $dataFim = $request->filled('data_fim')
                ? $request->input('data_fim')
                : now()->endOfDay()->format('Y-m-d');

            $filtros = [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'cliente' => $request->input('cliente'),
                'chassi' => $request->input('chassi'),
                'estado' => $request->input('estado'),
            ];

            $pedidos = $this->pedidoServices->formatedVenda(Auth::user()->empresa_id, $filtros);

            return view('vendas.todos', [
                'pedidos' => $pedidos,
                'empresa' => Auth::user()->empresa_id,
                'filtros' => $filtros,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function visualizar($id)
    {
        try {
            $pedido = $this->pedidoServices->buscarPedido($id);
            $empresa = $this->empresaServices->buscarEmpresa($pedido->empresa_id);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int) $empresa->ambiente,
                "razaosocial" => $empresa->razao,
                "siglaUF" => $empresa->endereco->uf,
                "cnpj" => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
                "schemes" => "PL_010_V1.30",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $empresa->csc,
                "CSCid" => "00000" . $empresa->idCsc,
            ], $empresa);
            $result = $nfe_service->gerarXml($pedido, $empresa);
            $danfe = new Danfe($result['xml']);
            $pdf = $danfe->render();
            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (ValidatorException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function new()
    {
        try {
            return view('vendas.create');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function totalMesNFe()
    {
        try {
            $results = $this->pedidoServices->getTotalNFePerMonth(Auth::user()->empresa_id);
            return response()->json($results);
        } catch (Exception $e) {
            return response()->json('error: Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage(), $e->getCode());
        }
    }
}
