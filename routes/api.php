<?php

use App\Http\Controllers\Api\V1\AuthController as ApiAuthController;
use App\Http\Controllers\Api\V1\ClientesController as ApiClientesController;
use App\Http\Controllers\Api\V1\DashboardController as ApiDashboardController;
use App\Http\Controllers\Api\V1\ProdutosController as ApiProdutosController;
use App\Http\Controllers\PixWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/pix/webhook', [PixWebhookController::class, 'receber']);

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('auth/login', [ApiAuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:api')->group(function () {
        Route::get('auth/me', [ApiAuthController::class, 'me'])->name('auth.me');
        Route::post('auth/refresh', [ApiAuthController::class, 'refresh'])->name('auth.refresh');
        Route::post('auth/logout', [ApiAuthController::class, 'logout'])->name('auth.logout');

        Route::get('dashboard', [ApiDashboardController::class, 'index'])->name('dashboard.index');

        Route::get('clientes/consultar-cnpj/{cnpj}', [ApiClientesController::class, 'consultarCnpj'])
            ->name('clientes.consultar-cnpj');
        Route::get('clientes/verificar-cpf-cnpj', [ApiClientesController::class, 'verificarCpfCnpj'])
            ->name('clientes.verificar-cpf-cnpj');
        Route::apiResource('clientes', ApiClientesController::class);

        Route::apiResource('produtos', ApiProdutosController::class);
    });
});
