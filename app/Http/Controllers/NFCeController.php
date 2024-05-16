<?php

namespace App\Http\Controllers;

use App\Services\CupomFormaService;
use App\Services\CupomService;
use App\Services\EmpresasService;
use App\Services\ItemCupomService;
use App\Services\NFCeService;
use App\Utils\FormatationUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NFePHP\Common\Exception\ValidatorException;

class NFCeController extends Controller
{
    private $cupomService;
    private $cupomFormaService;
    private $itemCupomService;
    private EmpresasService $empresaServices;

    public function __construct(CupomService $cupomService, EmpresasService $empresaServices, CupomFormaService $cupomFormaService, ItemCupomService $itemCupomService)
    {
        $this->cupomService = $cupomService;
        $this->cupomFormaService = $cupomFormaService;
        $this->itemCupomService = $itemCupomService;
    }

    public function index()
    {
        try {
            $nfces = NFCeService::getCompanyNFCes(Auth::user()->empresa_id);
            return view('nfce.index', ['nfces' => $nfces]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
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
        ];

        return new NFCeService($config, $empresa);
    }

    public function enviarNFCE($id){

        try {

            $venda = $this->cupomService->buscarCupom($id);
            $empresa = $this->empresaServices->buscarEmpresa($venda->empresa_id);
            $nfce_service = dd($this->makeNFCeService($empresa));

            if ($venda->estado->value == 'Rejeitado' || $venda->estado->value == 'Pendente') {
                $result = $nfce_service->gerarXml($venda, $empresa);
                // dd($result);
                if (!isset($result['erros_xml'])) {
                    $signed = $nfce_service->sign($result['xml']);
                    // dd($signed);
                    $resultado = $nfce_service->transmitir($signed, $result['chave'], $empresa->fantasia . '/' . date('Y') . '/' . date('m') . '/notas/Autorizadas');
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
                        return redirect('/vendas')->with('warning', $resultado['erro']);
                    }
                } else {
                    return redirect('/vendas')->with('error', $result['erros_xml']);
                }
            } else {
                return redirect('/vendas')->with("error", 404);
            }
        } catch (ValidatorException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em alguns instantes!, Erro: ' . $e);
        }
    }

}
