<?php

namespace App\Http\Controllers;

use App\Enums\TipoCarroceriaEnum;
use App\Enums\TipoPropriedadeEnum;
use App\Enums\TipoProprietarioEnum;
use App\Enums\TipoRodadoEnum;
use App\Enums\TipoTransportadorEnum;
use App\Enums\TipoVeiculoEnum;
use App\Enums\UfEnum;
use App\Services\EmpresasService;
use App\Services\VeiculosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            $tipoProprietarios = TipoProprietarioEnum::cases();
            $tipoTransportadores = TipoTransportadorEnum::cases();
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
                    'tipoProprietarios' => $tipoProprietarios,
                    'tipoTransportadores' => $tipoTransportadores,
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
                'renavan' => 'required',
                'tara' => 'required|numeric',
                'capacidade_m3' => 'required',
                'tipo_carroceria' => ['required', Rule::enum(TipoCarroceriaEnum::class)],
                'tipo_veiculo' => ['required', Rule::enum(TipoVeiculoEnum::class)],
                'tipo_rodado' => ['required', Rule::enum(TipoRodadoEnum::class)],
                'uf_veiculo' => ['required', Rule::enum(UfEnum::class)],
                'tipo_propriedade' => ['required', Rule::enum(TipoPropriedadeEnum::class)],
                'descricao' => 'nullable|max:255',
                'empresaId' => 'required',
                'cpf_cnpj' => 'nullable',
                'ie' => 'nullable',
                'isento' => 'nullable',
                'nome' => 'nullable|max:255',
                'uf_prop' => ['nullable', Rule::enum(UfEnum::class)],
                'rntrc' => 'nullable|min:8|max:8',
                'tipo_proprietario' => ['nullable', Rule::enum(TipoProprietarioEnum::class)],
                'tipo_transportador' => ['nullable', Rule::enum(TipoTransportadorEnum::class)],
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ter um valor numérico!',
                'max' => 'O campo :attribute deve conter no máximo :max',
                'min' => 'O campo :attribute deve conter no mínimo :min'
            ]);
            DB::beginTransaction();
            $veiculo = $this->veiculosServices->salvar($request->all());
            $this->veiculosServices->salvarProprietario(
                $request->cpf_cnpj,
                $request->ie,
                $request->isento,
                $request->nome,
                $request->uf_prop,
                $request->rntrc,
                $request->tipo_proprietario,
                $request->tipo_transportador,
                $veiculo
            );
            DB::commit();
            return redirect()->route('veiculos.index')->with('success', 'Veículo cadastrado com sucesso!');
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

    public function edit($veiculoID)
    {
        try {
            $tiposCarrocerias = TipoCarroceriaEnum::cases();
            $tiposVeiculos = TipoVeiculoEnum::cases();
            $tiposRodados = TipoRodadoEnum::cases();
            $tiposPropriedades = TipoPropriedadeEnum::cases();
            $tipoProprietarios = TipoProprietarioEnum::cases();
            $tipoTransportadores = TipoTransportadorEnum::cases();
            $Ufs = UfEnum::cases();
            $veiculo = $this->veiculosServices->buscarVeiculo($veiculoID);
            $empresas = $this->empresaService->todos(Auth::user()->empresa_id);
            return view('veiculos.edit', [
                'veiculo' => $veiculo,
                'tiposCarrocerias' => $tiposCarrocerias,
                'tiposVeiculos' => $tiposVeiculos,
                'tiposPropriedades' => $tiposPropriedades,
                'tiposRodados' => $tiposRodados,
                'tipoProprietarios' => $tipoProprietarios,
                'tipoTransportadores' => $tipoTransportadores,
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
                'renavan' => 'required',
                'tara' => 'required|numeric',
                'capacidade_m3' => 'required',
                'tipo_carroceria' => ['required', Rule::enum(TipoCarroceriaEnum::class)],
                'tipo_veiculo' => ['required', Rule::enum(TipoVeiculoEnum::class)],
                'tipo_rodado' => ['required', Rule::enum(TipoRodadoEnum::class)],
                'uf_veiculo' => ['required', Rule::enum(UfEnum::class)],
                'tipo_propriedade' => ['required', Rule::enum(TipoPropriedadeEnum::class)],
                'descricao' => 'nullable|max:255',
                'empresaId' => 'required',
                'cpf_cnpj' => 'nullable',
                'ie' => 'nullable',
                'isento' => 'nullable',
                'nome' => 'nullable|max:255',
                'uf_prop' => ['nullable', Rule::enum(UfEnum::class)],
                'rntrc' => 'nullable|min:8|max:8',
                'tipo_proprietario' => ['nullable', Rule::enum(TipoProprietarioEnum::class)],
                'tipo_transportador' => ['nullable', Rule::enum(TipoTransportadorEnum::class)],
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ter um valor numérico!',
                'max' => 'O campo :attribute deve conter no máximo :max',
                'min' => 'O campo :attribute deve conter no mínimo :min'
            ]);
            DB::beginTransaction();
            $proprietario = $this->veiculosServices->update($request->all(), $veiculoID);
            $this->veiculosServices->atualizarProprietario(
                $request->cpf_cnpj,
                $request->ie,
                $request->isento,
                $request->nome,
                $request->uf_prop,
                $request->rntrc,
                $request->tipo_proprietario,
                $request->tipo_transportador,
                $proprietario
            );
            DB::commit();
            return redirect()->route('veiculos.edit', [$veiculoID])->with('success', 'Veículo atualizado com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'veiculoID' => 'required|numeric',
            ]);
            DB::beginTransaction();
            $this->veiculosServices->delete($request->veiculoID);
            DB::commit();
            return redirect()->route('veiculos.index')->with('success', 'Veículo deletado com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }
}
