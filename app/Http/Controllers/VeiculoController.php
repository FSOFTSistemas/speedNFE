<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use App\Services\VeiculosService;
use Exception;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    private VeiculosService $veiculosServices;

    public function __construct(VeiculosService $veiculosService)
    {
        $veiculosServices = $veiculosService;
    }

    public function index()
    {
        try {
            return view('veiculos.index');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('veiculos.create');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'placa' => 'required',
            'capacidade' => '',
            'renavan' => 'required|max:255',
            'tara' => 'required',
            'capacidade_m3' => 'required',
            'tipo_carroceria' => 'required',
            'tipo_veiculo' => 'required',
            'tipo_rodado' => 'required',
            'uf_veiculo' => 'required',
            'tipo_propriedade' => 'required',
        ]);

        $veiculo = $this->veiculosServices->salvar($request->all());

        dd($veiculo);

    }

}
