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

class CupomController extends Controller
{

    private $cupomService;
    private $itemCupomService;
    private $cupomFormaService;
    private $empresaServices;

    public function __construct(CupomService $cupomService, ItemCupomService $itemCupomService, CupomFormaService $cupomFormaService, EmpresasService $empresaServices)
    {
        $this->cupomService = $cupomService;
        $this->itemCupomService = $itemCupomService;
        $this->cupomFormaService = $cupomFormaService;
        $this->empresaServices = $empresaServices;
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
                'array' => 'O campo :attribiute deve ser uma lista!'
            ]);
            DB::beginTransaction();
            $cupomId = $this->cupomService->createCupom($this->empresaServices->incrementCupomSequence(Auth::user()->empresa_id), $request->valorTotal, $request->descontoTotal, $request->acrescimoTotal, $request->subtotal, $request->troco, $request->cliente['id'], Auth::user()->empresa_id);
            $this->itemCupomService->createItemsCupom($request->itens, $cupomId);
            $this->cupomFormaService->createCupomFormas($request->formas, $cupomId);
            DB::commit();
            return redirect()->route('cupom.create')->with('success','Venda realizada com sucesso!');
        } catch (ValidationException $e) {
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
