<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyExistException;
use App\Exceptions\MalformedXmlException;
use App\Exceptions\TimeExceededException;
use App\Mail\EmailXmlContador;
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\EstoquesService;
use App\Services\NFCeService;
use App\Utils\FormatationUtil;
use App\Utils\ZipArchiveUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use NFePHP\DA\NFe\Danfce;

class NFCeController extends Controller
{
    private $cupomService;
    private $empresaServices;
    private $estoqueService;

    public function __construct(CupomService $cupomService, EmpresasService $empresaServices, EstoquesService $estoqueService)
    {
        $this->cupomService = $cupomService;
        $this->empresaServices = $empresaServices;
        $this->estoqueService = $estoqueService;
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

    public function show($id)
    {
        try {
            $cupom = $this->cupomService->getCupom($id);
            $dancfe = new Danfce($cupom->nfce->xml);
            $pdf = $dancfe->render();
            return response($pdf)
                ->header('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function sendNFCe($id)
    {
        try {
            DB::beginTransaction();
            $cupom = $this->cupomService->getCupom($id);
            $nfceService = $this->makeNFCeService($cupom->empresa);
            $this->empresaServices->incrementLastNFCe($cupom->empresa_id);
            $resultXml = $nfceService->generateXml($cupom, $cupom->empresa);
            $this->cupomService->updateCoupon($cupom);
            NFCeService::createNFCe($resultXml, $cupom->id, $cupom->empresa);
            foreach ($cupom->itens as $item) {
                $this->estoqueService->out($item->produto_id, $item->qtde);
            }
            DB::commit();
            return redirect()->route('cupom.index')->with('success', 'Cupom foi enviado com sucesso!');
        } catch (MalformedXmlException $e) {
            DB::rollback();
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function cancelNFCe(Request $request)
    {
        try {
            $request->validate([
                'cpnId' => 'required|numeric',
                'justificativa' => 'required|min:15'
            ]);
            DB::beginTransaction();
            $coupon = $this->cupomService->getCupom($request->cpnId);
            $nfceService = $this->makeNFCeService($coupon->empresa);
            $nfceService->cancel($coupon->nfce->chave, $request->justificativa);
            $this->cupomService->cancelCoupon($request->cpnId);
            foreach ($coupon->itens as $item) {
                $this->estoqueService->reverseStock($item->produto_id, $item->qtde);
            }
            DB::commit();
            return redirect()->route('cupom.index')->with('success', 'Cupom foi cancelado com sucesso!');
        } catch (AlreadyExistException $e) {
            DB::rollback();
            return back()->with('warning', $e->getMessage() . ' ' . $coupon->nfce->chave);
        } catch (TimeExceededException $e) {
            DB::rollback();
            return back()->with('warning', $e->getMessage() . ' ' . $coupon->nfce->chave);
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function showUnuser()
    {
        try {
            $user = Auth::user();
            return view('notas.inutilizar', ['empresa' => $user->empresa_id, 'mode' => 'nfce']);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function unuseNFCe(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'empresa_id' => 'required|numeric',
                'numI' => 'required|numeric',
                'numF' => 'required|numeric'
            ]);
            $company = $this->empresaServices->buscarEmpresa($request->empresa_id);
            $nfceService = $this->makeNFCeService($company);
            $nfceService->unuse($company->serie, $request->numI, $request->numF, $request->justificativa);
            DB::commit();
            return redirect()->route('cupom.index')->with('success', 'Faixa de nº foi inutilizada com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (AlreadyExistException $e) {
            DB::rollBack();
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function index()
    {
        try {
            $nfces = NFCeService::getCompanyNFCes(Auth::user()->empresa_id);
            return view('nfce.xmls', ['nfces' => $nfces, 'accountant' => $this->empresaServices->buscarEmpresa(Auth::user()->empresa_id)->contador]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function downloadXmlNFCe($nfceId)
    {
        try {
            $nfce = NFCeService::getNFCe($nfceId);
            header('Content-disposition: attachment; filename="' . $nfce->chave . '.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
            echo $nfce->xml;
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function sendXmlsToAccountant(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|date',
                'accountant' => 'required|email'
            ], [
                'month.required' => 'O campo Mês é obrigatório!',
                'accountant.required' => 'O campo Contador é obrigatório!',
                'month.date' => 'O campo Mês deve ser uma data!',
                'email' => 'O campo Contador deve ser um email!'
            ]);
            $company = Auth::user()->empresa;
            $xmls = NFCeService::getMonthlyCompanyXmls($company->id, $request->month);
            $zipedXmlsPath = ZipArchiveUtil::zip($xmls, $company->id);
            Mail::to($request->accountant)->send(new EmailXmlContador($company, $zipedXmlsPath, $request->month));
            ZipArchiveUtil::deleteArchive($zipedXmlsPath);
            return redirect()->route('nfce.index')->with('success', 'XMLS enviados com sucesso para: ' . $request->accountant);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }

    public function sendLotOfNFCe(Request $request)
    {
        try {
            $request->validate([
                'day' => 'required|date',
            ], [
                'day.required' => 'O campo Dia é obrigatório!',
                'day.date' => 'O campo Dia deve ser uma data!',
            ]);
            DB::beginTransaction();
            $outstandingCoupons = $this->cupomService->getOutstandingCouponsOfTheDay(Auth::user()->empresa_id, $request->day);
            $nfceService = $this->makeNFCeService(Auth::user()->empresa);
            foreach ($outstandingCoupons as $coupon) {
                try {
                    $this->empresaServices->incrementLastNFCe($coupon->empresa_id);
                    $resultXml = $nfceService->generateXml($coupon, $coupon->empresa);
                    $this->cupomService->updateCoupon($coupon);
                    NFCeService::createNFCe($resultXml, $coupon->id, $coupon->empresa);
                } catch (Exception $e) {
                    continue;
                }
            }
            DB::commit();
            return redirect()->route('cupom.index')->with('success', 'Cupoms foram enviados com sucesso!');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e->getMessage());
        }
    }
}
