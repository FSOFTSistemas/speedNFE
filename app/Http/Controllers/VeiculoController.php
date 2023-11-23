<?php

namespace App\Http\Controllers;

use App\Enum\TipoCarroceriaEnum;
use App\Enum\TipoPropriedadeEnum;
use App\Enum\TipoRodadoEnum;
use App\Enum\TipoVeiculoEnum;
use App\Enum\UfEnum;
use App\Models\Veiculo;
use App\Services\EmpresasService;
use App\Services\VeiculosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VeiculoController extends Controller
{
    private VeiculosService $veiculosServices;
    private EmpresasService $empresaService;

    public function __construct(VeiculosService $veiculosService, EmpresasService $empresaService)
    {
        $this->veiculosServices = $veiculosService;
        $this->empresaService = $empresaService;
    }

    public function index()
    {
        try {
            $veiculos = $this->veiculosServices->buscarVeiculos();
            return view('veiculos.index', ['veiculos' => $veiculos]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $tiposCarrocerias = TipoCarroceriaEnum::cases();
            $tiposVeiculos = TipoVeiculoEnum::cases();
            $tiposRodados = TipoRodadoEnum::cases();
            $tiposPropriedades = TipoPropriedadeEnum::cases();
            $Ufs = UfEnum::cases();
            $empresas = $this->empresaService->todos(Auth::user()->empresa_id);
            return view(
                'veiculos.create',
                [
                    'tiposCarrocerias' => $tiposCarrocerias,
                    'tiposVeiculos' => $tiposVeiculos,
                    'tiposPropriedades' => $tiposPropriedades,
                    'tiposRodados' => $tiposRodados,
                    'ufs' => $Ufs,
                    'empresas' => $empresas,
                ]
            );
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'placa' => 'required',
                'capacidade' => 'nullable|numeric',
                'renavan' => 'required|max:255',
                'tara' => 'required|numeric',
                'capacidade_m3' => 'required|numeric',
                'tipo_carroceria' => 'required',
                'tipo_veiculo' => 'required',
                'tipo_rodado' => 'required',
                'uf_veiculo' => 'required',
                'tipo_propriedade' => 'required',
                'descricao' => 'nullable|max:512',
            ]);
            $this->veiculosServices->salvar($request->all());
            return redirect()->route('veiculos.index')->with('success', 'Veículo cadastrado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function edit($veiculoID)
    {
        try {
            $tiposCarrocerias = TipoCarroceriaEnum::cases();
            $tiposVeiculos = TipoVeiculoEnum::cases();
            $tiposRodados = TipoRodadoEnum::cases();
            $tiposPropriedades = TipoPropriedadeEnum::cases();
            $Ufs = UfEnum::cases();
            $veiculo = $this->veiculosServices->buscarVeiculo( $veiculoID);
            $empresas = $this->empresaService->todos(Auth::user()->empresa_id);
            return view('veiculos.edit', [
                'veiculo' => $veiculo,
                'tiposCarrocerias' => $tiposCarrocerias,
                'tiposVeiculos' => $tiposVeiculos,
                'tiposPropriedades' => $tiposPropriedades,
                'tiposRodados' => $tiposRodados,
                'ufs' => $Ufs,
                'empresas' => $empresas,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $veiculoID)
    {
        try {
            $request->validate([
                'placa' => 'required',
                'capacidade' => 'nullable|numeric',
                'renavan' => 'required|max:255',
                'tara' => 'required|numeric',
                'capacidade_m3' => 'required|numeric',
                'tipo_carroceria' => 'required',
                'tipo_veiculo' => 'required',
                'tipo_rodado' => 'required',
                'uf_veiculo' => 'required',
                'tipo_propriedade' => 'required',
                'descricao' => 'nullable|max:512',
            ]);
            $this->veiculosServices->update($request->all(), $veiculoID);
            return redirect()->route('veiculos.edit', [$veiculoID])->with('success', 'Veículo atualizado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'veiculoID' => 'required|numeric'
            ]);
            $this->veiculosServices->delete($request->veiculoID);
            return redirect()->route('veiculos.index')->with('success', 'Veículo deletado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }
}
