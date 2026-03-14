<?php

namespace App\Http\Controllers;

use App\Services\EntradaService;

class ItensEntradaController extends Controller
{
    private $entradaService;

    public function __construct(EntradaService $entradaService)
    {
        $this->entradaService = $entradaService;
    }

    public function show($entradaId)
    {
        try {
            $entrada = $this->entradaService->getInput($entradaId);
            return view('nfeEntrada.show', ['entrada' => $entrada]);
        } catch (\Exception $e) {
            return redirect()->route('entradas.index')->with('error', 'Erro interno, tente novamente em outro momento ou entre em contato com nosso suporte!');
        }
    }

}
