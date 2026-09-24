<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmpresaResource;
use App\Support\Modulos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'E-mail ou senha inválidos.',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $empresa = $user->empresa()->with('endereco')->first();

        return response()->json([
            'data' => [
                'user' => $user,
                'empresa' => $empresa ? new EmpresaResource($empresa) : null,
                'permissoes' => Modulos::doUsuario($user),
            ],
        ]);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user(),
        ]);
    }
}
