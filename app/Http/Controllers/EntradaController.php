<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Services\EmpresasService;
use App\Services\ImportProductsService;
use App\Utils\FormatationUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntradaController extends Controller
{
    private $empresaServices;

    public function __construct(EmpresasService $empresaService)
    {
        $this->empresaServices = $empresaService;
    }

    public function index()
    {
        try {
            $entradas = [];
            return view('nfeEntrada.entradas', ['entradas' => $entradas]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro interno, tente novamente em outro momento ou entre em contato com nosso suporte!');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Entrada  $entrada
     * @return \Illuminate\Http\Response
     */
    public function show(Entrada $entrada)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Entrada  $entrada
     * @return \Illuminate\Http\Response
     */
    public function edit(Entrada $entrada)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Entrada  $entrada
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Entrada $entrada)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Entrada  $entrada
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entrada $entrada)
    {
        //
    }

    public function importProducts (Request $request)
    {
        try {
            $emitente = $this->empresaServices->buscarEmpresa(Auth::user()->empresa_id);
            $importService = new ImportProductsService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int) $emitente->ambiente,
                "razaosocial" => $emitente->razao,
                "siglaUF" => $emitente->endereco->uf,
                "cnpj" => FormatationUtil::retiraPontuacoes($emitente->cpf_cnpj),
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
                "tokenIBPT" => "AAAAAAA",
                "CSC" => $emitente->csc,
                "CSCid" => '00000' . $emitente->idCsc,
            ], $emitente);
            $result = $importService->importProducts($request->chaveNota);
            dd($result);
            // return redirect()->route('');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente em outro momento!, Erro: ' . $e);
        }
    }
}
