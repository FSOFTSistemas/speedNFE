<?php

namespace App\Http\Controllers;

use App\Services\EmpresasService;
use App\Services\MotoristaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            $empresa = Auth::user()->empresa_id;
            $motoristas = $this->motoristaService->buscarMotoristas($empresa);
            return view('motoristas.index', ['motoristas' => $motoristas, 'empresa' => $empresa]);
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
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo :attribute deve conter no máximo :max caracteres!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!'
            ]);
            DB::beginTransaction();
            $this->motoristaService->create(
                $request->nome,
                $request->cpf,
                $request->empresaId ? $request->empresaId : Auth::user()->empresa_id
            );
            DB::commit();
            return redirect()->route('motorista.index')->with('success', 'Motorista cadastrado com sucesso!');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors))->withInput();
        } catch (Exception $e) {
            DB::rollBack();
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
