<?php

namespace App\Http\Controllers;

use App\Services\CupomFormaService;
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\ItemCupomService;
use App\Utils\CalculateCouponHeight;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Traits\EnviaNFCe;
use App\Services\EstoquesService;

class CupomController extends Controller
{

    private $cupomService;
    private $itemCupomService;
    private $cupomFormaService;
    private $empresaServices;
    private $estoqueService;
    use EnviaNFCe;

    public function __construct(CupomService $cupomService, ItemCupomService $itemCupomService, CupomFormaService $cupomFormaService, EmpresasService $empresaServices, EstoquesService $estoqueService)
    {
        $this->cupomService = $cupomService;
        $this->itemCupomService = $itemCupomService;
        $this->cupomFormaService = $cupomFormaService;
        $this->empresaServices = $empresaServices;
        $this->estoqueService = $estoqueService;
    }

    public function index()
    {
        try {
            $cupoms = $this->cupomService->getCompanyCoupons(Auth::user()->empresa_id);
            return view('nfce.index', ['cupoms' => $cupoms]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('nfce.create');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

        public function store(Request $request)
    {
        try {
            // Validação dos dados (mantida como estava)
            $request->validate([
                'cliente' => 'nullable|array',
                'itens' => 'required|array',
                'formas' => 'required|array',
                'valorTotal' => 'required|numeric',
                'subtotal' => 'required|numeric',
                'descontoTotal' => 'required|numeric',
                'acrescimoTotal' => 'required|numeric',
                'troco' => 'nullable|numeric',
                'aReceber' => 'nullable|numeric'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser numérico!',
                'array' => 'O campo :attribute deve ser uma lista!'
            ]);

            // Transação para salvar a venda (mantida como estava)
            DB::beginTransaction();
            $cupomId = $this->cupomService->createCupom($this->empresaServices->incrementCupomSequence(Auth::user()->empresa_id), $request->valorTotal, $request->descontoTotal, $request->acrescimoTotal, $request->subtotal, $request->troco, $request->cliente['id'], Auth::user()->empresa_id);
            $this->itemCupomService->createItemsCupom($request->itens, $cupomId);
            $this->cupomFormaService->createCupomFormas($request->formas, $cupomId);
            DB::commit();

            // --- INÍCIO DA NOVA LÓGICA ---

            $mensagemSucesso = 'Venda realizada com sucesso!';

            // Verifica a escolha do usuário vinda do formulário
            if ($request->input('acao_pos_salvar') === 'agora') {
                
                // Chama a lógica de envio que está no Trait, passando o ID da venda recém-criada
                $resultadoEmissao = $this->_enviarNFCePeloId(
                    $cupomId,
                    $this->cupomService,
                    $this->empresaServices,
                    $this->estoqueService
                );

                // Verifica o resultado retornado pelo Trait
                if ($resultadoEmissao->status === 'success') {
                    // Se deu certo, anexa a mensagem de sucesso da emissão
                    $mensagemSucesso .= ' NFC-e emitida!';
                } else {
                    // Se a emissão falhou, redireciona de volta para o PDV com a mensagem de erro específica
                    return redirect()->route('cupom.create')
                                     ->with($resultadoEmissao->status, 'Venda salva, mas falha ao emitir: ' . $resultadoEmissao->message);
                }
            }

            // Redireciona para a tela do PDV com a mensagem final de sucesso
            return redirect()->route('cupom.create')->with('success', $mensagemSucesso);

        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function destroyCoupon(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->cupomService->cancelCoupon($request->couponId);
            DB::commit();
            return redirect()->route('cupom.index')->with('success','Venda cancelada com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function showPreView($id)
    {
        try {
            $cupom = $this->cupomService->getCupom($id);
            $pdf = Pdf::loadView('nfce.coupon-preview', ['cupom' => $cupom])->setPaper([0, 0, 225, CalculateCouponHeight::calculate(count($cupom->itens), count($cupom->formasPagamento), $cupom->cliente)], 'portrait');
            return $pdf->stream(date('d-m-Y') . '_' . $cupom->nroCupom . '.pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

}
