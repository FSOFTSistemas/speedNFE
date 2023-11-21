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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|max:255',
                'cpf' => 'required',
                'empresaId' => 'nullable|numeric'
            ]);
            $this->motoristaService->create(
                $request->nome,
                $request->cpf,
                $request->empresaId ? $request->empresaId : Auth::user()->empresa_id
            );
            return redirect()->route('motorista.index')->with('success', 'Motorista cadastrado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function edit($motoristaId)
    {
        try {
            $motorista = $this->motoristaService->buscarMotorista($motoristaId);
            return view('motoristas.edit', ['motorista' => $motorista]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $motoristaId)
    {
        try {
            $request->validate([
                'nome' => 'required|max:255',
                'cpf' => 'required',
            ]);
            $this->motoristaService->update(
                $request->nome,
                $request->cpf,
                $motoristaId
            );
            return redirect()->route('motorista.edit', [$motoristaId])->with('success', 'Motorista atualizado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'motoristaId' => 'required|numeric'
            ]);
            $this->motoristaService->delete($request->motoristaId);
            return redirect()->route('motorista.index')->with('success', 'Motorista deletado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

}
