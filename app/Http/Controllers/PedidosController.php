<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\EnviaNFe;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\FaturaService;
use App\Services\FluxoDeCaixaService;
use App\Services\ItemFiscalService;
use App\Services\ItemService;
use App\Services\NFeService;
use App\Services\PedidosService;
use App\Services\ProdutosService;
use App\Utils\FormatationUtil;
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
    use EnviaNFe;

    private PedidosService $pedidoServices;

    private EmpresasService $empresaServices;

    private FaturaService $faturaServices;

    private ItemService $itemServices;

    private ProdutosService $produtoServices;

    private EstoquesService $estoqueService;

    private FluxoDeCaixaService $fluxoCaixaService;

    private ItemFiscalService $itemFiscalService;

    public function __construct(PedidosService $pedidoServices, EmpresasService $empresaServices, ItemService $itemServices, FaturaService $faturaServices, ProdutosService $produtoServices, EstoquesService $estoqueService, FluxoDeCaixaService $fluxoCaixaService, ItemFiscalService $itemFiscalService)
    {
        $this->itemFiscalService = $itemFiscalService;
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
            if (! $xmlRow) {
                return back()->with('error', 'XML de correção não encontrado.');
            }
            $daevento = new Daevento($xmlRow->xml, $emitente);
            $daevento->debugMode(true);
            $pdf = $daevento->render();

            return response($pdf)->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
        }
    }

    public function inutil()
    {
        try {
            $user = Auth::user();

            return view('notas.inutilizar', ['empresa' => $user->empresa_id, 'mode' => 'nfe']);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
        }
    }

    public function inutilizar(Request $request)
    {
        $resultado = $this->_inutilizarNumeracao(
            (int) $request->empresa_id,
            (int) $request->numI,
            (int) $request->numI,
            (string) $request->justificativa,
            $this->empresaServices
        );

        if ($resultado->status === 'success') {
            return redirect('/inutilizar')->with('success', $resultado->message);
        }

        if ($resultado->type === 'emitente_nao_configurado') {
            return redirect('/inutilizar')->with('error', $resultado->message);
        }

        return redirect('/inutilizar')->with('warning', $resultado->message);
    }

    public function cartaCorrecao(Request $request)
    {
        $venda = Pedido::find($request->venda_id_cce);

        if (! $venda) {
            return response()->json('Nota não encontrada', 404);
        }

        $resultado = $this->_cartaCorrecaoPeloId(
            $venda->id,
            (string) $request->justificativa,
            $this->pedidoServices,
            $this->empresaServices
        );

        if ($resultado->type === 'emitente_nao_configurado') {
            return response()->json($resultado->message, 404);
        }

        if ($resultado->status === 'success') {
            return redirect('/venda')->with('success', $resultado->message);
        }

        return redirect('/venda')->with('warning', $resultado->message);
    }

    public function cancelarNFe(Request $request)
    {
        $venda = Pedido::find($request->venda_id_cancelar);

        if (! $venda) {
            return response()->json('Nota não encontrada', 404);
        }

        $resultado = $this->_cancelarNFePeloId(
            $venda->id,
            (string) $request->justificativa,
            $this->pedidoServices,
            $this->empresaServices,
            $this->estoqueService,
            $this->fluxoCaixaService
        );

        if ($resultado->type === 'emitente_nao_configurado') {
            return response()->json($resultado->message, 404);
        }

        if ($resultado->status === 'success') {
            return redirect('/venda')->with('success', $resultado->message);
        }

        return redirect('/venda')->with('error', $resultado->message);
    }

    public function imprimirCancelamento($id)
    {
        try {
            $venda = Pedido::find($id);
            $xmlRow = $venda->xmlCancelado;
            if (! $xmlRow) {
                return back()->with('error', 'XML de cancelamento não encontrado.');
            }
            $dadosEmitente = Empresa::find($venda->empresa_id);
            $daevento = new Daevento($xmlRow->xml, $dadosEmitente->toArray());
            $daevento->debugMode(true);
            $pdf = $daevento->render();

            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            session()->flash('erro', $e->getMessage());

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
        }
    }

    public function imprimir($id)
    {
        try {
            $venda = Pedido::find($id);
            $xmlRow = $venda->xmlAutorizado;
            if (! $xmlRow) {
                return back()->with('error', 'XML da nota não encontrado.');
            }
            $danfe = new Danfe($xmlRow->xml);
            $danfe->creditsIntegratorFooter('SpeedNFE - www.f-softsistemas.com.br', false);
            $pdf = $danfe->render();

            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
        }
    }

    public function enviarNFe($id)
    {
        $resultado = $this->_enviarNFePeloId(
            $id,
            $this->pedidoServices,
            $this->empresaServices,
            $this->estoqueService,
            $this->fluxoCaixaService
        );

        switch ($resultado->type) {
            case 'success':
                return redirect('/vendas')->with('success', $resultado->message);
            case 'rejeitado_sefaz':
                Alert::warning($resultado->title, $resultado->message)->persistent();

                return redirect('/vendas');
            case 'erro_xml':
                Alert::error($resultado->title, $resultado->message)->persistent();

                return redirect('/vendas');
            case 'estado_invalido':
                return redirect('/vendas')->with('error', $resultado->message);
            case 'validator_exception':
                return back()->with('warning', $resultado->message);
            default:
                return back()->with('error', $resultado->message);
        }
    }

    public function edit($id)
    {
        try {
            $pedido = $this->pedidoServices->buscarPedido($id);

            return view('vendas.edit', ['pedido' => $pedido]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
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
                'info_complementares' => 'nullable',
            ], $this->regrasReferenciasDosItens($usarReferenciaPorItem), $this->itemFiscalService->regrasValidacao()), array_merge($this->mensagensReferenciasDosItens(), $this->itemFiscalService->mensagensValidacao()));

            $this->validarReferenciasDuplicadas($request, $usarReferenciaPorItem);

            if (! $venda->chave) {
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
                        $usarReferenciaPorItem ? $item['dfe_referenciado_n_item'] : null,
                        $item['fiscal'] ?? null
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
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
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
                'ref_nfe' => $isDevolucao && ! $usarReferenciaPorItem ? 'required' : 'nullable',
                'cliente' => 'required|numeric',
                'cfop' => 'required|numeric',
                'vendaItens' => 'required|array|min:1',
                'info_complementares' => 'nullable|max:255',
                'aut_xml' => 'nullable|string|max:18',
            ], $this->regrasReferenciasDosItens($usarReferenciaPorItem), $this->itemFiscalService->regrasValidacao()), array_merge($this->itemFiscalService->mensagensValidacao(), [
                'required' => 'O campo :attribute é obrigatório!',
                'vendaItens.required' => 'Deve existir pelo menos um item no pedido!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
                'max' => 'O campo :attribute deve conter no máximo :max caracteres',
            ], $this->mensagensReferenciasDosItens()));

            $this->validarReferenciasDuplicadas($request, $usarReferenciaPorItem);

            $autXml = preg_replace('/\D/', '', $request->aut_xml ?? '');

            if (! empty($autXml) && ! in_array(strlen($autXml), [11, 14])) {
                return back()
                    ->withInput()
                    ->with('warning', 'CPF/CNPJ autorizado para XML deve ter 11 ou 14 dígitos.');
            }

            DB::beginTransaction();
            $subtotal = 0;
            $desconto = 0;
            // Se ainda não atingiu o limite de Notas ou é janaina que está fazendo, permito a criação de uma nova nota, caso contrário faço o bloqueio da ação
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
                        $usarReferenciaPorItem ? $item['dfe_referenciado_n_item'] : null,
                        $item['fiscal'] ?? null
                    );
                }
                $this->faturaServices->create(
                    $subtotal - $desconto,
                    $pedido->id,
                    $pedido->finNF,
                    $request->empresa
                );
                DB::commit();

                return redirect()->route('vendas.index')->with('success', 'Nota criada com sucesso');
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

            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e->getmessage());
        }
    }

    private function normalizarReferenciasDosItens(Request $request): void
    {
        $itens = $request->input('vendaItens');

        if (! is_array($itens)) {
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
        if (! $usarReferenciaPorItem) {
            return;
        }

        $referencias = [];

        foreach ($request->input('vendaItens', []) as $index => $item) {
            $referencia = ($item['dfe_referenciado_chave'] ?? '').':'.($item['dfe_referenciado_n_item'] ?? '');

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
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
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
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
        }
    }

    public function visualizar($id)
    {
        try {
            $pedido = $this->pedidoServices->buscarPedido($id);
            $empresa = $this->empresaServices->buscarEmpresa($pedido->empresa_id);
            $nfe_service = new NFeService([
                'atualizacao' => date('Y-m-d h:i:s'),
                'tpAmb' => (int) $empresa->ambiente,
                'razaosocial' => $empresa->razao,
                'siglaUF' => $empresa->endereco->uf,
                'cnpj' => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
                'schemes' => 'PL_010_V1.30',
                'versao' => '4.00',
                'tokenIBPT' => 'AAAAAAA',
                'CSC' => $empresa->csc,
                'CSCid' => '00000'.$empresa->idCsc,
            ], $empresa);
            $result = $nfe_service->gerarXml($pedido, $empresa);
            $danfe = new Danfe($result['xml']);
            $pdf = $danfe->render();

            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (ValidatorException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
        }
    }

    public function new()
    {
        try {
            return view('vendas.create');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e);
        }
    }

    public function totalMesNFe()
    {
        try {
            $results = $this->pedidoServices->getTotalNFePerMonth(Auth::user()->empresa_id);

            return response()->json($results);
        } catch (Exception $e) {
            return response()->json('error: Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: '.$e->getMessage(), $e->getCode());
        }
    }
}
