<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PlanoController;
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
use App\Http\Controllers\MDFEController;
use App\Http\Controllers\MDFeNotaController;
use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\NotasFiscaisController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\VeiculoController;

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



//Home
Route::get('/', function(){

    return view('homePage');
});


Route::get('/homePage', [HomeController::class, 'homePage'])->name('homePage');
Route::get('/homePage2', [HomeController::class, 'homePage2'])->name('homePage2');


//Planos
Route::get('/planos', [PlanoController::class, 'Planos'])->name('Planos');


Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//CATEGORIA
Route::prefix('categoria')->group(function () {
    Route::get('', [CategoriasController::class, 'show'])->name('categoria.index')->middleware('auth');
    Route::get('/cadastro', [CategoriasController::class, 'new'])->name('cadastrar_categoria')->middleware('auth');
    Route::post('/cadastro', [CategoriasController::class, 'store'])->name('salvar_categoria')->middleware('auth');
    Route::get('/status/{id}', [CategoriasController::class, 'destroy'])->name('desativarReativar_categoria')->middleware('auth');
});

//EMPRESA
Route::prefix('empresa')->group(function () {
    Route::get('', [EmpresasController::class, 'show'])->name('empresa.index')->middleware('auth');
    Route::get('/ver/{id}', [EmpresasController::class, 'view'])->name('empresa.view')->middleware('auth');
    Route::get('/status/{id}', [EmpresasController::class, 'desativarReativar'])->name('desativarReativar_empresa')->middleware('auth');
    Route::get('/cadastro', [EmpresasController::class, 'cadastrar'])->middleware('auth');
    Route::get('/editar/{id}', [EmpresasController::class, 'editar'])->name('editar_empresa')->middleware('auth');
    Route::post('/editar/{id}', [EmpresasController::class, 'update'])->name('update_empresa')->middleware('auth');
    Route::post('', [EmpresasController::class, 'store'])->name('salvar_empresa')->middleware('auth');
    Route::get('/{uf}/atualizar-cidades', [EmpresasController::class, 'updateCities'])->name('updateCities')->middleware('auth');
});

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
Route::prefix('forma')->group(function () {
    Route::get('', [FormaPagController::class, 'show'])->middleware('auth');
    Route::get('/cadastro', [FormaPagController::class, 'new'])->middleware('auth');
    Route::post('/cadastro', [FormaPagController::class, 'store'])->name('salvar_forma')->middleware('auth');
    Route::get('/del/{id}', [FormaPagController::class, 'excluir'])->name('excluir_forma')->middleware('auth');
});

//USUARIO
Route::prefix('usuarios')->group(function () {
    Route::get('', [UsersController::class, 'show'])->middleware('auth');
    Route::get('/cadastro', [UsersController::class, 'new'])->name('cadastrar_usuario')->middleware('auth');
    Route::post('/cadastro', [UsersController::class, 'store'])->name('salvar_usuario')->middleware('auth');
    Route::get('/del/{id}', [UsersController::class, 'destroy'])->name('excluir_usuario')->middleware('auth');
    Route::get('/editar/{id}', [UsersController::class, 'editar'])->name('editar_usuario')->middleware('auth');
    Route::post('/editar/{id}', [UsersController::class, 'update'])->name('update_usuario')->middleware('auth');
});

//PRODUTOS
Route::prefix('produto')->group(function () {
    Route::get('', [ProdutosController::class, 'show'])->name('produto.index')->middleware('auth');
    Route::get('/cadastro', [ProdutosController::class, 'new'])->middleware('auth');
    Route::post('/cadastro', [ProdutosController::class, 'store'])->name('salvar_produto')->middleware('auth');
    Route::get('/ver/{id}', [ProdutosController::class, 'view'])->name('ver_produto')->middleware('auth');
    Route::delete('/del', [ProdutosController::class, 'destroy'])->name('excluir_produto')->middleware('auth');
    Route::get('/editar/{id}', [ProdutosController::class, 'editar'])->name('editar_produto')->middleware('auth');
    Route::put('/editar/{id}', [ProdutosController::class, 'update'])->name('update_produto')->middleware('auth');
});

