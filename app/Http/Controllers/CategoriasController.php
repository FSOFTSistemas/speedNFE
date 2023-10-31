<?php

namespace App\Http\Controllers;

use App\Services\CategoriasService;
use App\Services\EmpresasService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriasController extends Controller
{

    private CategoriasService $categoriaServices;
    private EmpresasService $empresaServices;

    public function __construct(CategoriasService $categoriaServices, EmpresasService $empresaServices)
    {
        $this->categoriaServices = $categoriaServices;
        $this->empresaServices = $empresaServices;
    }

    public function destroy($id)
    {
        try {
            $this->categoriaServices->status($id);
            return redirect()->route('categoria.index')->with('success', 'Categoria desativada com sucesso');
        } catch (Exception $e) {
            return back();
        }
    }

    public function show()
    {
        try {
            $user = Auth::user();
            $categorias = null;
            if ($user->empresa_id != 1) {
                $categorias = $this->categoriaServices->todas($user->empresa_id);
            } else {
                $categorias = $this->categoriaServices->todasCategorias();
            }
            return view('categorias.todos', ['categorias' => $categorias, 'empresa' => $user->empresa_id]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function new ()
    {
        try {
            $user = Auth::user();
            $empresas = $this->empresaServices->todas();
            return view('categorias.new', ['empresa' => $user->empresa_id, 'empresas' => $empresas]);
        } catch (Exception $e) {
            return back();
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'descricao' => 'required|max:512',
                'empresa' => 'required',
            ]);
            $this->categoriaServices->store(
                $request->descricao,
                $request->empresa
            );
            return redirect()->route('categoria.index')->with('success', 'Categoria Cadastrada com sucesso');
        } catch (Exception $e) {
            return back();
        }
    }
}
