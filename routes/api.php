<?php

use App\Http\Controllers\Api\V1\CategoriasController as ApiCategoriasController;
use App\Http\Controllers\Api\V1\ClientesController as ApiClientesController;
use App\Http\Controllers\Api\V1\EmpresasController as ApiEmpresasController;
use App\Http\Controllers\Api\V1\EntradaController as ApiEntradaController;
use App\Http\Controllers\Api\V1\EstoqueController as ApiEstoqueController;
use App\Http\Controllers\Api\V1\FluxoDeCaixaController as ApiFluxoDeCaixaController;
use App\Http\Controllers\Api\V1\FormaPagController as ApiFormaPagController;
use App\Http\Controllers\Api\V1\ItensEntradaController as ApiItensEntradaController;
use App\Http\Controllers\Api\V1\NotasFiscaisController as ApiNotasFiscaisController;
use App\Http\Controllers\Api\V1\PlanoDeContaController as ApiPlanoDeContaController;
use App\Http\Controllers\Api\V1\ProdutosController as ApiProdutosController;
use App\Http\Controllers\Api\V1\ReceberController as ApiReceberController;
use App\Http\Controllers\Api\V1\RelatoriosController as ApiRelatoriosController;
use App\Http\Controllers\Api\V1\UsersController as ApiUsersController;
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
    Route::apiResource('categorias', ApiCategoriasController::class);
    Route::apiResource('clientes', ApiClientesController::class);
    Route::apiResource('empresas', ApiEmpresasController::class);
    Route::apiResource('entradas', ApiEntradaController::class);
    Route::apiResource('estoques', ApiEstoqueController::class);
    Route::apiResource('fluxo-caixa', ApiFluxoDeCaixaController::class);
    Route::apiResource('formas-pagamento', ApiFormaPagController::class);
    Route::apiResource('itens-entradas', ApiItensEntradaController::class);
    Route::apiResource('notas-fiscais', ApiNotasFiscaisController::class);
    Route::apiResource('planos-contas', ApiPlanoDeContaController::class);
    Route::apiResource('produtos', ApiProdutosController::class);
    Route::apiResource('receber', ApiReceberController::class);
    Route::apiResource('relatorios', ApiRelatoriosController::class)->only(['index', 'show']);
    Route::apiResource('usuarios', ApiUsersController::class);
});
