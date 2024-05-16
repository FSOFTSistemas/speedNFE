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
    public function update($id, Request $request)
    {
        $sUsers = new UsersService();
        $resp = $sUsers->editar($id, $request->name, $request->cargo);

        if ($resp == 1) {
            return redirect('/usuarios')->with('success', 'Usuário atualizado com sucesso');
        }
        return $resp;
    }

    public function editar($id)
    {
        $sUsers = new UsersService();
        $user = $sUsers->buscaId($id);
        $empresa = $sUsers->getEmpresa(Auth::id());

        $sEmpresas = new EmpresasService();
        $empresas = $sEmpresas->todas();

        return view('users.editar', ['user' => $user, 'empresas' => $empresas, 'empresa' => $empresa->empresa_id]);
    }

    public function destroy(Request $request)
    {
        $sUsers = new UsersService();
        $resp = $sUsers->destroy($request->userId);

        if ($resp == 1) {
            return redirect('/usuarios')->with('success', 'Usuário excluído com sucesso');
        }

        return redirect('/usuarios')->with('error', 'Não foi possível excluir o usuário');
    }

    public function show()
    {
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());
        return view('users.todos', ['users' => $sUsers->todos($empresa->empresa_id), 'logged' => $sUsers->logged(Auth::id())]);
    }

    public function new ()
    {
        $sUsers = new UsersService();
        $empresa = $sUsers->getEmpresa(Auth::id());
        $sEmpresas = new EmpresasService();
        $empresas = $sEmpresas->todas();
        return view('users.new', ['empresa' => $empresa->empresa_id, 'empresas' => $empresas]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'email|required',
                'senha' => 'required'
            ], [
                'required' => 'O campo :attribute é obrigatório!',
                'email' => 'O email deve ser válido!'
            ]);
            DB::beginTransaction();
            $sUsers = new UsersService();
            $sUsers->store($request->email, $request->senha, $request->cargo, $request->empresa, $request->name);
            DB::commit();
            return redirect()->route('index_usuario')->with('Success, Usuário inserido com sucesso !');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $error) {
                $errors[] = implode(PHP_EOL, $error);
            }
            DB::rollBack();
            return back()->with('warning', implode(PHP_EOL, $errors))->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('erro: ' . $e->getMessage());
        }
    }

}
