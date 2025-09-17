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
            // Usar findOrFail é uma boa prática para retornar um erro 404 se o usuário não for encontrado.
            return User::findOrFail($id);
        } catch (Exception $e) {
            // Retornar a exceção permite que o Controller a trate.
            throw $e;
        }
    }

    /**
     * Atualiza os dados de um usuário.
     * Se o $empresa_id for fornecido (pelo usuário master), também atualiza a empresa.
     */
    public function editar($userId, $name, $cargo, $empresa_id = null)
    {
        $user = $this->buscaId($userId);

        $dataToUpdate = [
            'name' => $name,
            'cargo' => $cargo,
            'tipo' => $tipo,
        ];

        // Adiciona a empresa ao array de atualização apenas se um valor for passado.
        if ($empresa_id !== null) {
            $dataToUpdate['empresa_id'] = $empresa_id;
        }

        return $user->update($dataToUpdate);
    }

    public function destroy($id)
    {
        try {
            $user = $this->buscaId($id);
            return $user->delete();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function store($email, $senha, $cargo, $empresa, $name)
    {
        try {
            return User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($senha),
                'cargo' => $cargo,
                'empresa_id' => $empresa,
                'tipo' => $tipo,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function todos($empresa_id)
    {
        // Inicia a query base que será usada em ambos os casos.
        $query = DB::table('users')
            ->select('users.*', 'empresas.fantasia')
            ->join('empresas', 'empresas.id', '=', 'users.empresa_id');

        // Se um ID de empresa foi fornecido (usuário não-master), adiciona o filtro.
        if ($empresa_id !== null) {
            $query->where('users.empresa_id', $empresa_id);
        }
        
        return $query->get();
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