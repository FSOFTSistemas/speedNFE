<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EmpresasService;
use App\Services\UsersService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

    public function show(Request $request)
    {
        $status = $request->query('status', 'ativo');
        $empresa_id_filter = Auth::user()->cargo == 'master' ? null : Auth::user()->empresa_id;
        $users = $this->userService->todos($empresa_id_filter, $status);
        return view('users.index', ['users' => $users, 'logged' => Auth::user(), 'status' => $status]);
    }

    public function create()
    {
        $empresas = (Auth::user()->cargo == 'master') ? $this->empresaService->todas() : [];
        return view('users.create', ['empresas' => $empresas]);
    }


// MÉTODO PARA SALVAR NOVO USUÁRIO
public function store(Request $request)
{

    try {
        $rules = [
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'senha' => 'required|min:4',
            'tipo' => 'required|in:admin,usuario',
        ];

        // Regras de validação que se aplicam apenas ao usuário 'master'
        if (Auth::user()->cargo == 'master') {
            $rules['cargo'] = 'required';
            $rules['empresa'] = 'required';
        }

        // Executa a validação com as mensagens de erro
        $request->validate($rules, [
            'required' => 'O campo :attribute é obrigatório!',
            'email' => 'O email deve ser válido!',
            'unique' => 'Este e-mail já está cadastrado.',
            'min' => 'A senha deve conter no mínimo :min caracteres!',
            'in' => 'O valor para o campo :attribute é inválido.'
        ]);

        // **PONTO CRÍTICO:** Define a variável $tipo a partir dos dados validados da requisição.
        $tipo = $request->tipo;
        
        $empresa_id = null;
        $cargo = null;

        // Define os valores de 'cargo' e 'empresa' com base na permissão do usuário logado
        if (Auth::user()->cargo == 'master') {
            $empresa_id = $request->empresa;
            $cargo = $request->cargo;
        } else {
            // Para não-masters, os valores são fixos para segurança
            $empresa_id = Auth::user()->empresa_id;
            $cargo = Auth::user()->cargo;
        }

        DB::beginTransaction();
        
        // Passa todas as variáveis definidas para o service
        $this->userService->store($request->email, $request->senha, $cargo, $empresa_id, $request->name, $tipo);
        
        DB::commit();
        return redirect()->route('index_usuario')->with('success', 'Usuário inserido com sucesso!');

    } catch (ValidationException $e) {
        dd($e->getMessage());
        // Bloco para tratar erros de validação
        return back()->withErrors($e->errors())->withInput();
    } catch (Exception $e) {
        dd($e->getMessage());
        // Bloco para tratar outros erros inesperados
        DB::rollBack();
        return back()->with('error', 'Erro: ' . $e->getMessage());
    }
}
    public function editar($id)
    {
        $user = $this->userService->buscaId($id);
        if (Auth::user()->cargo != 'master' && $user->empresa_id != Auth::user()->empresa_id) {
            abort(403, 'Acesso não autorizado');
        }
        $empresas = (Auth::user()->cargo == 'master') ? $this->empresaService->todas() : [];
        return view('users.edit', ['user' => $user, 'empresas' => $empresas]);
    }

    // MÉTODO PARA ATUALIZAR UM USUÁRIO
public function update($id, Request $request)
{
    try {
        $userToUpdate = $this->userService->buscaId($id);
        if (Auth::user()->cargo != 'master' && $userToUpdate->empresa_id != Auth::user()->empresa_id) {
            abort(403, 'Acesso não autorizado');
        }

        $rules = [
            'name' => 'required|max:255',
            'tipo' => 'required|in:admin,usuario',
            'senha' => ['nullable', 'min:4', 'confirmed'],
        ];

        if (Auth::user()->cargo == 'master') {
            $rules['cargo'] = 'required';
        }
        $request->validate($rules);

        // Garante que a variável $tipo seja definida a partir do request
        $tipo = $request->tipo;
        $name = $request->name;
        $senha = $request->senha;

        if (Auth::user()->cargo == 'master') {
            $cargo = $request->cargo;
            $empresa_id = $request->empresa;
            $this->userService->editar($id, $name, $cargo, $empresa_id, $tipo, $senha);
        } else {
            // Se não for master, o cargo não muda. Usamos o que já existe no banco.
            $cargo = $userToUpdate->cargo;
            $this->userService->editar($id, $name, $cargo, null, $tipo,  $senha);
        }

        return redirect()->route('index_usuario')->with('success', 'Usuário atualizado com sucesso');
    } catch (Exception $e) {
        return back()->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
    }
}

   public function inativar(Request $request)
    {
        try {
            $request->validate(['userId' => 'required|numeric']);
            $userId = $request->userId;
            $userToInactivate = $this->userService->buscaId($userId);

            if (Auth::user()->cargo != 'master' && $userToInactivate->empresa_id != Auth::user()->empresa_id) {
                abort(403, 'Acesso não autorizado');
            }

            $this->userService->inativar($userId);
            
            return redirect()->route('index_usuario')->with('success', 'Usuário inativado com sucesso');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }

    public function ativar($id)
    {
        try {
            $userToActivate = $this->userService->buscaId($id);

            if (Auth::user()->cargo != 'master' && $userToActivate->empresa_id != Auth::user()->empresa_id) {
                abort(403, 'Acesso não autorizado');
            }
            
            $this->userService->ativar($id);

            // Redireciona de volta para a lista de inativos
            return redirect()->route('index_usuario', ['status' => 'inativo'])->with('success', 'Usuário reativado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }
}