//ESTOQUE
Route::prefix('estoque')->group(function () {
    Route::get('', [EstoqueController::class, 'show'])->middleware('auth');
    Route::get('/cadastro', [EstoqueController::class, 'new'])->middleware('auth');
    Route::post('/cadastro', [EstoqueController::class, 'store'])->name('salvar_estoque')->middleware('auth');
    Route::get('/del/{id}', [EstoqueController::class, 'destroy'])->name('excluir_estoque')->middleware('auth');
    Route::get('/edit/{id}', [EstoqueController::class, 'editar'])->name('editar_estoque')->middleware('auth');
    Route::post('/edit/{id}', [EstoqueController::class, 'update'])->name('update_estoque')->middleware('auth');
});

//RECEBER
Route::prefix('receber')->group(function () {
    Route::get('', [ReceberController::class, 'show'])->middleware('auth');
    Route::get('/cadastro', [ReceberController::class, 'new'])->middleware('auth');
    Route::post('/cadastro', [ReceberController::class, 'store'])->name('salvar_recebimento')->middleware('auth');
    Route::get('/del/{id}', [ReceberController::class, 'destroy'])->name('excluir_recebimento')->middleware('auth');
    Route::get('/edit/{id}', [ReceberController::class, 'editar'])->name('editar_recebimento')->middleware('auth');
    Route::post('/edit/{id}', [ReceberController::class, 'update'])->name('update_recebimento')->middleware('auth');
});

//VENDAS
Route::get('/venda', [PedidosController::class, 'todos'])->middleware('auth');
Route::post('/venda', [PedidosController::class, 'cancelarNFe'])->name('cancelar')->middleware('auth');
Route::get('/vendas', [PedidosController::class, 'todos'])->name('vendas.index')->middleware('auth');
Route::post('/vendas', [PedidosController::class, 'cancelarNFe'])->name('cancelar')->middleware('auth');
Route::get('/visualizar/{pedido}', [PedidosController::class, 'visualizar'])->name('vendas.show')->middleware('auth');
Route::get('/editar/{pedido}', [PedidosController::class, 'edit'])->name('vendas.editar')->middleware('auth');
Route::put('/atualizar/{id}', [PedidosController::class, 'update'])->name('vendas.atualizar')->middleware('auth');
Route::get('/vendas/nova', [PedidosController::class, 'new'])->middleware('auth');
Route::post('/vendas/nova', [PedidosController::class, 'store'])->name('salvar_venda')->middleware('auth');
Route::get('/venda/envio/{id}', [PedidosController::class, 'enviarNFe'])->name('enviarXML')->middleware('auth');
Route::get('/venda/imprimir/{id}', [PedidosController::class, 'imprimir'])->name('imprimirXML')->middleware('auth');
Route::get('/venda/imprimirCancelamento/{id}', [PedidosController::class, 'imprimirCancelamento'])->name('imprimirCancelamentoXML')->middleware('auth');
Route::post('/venda/cce', [PedidosController::class, 'cartaCorrecao'])->name('cartaCorrecao')->middleware('auth');
Route::get('/venda/cce/{id}', [PedidosController::class, 'imprimirCorrecao'])->middleware('auth');
Route::delete('/vendas/deletar', [PedidosController::class, 'destroyPedido'])->name('pedido.deletar')->middleware('auth');

//NOTAS FISCAIS
Route::get('/notas', [NotasFiscaisController::class, 'show'])->name('notas.index')->middleware('auth');
Route::get('/notas/{chave}', [NotasFiscaisController::class, 'visualizarPdf'])->middleware('auth');
Route::post('/notas/xml', [NotasFiscaisController::class, 'downloadXml'])->name('baixarXml')->middleware('auth');
Route::post('/zip', [NotasFiscaisController::class, 'zip'])->name('zip')->middleware('auth');

