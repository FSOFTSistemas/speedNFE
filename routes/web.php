<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\FormaPagController;
use App\Http\Controllers\EmpresasController;
use App\Http\Controllers\ReceberController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\NotasFiscaisController;
use App\Http\Controllers\RelatoriosController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');

Route::get('/',  function() {
    return view('home');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//CATEGORIA
Route::get('/categoria', [CategoriasController::class, 'show'])->name('categoria.index')->middleware('auth');
Route::get('/categoria/cadastro', [CategoriasController::class, 'new'])->name('cadastrar_categoria')->middleware('auth');
Route::post('/categoria/cadastro', [CategoriasController::class, 'store'])->name('salvar_categoria')->middleware('auth');
Route::get('/categoria/status/{id}', [CategoriasController::class, 'destroy'])->name('desativarReativar_categoria')->middleware('auth');

//EMPRESA
Route::get('/empresa', [EmpresasController::class, 'show'])->name('empresa.index')->middleware('auth');
Route::get('/empresa/ver/{id}', [EmpresasController::class, 'view'])->name('empresa.view')->middleware('auth');
Route::get('/empresa/status/{id}', [EmpresasController::class, 'desativarReativar'])->name('desativarReativar_empresa')->middleware('auth');
Route::get('/empresa/cadastro', [EmpresasController::class, 'cadastrar'])->middleware('auth');
Route::get('/empresa/editar/{id}', [EmpresasController::class, 'editar'])->name('editar_empresa')->middleware('auth');
Route::post('/empresa/editar/{id}', [EmpresasController::class, 'update'])->name('update_empresa')->middleware('auth');
Route::post('/empresa', [EmpresasController::class, 'store'])->name('salvar_empresa')->middleware('auth');

//CLIENTE
Route::get('/cliente', [ClientesController::class, 'show'])->name('index')->middleware('auth');
Route::get('/cliente/cadastro', [ClientesController::class, 'new'])->middleware('auth');
Route::post('/cliente/cadastro', [ClientesController::class, 'salvar'])->name('criar_cliente')->middleware('auth');
Route::get('/cliente/ver/{id}', [ClientesController::class, 'view'])->name('cliente.view')->middleware('auth');
Route::get('/cliente/edit/{id}', [ClientesController::class, 'editar'])->name('editar_cliente')->middleware('auth');
Route::put('/cliente/salvar/{id}', [ClientesController::class, 'update'])->name('salvar_cliente')->middleware('auth');
Route::delete('/cliente/del', [ClientesController::class, 'excluir'])->name('excluir_cliente')->middleware('auth');
Route::post('/clientes/cnpj/', [ClientesController::class, 'BuscarCnpj'])->name('cnpj.clientes');

//FORMA DE PAGAMENTO
Route::get('/forma', [FormaPagController::class, 'show'])->middleware('auth');
Route::get('/forma/cadastro', [FormaPagController::class, 'new'])->middleware('auth');
Route::post('/forma/cadastro', [FormaPagController::class, 'store'])->name('salvar_forma')->middleware('auth');
Route::get('/forma/del/{id}', [FormaPagController::class, 'excluir'])->name('excluir_forma')->middleware('auth');

//USUARIO
Route::get('/usuarios', [UsersController::class, 'show'])->middleware('auth');
Route::get('/usuarios/cadastro', [UsersController::class, 'new'])->name('cadastrar_usuario')->middleware('auth');
Route::post('/usuarios/cadastro', [UsersController::class, 'store'])->name('salvar_usuario')->middleware('auth');
Route::get('/usuarios/del/{id}', [UsersController::class, 'destroy'])->name('excluir_usuario')->middleware('auth');
Route::get('/usuarios/editar/{id}', [UsersController::class, 'editar'])->name('editar_usuario')->middleware('auth');
Route::post('/usuarios/editar/{id}', [UsersController::class, 'update'])->name('update_usuario')->middleware('auth');

//PRODUTOS
Route::get('/produto', [ProdutosController::class, 'show'])->name('produto.index')->middleware('auth');
Route::get('/produto/cadastro', [ProdutosController::class, 'new'])->middleware('auth');
Route::post('/produto/cadastro', [ProdutosController::class, 'store'])->name('salvar_produto')->middleware('auth');
Route::get('/produto/ver/{id}', [ProdutosController::class, 'view'])->name('ver_produto')->middleware('auth');
Route::delete('/produto/del', [ProdutosController::class, 'destroy'])->name('excluir_produto')->middleware('auth');
Route::get('/produto/editar/{id}', [ProdutosController::class, 'editar'])->name('editar_produto')->middleware('auth');
Route::put('/produto/editar/{id}', [ProdutosController::class, 'update'])->name('update_produto')->middleware('auth');

//ESTOQUE
Route::get('/estoque', [EstoqueController::class, 'show'])->middleware('auth');
Route::get('/estoque/cadastro', [EstoqueController::class, 'new'])->middleware('auth');
Route::post('/estoque/cadastro', [EstoqueController::class, 'store'])->name('salvar_estoque')->middleware('auth');
Route::get('/estoque/del/{id}', [EstoqueController::class, 'destroy'])->name('excluir_estoque')->middleware('auth');
Route::get('/estoque/edit/{id}', [EstoqueController::class, 'editar'])->name('editar_estoque')->middleware('auth');
Route::post('/estoque/edit/{id}', [EstoqueController::class, 'update'])->name('update_estoque')->middleware('auth');

//RECEBER
Route::get('/receber', [ReceberController::class, 'show'])->middleware('auth');
Route::get('/receber/cadastro', [ReceberController::class, 'new'])->middleware('auth');
Route::post('/receber/cadastro', [ReceberController::class, 'store'])->name('salvar_recebimento')->middleware('auth');
Route::get('/receber/del/{id}', [ReceberController::class, 'destroy'])->name('excluir_recebimento')->middleware('auth');
Route::get('/receber/edit/{id}', [ReceberController::class, 'editar'])->name('editar_recebimento')->middleware('auth');
Route::post('/receber/edit/{id}', [ReceberController::class, 'update'])->name('update_recebimento')->middleware('auth');

//VENDAS
Route::get('/venda', [PedidosController::class, 'todos'])->middleware('auth');
Route::post('/venda', [PedidosController::class, 'cancelarNFe'])->name('cancelar')->middleware('auth');
Route::get('/vendas', [PedidosController::class, 'todos'])->name('vendas.index')->middleware('auth');
Route::post('/vendas', [PedidosController::class, 'cancelarNFe'])->name('cancelar')->middleware('auth');
Route::get('/visualizar/{pedido}', [PedidosController::class, 'visualizar'])->name('visu')->middleware('auth');
Route::post('/visualizar/{pedido}', [PedidosController::class, 'update'])->middleware('auth');
Route::get('/vendas/nova', [PedidosController::class, 'new'])->middleware('auth');
Route::post('/vendas/nova', [PedidosController::class, 'store'])->name('salvar_venda')->middleware('auth');
Route::get('/venda/envio/{id}', [PedidosController::class, 'enviarNFe'])->name('enviarXML')->middleware('auth');
Route::get('/venda/imprimir/{id}', [PedidosController::class, 'imprimir'])->name('imprimirXML')->middleware('auth');
Route::get('/venda/imprimirCancelamento/{id}', [PedidosController::class, 'imprimirCancelamento'])->name('imprimirCancelamentoXML')->middleware('auth');
Route::post('/venda/cce', [PedidosController::class, 'cartaCorrecao'])->name('cartaCorrecao')->middleware('auth');
Route::get('/venda/cce/{id}', [PedidosController::class, 'imprimirCorrecao'])->middleware('auth');

//NOTAS FISCAIS
Route::get('/notas', [NotasFiscaisController::class, 'show'])->middleware('auth');
Route::get('/notas/{chave}', [NotasFiscaisController::class, 'visualizarPdf'])->middleware('auth');
Route::get('/notas/xml/{chave}', [NotasFiscaisController::class, 'downloadXml'])->middleware('auth');
Route::post('/zip', [NotasFiscaisController::class, 'zip'])->middleware('auth');

//INUTILIZAR
Route::get('/inutilizar', [PedidosController::class, 'inutil'])->middleware('auth');
Route::post('/inutilizar', [PedidosController::class, 'inutilizar'])->middleware('auth');

//RELATORIOS
Route::get('/relatorios', [RelatoriosController::class, 'show'])->middleware('auth');
Route::post('/relatorios', [RelatoriosController::class, 'relatorio'])->name('relatorio')->middleware('auth');

require __DIR__.'/auth.php';
