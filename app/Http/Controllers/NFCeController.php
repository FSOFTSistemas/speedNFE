<?php

namespace App\Http\Controllers;

use App\Services\CupomFormaService;
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\ItemCupomService;
use App\Services\NFCeService;
use App\Utils\CalculateCouponHeight;
use App\Utils\FormatationUtil;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use NFePHP\Common\Exception\ValidatorException;
use NFePHP\DA\NFe\Danfce;

class NFCeController extends Controller
{
    private $cupomService;
    private $cupomFormaService;
    private $itemCupomService;
    private $empresaServices;

    public function __construct(CupomService $cupomService, EmpresasService $empresaServices, CupomFormaService $cupomFormaService, ItemCupomService $itemCupomService)
    {
        $this->cupomService = $cupomService;
        $this->cupomFormaService = $cupomFormaService;
        $this->itemCupomService = $itemCupomService;
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
            return redirect()->route('nfce.create')->with('success','Venda realizada com sucesso!');
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

    public function show($id)
    {
        try {
            $cupom = $this->cupomService->getCupom($id);
            $pdf = Pdf::loadView('nfce.coupon-preview', ['cupom' => $cupom])->setPaper([0, 0, 225, CalculateCouponHeight::calculate(count($cupom->itens), count($cupom->formasPagamento), $cupom->cliente)], 'portrait');
            return $pdf->stream(date('d-m-Y') . '_' . $cupom->nroCupom . '.pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    private function makeNFCeService($empresa)
    {
        $config = [
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int) $empresa->ambiente,
            "razaosocial" => $empresa->razao,
            "siglaUF" => $empresa->endereco->uf,
            "cnpj" => FormatationUtil::retiraPontuacoes($empresa->cpf_cnpj),
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "tokenIBPT" => "AAAAAAA",
            "CSC" => $empresa->csc,
            "CSCid" => "00000" . $empresa->idCsc,
            "proxyConf"   => [
                "proxyIp"   => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];
        return new NFCeService($config, $empresa);
    }

    public function sendNFCe($id){
        try {
            DB::beginTransaction();
            $cupom = $this->cupomService->getCupom($id);
            $nfceService = $this->makeNFCeService($cupom->empresa);
            $this->empresaServices->incrementLastNFCe($cupom->empresa_id);
            $resultXml = $nfceService->generateXml($cupom, $cupom->empresa);
            $this->cupomService->updateCoupon($cupom);
            NFCeService::createNFCe($resultXml, $cupom->id, $cupom->empresa);
            DB::commit();
            return redirect()->route('nfce.index')->with('success', 'Cupom foi enviado com sucesso!');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

}
