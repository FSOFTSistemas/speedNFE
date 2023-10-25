<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersService{
    public function __construct()
    {

    }

    public function buscaId($id){
        try{
            return User::findOrFail($id);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function buscarEmpresa($id)
    {
        return Empresa::select('empresas.*', 'users.name', 'users.email')
            ->join('users', 'users.empresa_id', 'empresas.id')
            ->where('empresas.id', $id)
            ->first();
    }

    public function editar($id, $name, $email, $senha, $cargo, $empresa){
        try{
            $user = User::findOrFail($id);
            if($senha){
                $user->update([
                    'name' => $name,
                    'email' => $email,
                    'password' => $senha,
                    'cargo' => $cargo,
                    'empresa_id' => $empresa
                ]);
            } else {
                $user->update([
                    'name' => $name,
                    'email' => $email,
                    'cargo' => $cargo,
                    'empresa_id' => $empresa
                ]);
            }
            return 1;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function destroy($id){
        try{
            $user = User::findOrFail($id);
            return $user->delete();
        } catch (Exception $e) {
            return $e;
        }
    }

    public function store($email, $senha, $cargo, $empresa, $name){
        try{
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($senha),
                'cargo' => $cargo,
                'empresa_id' => $empresa
            ]);
            return 1;
        } catch (Exception $e){
            return $e;
        }
    }

    public function todos($id){
        if ($id == 0 ){
            return DB::table('users')
            ->select('users.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'users.empresa_id')
            ->get();
        } else {
            return DB::table('users')
            ->select('users.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'users.empresa_id')
            ->where('users.empresa_id', '=', $id)
            ->get();
        }
    }

    public function logged($id){
        return User::findOrFail($id);
    }

    public function getEmpresa($id){
        return DB::table('users')
        ->select('empresa_id')
        ->where('id', '=', $id)
        ->first();
    }
}