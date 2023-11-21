<?php

namespace App\Http\Controllers;

use App\Services\EmpresasService;
use App\Services\MotoristaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MotoristaController extends Controller
{

    private MotoristaService $motoristaService;
    private EmpresasService $empresaService;

    public function __construct(MotoristaService $motoristaService, EmpresasService $empresaService)
    {
        $this->motoristaService = $motoristaService;
        $this->empresaService = $empresaService;
    }

    public function index()
    {
        try {
            $motoristas = $this->motoristaService->buscarMotoristas(Auth::user()->empresa_id);
            return view('motoristas.index', ['motoristas' => $motoristas]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $empresas = $this->empresaService->todos(Auth::user()->empresa_id);
            return view('motoristas.create', ['empresas' => $empresas]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

}
