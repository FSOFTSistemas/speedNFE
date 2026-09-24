<?php

use App\Http\Controllers\Api\V1\AuthController as ApiAuthController;
use App\Http\Controllers\Api\V1\CategoriasController as ApiCategoriasController;
use App\Http\Controllers\Api\V1\CfopController as ApiCfopController;
use App\Http\Controllers\Api\V1\ClientesController as ApiClientesController;
use App\Http\Controllers\Api\V1\DashboardController as ApiDashboardController;
use App\Http\Controllers\Api\V1\EntradaController as ApiEntradaController;
use App\Http\Controllers\Api\V1\EstoqueController as ApiEstoqueController;
use App\Http\Controllers\Api\V1\FiscalController as ApiFiscalController;
use App\Http\Controllers\Api\V1\FluxoDeCaixaController as ApiFluxoDeCaixaController;
use App\Http\Controllers\Api\V1\FormaPagController as ApiFormaPagController;
use App\Http\Controllers\Api\V1\FornecedoresController as ApiFornecedoresController;
use App\Http\Controllers\Api\V1\NFCeController as ApiNFCeController;
use App\Http\Controllers\Api\V1\NotasFiscaisController as ApiNotasFiscaisController;
use App\Http\Controllers\Api\V1\PlanoDeContaController as ApiPlanoDeContaController;
use App\Http\Controllers\Api\V1\ProdutosController as ApiProdutosController;
use App\Http\Controllers\Api\V1\ReceberController as ApiReceberController;
use App\Http\Controllers\Api\V1\RelatoriosController as ApiRelatoriosController;
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

        Route::middleware('api.modulo:cadastros')->group(function () {
            Route::get('cfops', [ApiCfopController::class, 'index'])->name('cfops.index');
            Route::get('categorias', [ApiCategoriasController::class, 'index'])->name('categorias.index');
            Route::get('fiscal/cst-ibs-cbs', [ApiFiscalController::class, 'cstIbsCbs'])->name('fiscal.cst-ibs-cbs');
            Route::get('fiscal/cclasstrib', [ApiFiscalController::class, 'cClassTrib'])->name('fiscal.cclasstrib');
            Route::get('fiscal/ncms', [ApiFiscalController::class, 'ncms'])->name('fiscal.ncms');

            Route::get('clientes/consultar-cnpj/{cnpj}', [ApiClientesController::class, 'consultarCnpj'])
                ->name('clientes.consultar-cnpj');
            Route::get('clientes/verificar-cpf-cnpj', [ApiClientesController::class, 'verificarCpfCnpj'])
                ->name('clientes.verificar-cpf-cnpj');
            Route::apiResource('clientes', ApiClientesController::class);

            Route::apiResource('produtos', ApiProdutosController::class);

            Route::apiResource('entradas', ApiEntradaController::class)->only(['index', 'show', 'destroy']);
            Route::apiResource('estoques', ApiEstoqueController::class)->only(['index', 'show', 'update']);
            Route::get('fornecedores', [ApiFornecedoresController::class, 'index'])->name('fornecedores.index');
        });

        Route::get('fluxo-caixa/resumo', [ApiFluxoDeCaixaController::class, 'resumo'])->name('fluxo-caixa.resumo');
        Route::apiResource('fluxo-caixa', ApiFluxoDeCaixaController::class);
        Route::apiResource('formas-pagamento', ApiFormaPagController::class);
        Route::apiResource('plano-contas', ApiPlanoDeContaController::class);
        Route::apiResource('receber', ApiReceberController::class);

        Route::middleware('api.modulo:nfce')->group(function () {
            Route::get('nfce/total-mes', [ApiNFCeController::class, 'totalMes'])->name('nfce.total-mes');
            Route::post('nfce/cupons/{cupom}/enviar', [ApiNFCeController::class, 'enviar'])->name('nfce.enviar');
            Route::get('nfce/{nfce}/pdf', [ApiNFCeController::class, 'pdf'])->name('nfce.pdf');
            Route::get('nfce/{nfce}/xml', [ApiNFCeController::class, 'xml'])->name('nfce.xml');
            Route::apiResource('nfce', ApiNFCeController::class)->only(['index', 'show', 'store']);
        });

        Route::middleware('api.modulo:nfe')->group(function () {
            Route::get('notas-fiscais/total-mes', [ApiNotasFiscaisController::class, 'totalMes'])->name('notas-fiscais.total-mes');
            Route::post('notas-fiscais/inutilizar', [ApiNotasFiscaisController::class, 'inutilizar'])->name('notas-fiscais.inutilizar');
            Route::post('notas-fiscais/{id}/enviar', [ApiNotasFiscaisController::class, 'enviar'])->name('notas-fiscais.enviar');
            Route::post('notas-fiscais/{id}/cancelar', [ApiNotasFiscaisController::class, 'cancelar'])->name('notas-fiscais.cancelar');
            Route::post('notas-fiscais/{id}/carta-correcao', [ApiNotasFiscaisController::class, 'cartaCorrecao'])->name('notas-fiscais.carta-correcao');
            Route::get('notas-fiscais/{id}/pdf', [ApiNotasFiscaisController::class, 'pdf'])->name('notas-fiscais.pdf');
            Route::get('notas-fiscais/{id}/xml', [ApiNotasFiscaisController::class, 'xml'])->name('notas-fiscais.xml');
            Route::get('notas-fiscais/{id}/cancelamento/pdf', [ApiNotasFiscaisController::class, 'cancelamentoPdf'])->name('notas-fiscais.cancelamento-pdf');
            Route::put('notas-fiscais/{id}', [ApiNotasFiscaisController::class, 'update'])->name('notas-fiscais.update');
            Route::delete('notas-fiscais/{id}', [ApiNotasFiscaisController::class, 'destroy'])->name('notas-fiscais.destroy');
            Route::apiResource('notas-fiscais', ApiNotasFiscaisController::class)->only(['index', 'show', 'store']);
        });

        Route::get('relatorios/vendas', [ApiRelatoriosController::class, 'vendas'])->name('relatorios.vendas');
        Route::get('relatorios/nfe', [ApiRelatoriosController::class, 'nfe'])->name('relatorios.nfe');
    });
});
