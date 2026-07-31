<?php

namespace App\Http\Controllers;

use App\Services\ServicosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ServicosController extends Controller
{
    private ServicosService $servicosService;

    public function __construct(ServicosService $servicosService)
    {
        $this->servicosService = $servicosService;
    }

    private function rules(): array
    {
        return [
            'codigo' => 'required',
            'descricao' => 'required',
            'cClass' => 'required',
            'uMed' => 'required',
            'valor' => 'required|numeric',
            'cfop' => 'nullable',
            'icms_cst' => 'nullable',
            'icms_pICMS' => 'nullable|numeric',
            'icms_pFCP' => 'nullable|numeric',
            'icms_orig' => 'nullable',
            'icms_csosn' => 'nullable',
            'pis_cst' => 'nullable',
            'pis_pPIS' => 'nullable|numeric',
            'cofins_cst' => 'nullable',
            'cofins_pCOFINS' => 'nullable|numeric',
            'fust_pFUST' => 'nullable|numeric',
            'funttel_pFUNTTEL' => 'nullable|numeric',
        ];
    }

    public function index()
    {
        try {
            $servicos = $this->servicosService->todos(Auth::user()->empresa_id);

            return view('servicos.index', ['servicos' => $servicos]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate($this->rules(), [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
            ]);
            $data = $request->only(array_keys($this->rules()));
            $data['empresa_id'] = Auth::user()->empresa_id;
            $this->servicosService->store($data);

            return redirect()->route('servicos.index')->with('success', 'Serviço cadastrado com sucesso!');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }

            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate($this->rules(), [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!',
            ]);
            $data = $request->only(array_keys($this->rules()));
            $this->servicosService->update($id, $data);

            return redirect()->route('servicos.index')->with('success', 'Serviço atualizado com sucesso!');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }

            return back()->with('warning', implode(PHP_EOL, $errors));
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->validate(['servico_id' => 'required|numeric']);
            $this->servicosService->destroy($request->servico_id);

            return redirect()->route('servicos.index')->with('success', 'Serviço excluído com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: '.$e->getMessage());
        }
    }
}
