<?php

namespace App\Http\Controllers;

use App\Exceptions\MalformedXmlException;
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\NFCeService;
use App\Utils\FormatationUtil;
use Exception;
use Illuminate\Support\Facades\DB;
use NFePHP\DA\NFe\Danfce;

class NFCeController extends Controller
{
    private $cupomService;
    private $empresaServices;

    public function __construct(CupomService $cupomService, EmpresasService $empresaServices)
    {
        $this->cupomService = $cupomService;
        $this->empresaServices = $empresaServices;
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
        } catch (MalformedXmlException $e) {
            DB::rollback();
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

    public function unuseNFCe($couponId)
    {
        try {
            DB::beginTransaction();
            $cupom = $this->cupomService->getCupom($couponId);
            $nfceService = $this->makeNFCeService($cupom->empresa);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

}