//INUTILIZAR
Route::prefix('inutilizar')->group(function () {
    Route::get('', [PedidosController::class, 'inutil'])->name('inutilizar.index')->middleware('auth');
    Route::post('', [PedidosController::class, 'inutilizar'])->name('inutilizar.create')->middleware('auth');
});

//RELATORIOS
Route::prefix('relatorios')->group(function () {
    Route::get('', [RelatoriosController::class, 'show'])->middleware('auth');
    Route::post('', [RelatoriosController::class, 'relatorio'])->name('relatorio')->middleware('auth');
    Route::get('/mdfe', [RelatoriosController::class, 'indexMDFe'])->name('relatorio.indexMDFe')->middleware('auth');
});

//MDFe
Route::prefix('mdfes')->group(function () {
    Route::get('', [MDFEController::class, 'index'])->name('mdfe.index')->middleware(['auth', 'admin']);
    Route::get('/emitir', [MDFEController::class, 'create'])->name('mdfe.create')->middleware('auth');
    Route::post('/emitir', [MDFEController::class, 'store'])->name('mdfe.store')->middleware('auth');
    Route::get('/{id}/editar', [MDFEController::class, 'edit'])->name('mdfe.edit')->middleware('auth');
    Route::put('/{id}/update', [MDFEController::class, 'update'])->name('mdfe.update')->middleware('auth');
    Route::delete('/deletar', [MDFEController::class, 'delete'])->name('mdfe.delete')->middleware('auth');
    Route::get('/{mdfeId}/visualizar', [MDFEController::class, 'visualizar'])->name('mdfe.view')->middleware('auth');
    Route::get('/{mdfeId}/download-xml', [MDFEController::class, 'downloadXML'])->name('mdfe.downloadXML')->middleware('auth');
    Route::get('/{mdfeId}/enviar-nota', [MDFEController::class, 'enviarMDFe'])->name('mdfe.enviar')->middleware('auth');
    Route::get('/{mdfeId}/encerrar-nota', [MDFEController::class, 'encerrarMDFe'])->name('mdfe.close')->middleware('auth');
    Route::post('/cancelar-nota', [MDFEController::class, 'cancelarMDFe'])->name('mdfe.cancel')->middleware('auth');
    Route::get('/{mdfeId}/{mode}/imprimir-nota', [MDFEController::class, 'imprimirMDFe'])->name('mdfe.print')->middleware('auth');
});

//MOTORISTAS
Route::prefix('motoristas')->group(function () {
    Route::get('', [MotoristaController::class, 'index'])->name('motorista.index')->middleware('auth');
    Route::get('/registrar', [MotoristaController::class, 'create'])->name('motorista.create')->middleware('auth');
    Route::post('/salvar', [MotoristaController::class, 'store'])->name('motorista.store')->middleware('auth');
    Route::get('/editar/{id}', [MotoristaController::class, 'edit'])->name('motorista.edit')->middleware('auth');
    Route::put('/atualizar/{id}', [MotoristaController::class, 'update'])->name('motorista.update')->middleware('auth');
    Route::delete('/deletar', [MotoristaController::class, 'delete'])->name('motorista.delete')->middleware('auth');
});

//VEICULO
Route::prefix('veiculos')->group(function () {
    Route::get('', [VeiculoController::class, 'index'])->name('veiculos.index')->middleware('auth');
    Route::get('/registrar', [VeiculoController::class, 'create'])->name('veiculos.create')->middleware('auth');
    Route::post('/salvar', [VeiculoController::class, 'store'])->name('veiculos.salvar')->middleware('auth');
    Route::get('/editar/{id}', [VeiculoController::class, 'edit'])->name('veiculos.edit')->middleware('auth');
    Route::put('/atualizar/{id}', [VeiculoController::class, 'update'])->name('veiculos.update')->middleware('auth');
    Route::delete('/deletar', [VeiculoController::class, 'delete'])->name('veiculos.delete')->middleware('auth');
});



require __DIR__ . '/auth.php';
