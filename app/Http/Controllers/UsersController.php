<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EmpresasService;
use App\Services\UsersService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    private $userService;
    private $empresaService;

    public function __construct(UsersService $userService, EmpresasService $empresaService)
    {
        $this->userService = $userService;
        $this->empresaService = $empresaService;
    }

    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:255',
                'cargo' => 'required',
                'empresa' => 'required|numeric'
            ], [
                'required' => 'O campo :attribute deve ser obrigatório!',
                'max' => 'O campo :attribute deve conter no máximo :max caracteres!',
                'numeric' => 'O campo ::attribute deve ser um valor numérico!'
            ]);
            $this->userService->editar($id, $request->name, $request->cargo);
            return redirect()->route('index_usuario')->with('success', 'Usuário atualizado com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode("<br>", $error);
            }
            DB::rollBack();
            return back()->with('warning', implode("<br>", $errors))->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function editar($id)
    {
        try {
            $user = $this->userService->buscaId($id);
            $companies = $this->empresaService->todas();
            return view('users.edit', ['user' => $user, 'empresas' => $companies]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'userId' => 'required|numeric'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'numeric' => 'O campo :attribute deve ser um valor numérico!'
            ]);
            DB::beginTransaction();
            $this->userService->destroy($request->userId);
            DB::commit();
            return redirect('/usuarios')->with('success', 'Usuário excluído com sucesso');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode("<br>", $error);
            }
            DB::rollBack();
            return back()->with('warning', implode("<br>", $errors));
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function show()
    {
        try {
            $users = $this->userService->todos(Auth::user()->empresa_id);
            return view('users.index', ['users' => $users, 'logged' => Auth::user()]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $empresas = $this->empresaService->todas();
            return view('users.create', ['empresas' => $empresas]);
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado, tente novamente em outro momento! Erro: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'email|required',
                'senha' => 'required|min:4'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'email' => 'O email deve ser válido!',
                'min' => 'A senha deve conter no mínimo :min caracteres!'
            ]);
            DB::beginTransaction();
            $sUsers = new UsersService();
            $sUsers->store($request->email, $request->senha, $request->cargo, $request->empresa, $request->name);
            DB::commit();
            return redirect()->route('index_usuario')->with('success, Usuário inserido com sucesso !');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode("<br>", $error);
            }
            DB::rollBack();
            return back()->with('warning', implode("<br>", $errors))->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'erro: ' . $e->getMessage());
        }
    }
}
