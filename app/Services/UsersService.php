<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    public function __construct()
    {
    }

    public function buscaId($id)
    {
        try {
            return User::findOrFail($id);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function editar($userId, $name, $cargo)
    {
        $user = User::find($userId);
        return $user->update([
            'name' => $name,
            'cargo' => $cargo,
        ]);
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            return $user->delete();
        } catch (Exception $e) {
            return 0;
        }
    }

    public function store($email, $senha, $cargo, $empresa, $name)
    {
        try {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($senha),
                'cargo' => $cargo,
                'empresa_id' => $empresa,
            ]);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function todos($id)
    {
        if ($id == 1) {
            $users = DB::table('users')
                ->select('users.*', 'empresas.fantasia')
                ->join('empresas', 'empresas.id', '=', 'users.empresa_id')
                ->where('users.empresa_id', 'like', '%')
                ->get();
        } else {
            $users = DB::table('users')
                ->select('users.*', 'empresas.fantasia')
                ->join('empresas', 'empresas.id', '=', 'users.empresa_id')
                ->where('users.empresa_id', $id)
                ->get();
        }
        return $users;
    }

    public function logged($id)
    {
        return User::findOrFail($id);
    }

    public function getEmpresa($id)
    {
        return DB::table('users')
            ->select('empresa_id')
            ->where('id', '=', $id)
            ->first();
    }
}
