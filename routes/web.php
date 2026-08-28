<?php

use App\Http\Controllers\AjudaController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CTeController;
use App\Http\Controllers\CupomController;
use App\Http\Controllers\DRE;
use App\Http\Controllers\EmpresasController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\FluxoDeCaixaController;
use App\Http\Controllers\FormaPagController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItensEntradaController;
use App\Http\Controllers\MDFEController;
use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\NFCeController;
use App\Http\Controllers\NFComController;
use App\Http\Controllers\NotasFiscaisController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\PixController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\PlanoDeContaController;
use App\Http\Controllers\PreVendasController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\ReceberController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\ServicosController;
use App\Http\Controllers\TransactionLogController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VeiculoController;
use App\Models\CClassTrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Home
Route::get('/', function () {

    return view('homePage');
});

Route::get('/api/documentation', function () {
    return response()->file(public_path('docs/swagger.html'));
});

Route::get('/docs/openapi.yaml', function () {
    return response(file_get_contents(public_path('docs/openapi.yaml')), 200, [
        'Content-Type' => 'application/yaml',
    ]);
});

Route::get('/homePage', [HomeController::class, 'homePage'])->name('homePage');
Route::get('/homePage2', [HomeController::class, 'homePage2'])->name('homePage2');

// Planos
Route::get('/planos', [PlanoController::class, 'Planos'])->name('Planos');

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['auth']);

Route::middleware(['check.subscription'])->group(function () {
    // CATEGORIA
    Route::prefix('categoria')->group(function () {
        Route::get('', [CategoriasController::class, 'show'])->name('categoria.index')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/cadastro', [CategoriasController::class, 'new'])->name('cadastrar_categoria')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::post('/cadastro', [CategoriasController::class, 'store'])->name('salvar_categoria')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/status/{id}', [CategoriasController::class, 'destroy'])->name('desativarReativar_categoria')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    });

    // EMPRESA
    Route::prefix('empresa')->group(function () {
        Route::get('', [EmpresasController::class, 'index'])->name('empresa.index')->middleware(['auth', 'can:menu-administracao']);
        Route::get('/todas', [EmpresasController::class, 'show'])->name('empresa.show')->middleware(['auth', 'access.permission:master']);
        Route::get('/ver/{id}', [EmpresasController::class, 'view'])->name('empresa.view')->middleware(['auth', 'access.permission:master']);
        Route::get('/status/{id}', [EmpresasController::class, 'desativarReativar'])->name('desativarReativar_empresa')->middleware(['auth', 'access.permission:master']);
        Route::get('/cadastro', [EmpresasController::class, 'cadastrar'])->name('empresa.create')->middleware(['auth', 'access.permission:master']);
        Route::get('/editar/{id}', [EmpresasController::class, 'editar'])->name('editar_empresa')->middleware(['auth', 'can:menu-administracao']);
        Route::put('/salvar/{id}', [EmpresasController::class, 'update'])->name('update_empresa')->middleware(['auth', 'can:menu-administracao']);
        Route::post('/cadastrar', [EmpresasController::class, 'store'])->name('salvar_empresa')->middleware(['auth', 'access.permission:master']);
    });

    // CLIENTE
    Route::get('/cliente', [ClientesController::class, 'show'])->name('cliente.index')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    Route::get('/cliente/cadastro', [ClientesController::class, 'new'])->name('cliente.create')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    Route::post('/cliente/cadastro', [ClientesController::class, 'salvar'])->name('criar_cliente')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    Route::get('/cliente/ver/{id}', [ClientesController::class, 'view'])->name('cliente.view')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    Route::get('/cliente/edit/{id}', [ClientesController::class, 'editar'])->name('editar_cliente')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    Route::put('/cliente/salvar/{id}', [ClientesController::class, 'update'])->name('salvar_cliente')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    Route::delete('/cliente/del', [ClientesController::class, 'excluir'])->name('excluir_cliente')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    Route::post('/clientes/cnpj/', [ClientesController::class, 'BuscarCnpj'])->name('cnpj.clientes');

    // FORMA DE PAGAMENTO
    Route::prefix('forma')->group(function () {
        Route::get('', [FormaPagController::class, 'show'])->middleware('auth');
        Route::get('/cadastro', [FormaPagController::class, 'new'])->middleware('auth');
        Route::post('/cadastro', [FormaPagController::class, 'store'])->name('salvar_forma')->middleware('auth');
        Route::get('/del/{id}', [FormaPagController::class, 'excluir'])->name('excluir_forma')->middleware('auth');
    });

    // USUARIO
    Route::prefix('usuarios')->group(function () {
        Route::get('', [UsersController::class, 'show'])->name('index_usuario')->middleware('auth');
        Route::get('/criar', [UsersController::class, 'create'])->name('usuario.create')->middleware(['auth']);
        Route::post('/salvar', [UsersController::class, 'store'])->name('usuario.salvar')->middleware(['auth']);
        Route::delete('/deletar', [UsersController::class, 'destroy'])->name('excluir_usuario')->middleware(['auth']);
        Route::get('/editar/{id}', [UsersController::class, 'editar'])->name('editar_usuario')->middleware(['auth']);
        Route::put('/{id}/atualizar', [UsersController::class, 'update'])->name('usuario.update')->middleware(['auth']);

        Route::post('/inativar', [UsersController::class, 'inativar'])->name('usuario.inativar');
        Route::patch('/usuarios/{id}/ativar', [UsersController::class, 'ativar'])->name('usuario.ativar');
    });

    // PRODUTOS
    Route::prefix('produto')->group(function () {
        Route::get('', [ProdutosController::class, 'show'])->name('produto.index')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/cadastro', [ProdutosController::class, 'new'])->name('produto.new')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::post('/cadastro', [ProdutosController::class, 'store'])->name('salvar_produto')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/ver/{id}', [ProdutosController::class, 'view'])->name('ver_produto')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::delete('/del', [ProdutosController::class, 'destroy'])->name('excluir_produto')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/editar/{id}', [ProdutosController::class, 'editar'])->name('editar_produto')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::put('/editar/{id}', [ProdutosController::class, 'update'])->name('update_produto')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    });

    // ENTRADAS
    Route::prefix('entrada')->group(function () {
        Route::get('/', [EntradaController::class, 'index'])->name('entradas.index')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/criar', [EntradaController::class, 'create'])->name('entradas.create')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::post('/salvar', [EntradaController::class, 'store'])->name('entradas.store')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::post('/importar-produtos', [EntradaController::class, 'importProducts'])->name('importar_produtos')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/manual', [EntradaController::class, 'entradaManual'])->name('entradas.manual')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::delete('/entradas/{id}', [EntradaController::class, 'destroy'])->name('entradas.destroy')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('/entradas/{id}/pdf', [EntradaController::class, 'pdf'])->name('entradas.pdf')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    });

    // ITENS ENTRADAS
    Route::prefix('item-entrada')->group(function () {
        Route::get('/entrada/{entradaId}', [ItensEntradaController::class, 'show'])->name('itens-entradas.show')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    });

    // ESTOQUE
    Route::prefix('estoque')->group(function () {
        Route::get('', [EstoqueController::class, 'index'])->name('estoque.index')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('{estoqueId}/visualizar', [EstoqueController::class, 'show'])->name('estoque.show')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::get('{estoqueId}/editar', [EstoqueController::class, 'edit'])->name('estoque.edit')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
        Route::put('{estoqueId}/atualizar', [EstoqueController::class, 'update'])->name('estoque.update')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe']);
    });

    // RECEBER
    Route::prefix('receber')->group(function () {
        Route::get('', [ReceberController::class, 'show'])->middleware('auth');
        Route::get('/cadastro', [ReceberController::class, 'new'])->middleware('auth');
        Route::post('/cadastro', [ReceberController::class, 'store'])->name('salvar_recebimento')->middleware('auth');
        Route::get('/del/{id}', [ReceberController::class, 'destroy'])->name('excluir_recebimento')->middleware('auth');
        Route::get('/edit/{id}', [ReceberController::class, 'editar'])->name('editar_recebimento')->middleware('auth');
        Route::post('/edit/{id}', [ReceberController::class, 'update'])->name('update_recebimento')->middleware('auth');
    });

    // VENDAS
    Route::prefix('pre-vendas')->name('pre-vendas.')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe|client-NFCe'])->group(function () {
        Route::get('', [PreVendasController::class, 'index'])->name('index');
        Route::get('/nova', [PreVendasController::class, 'create'])->name('create');
        Route::post('', [PreVendasController::class, 'store'])->name('store');
        Route::get('/{id}', [PreVendasController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [PreVendasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PreVendasController::class, 'update'])->name('update');
        Route::post('/{id}/cancelar', [PreVendasController::class, 'cancelar'])->name('cancelar');
        Route::post('/{id}/converter', [PreVendasController::class, 'converter'])->name('converter');
        Route::get('/{id}/pdf', [PreVendasController::class, 'pdf'])->name('pdf');
    });

    Route::get('/venda', [PedidosController::class, 'todos'])->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::post('/venda', [PedidosController::class, 'cancelarNFe'])->name('cancelar')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/vendas', [PedidosController::class, 'todos'])->name('vendas.index')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::post('/vendas', [PedidosController::class, 'cancelarNFe'])->name('cancelar')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/visualizar/{pedido}', [PedidosController::class, 'visualizar'])->name('vendas.show')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/editar/{pedido}', [PedidosController::class, 'edit'])->name('vendas.editar')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::put('/atualizar/{id}', [PedidosController::class, 'update'])->name('vendas.atualizar')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/vendas/nova', [PedidosController::class, 'new'])->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::post('/vendas/nova', [PedidosController::class, 'store'])->name('salvar_venda')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/venda/envio/{id}', [PedidosController::class, 'enviarNFe'])->name('enviarXML')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/venda/imprimir/{id}', [PedidosController::class, 'imprimir'])->name('imprimirXML')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/venda/imprimirCancelamento/{id}', [PedidosController::class, 'imprimirCancelamento'])->name('imprimirCancelamentoXML')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::post('/venda/cce', [PedidosController::class, 'cartaCorrecao'])->name('cartaCorrecao')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/venda/cce/{id}', [PedidosController::class, 'imprimirCorrecao'])->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::delete('/vendas/deletar', [PedidosController::class, 'destroyPedido'])->name('pedido.deletar')->middleware(['auth', 'access.permission:master|admin|client-advanced1|client-advanced2|client-NFe']);
    Route::get('/total-mes-nfes', [PedidosController::class, 'totalMesNFe'])->name('totalMesNFe');

    // NOTAS FISCAIS
    Route::get('/notas', [NotasFiscaisController::class, 'show'])->name('notas.index')->middleware(['auth', 'access.permission:master|admin|client-NFe|client-advanced1|client-advanced2']);
    Route::get('/notas/{chave}', [NotasFiscaisController::class, 'visualizarPdf'])->middleware(['auth', 'access.permission:master|admin|client-NFe|client-advanced1|client-advanced2']);
    Route::post('/notas/xml', [NotasFiscaisController::class, 'downloadXml'])->name('baixarXml')->middleware(['auth', 'access.permission:master|admin|client-NFe|client-advanced1|client-advanced2']);
    Route::post('/zip', [NotasFiscaisController::class, 'zip'])->name('zip')->middleware(['auth', 'access.permission:master|admin|client-NFe|client-advanced1|client-advanced2']);

    // INUTILIZAR
    Route::prefix('inutilizar')->group(function () {
        Route::get('', [PedidosController::class, 'inutil'])->name('inutilizar.index')->middleware(['auth', 'access.permission:master|admin|client-NFe|client-advanced1|client-advanced2']);
        Route::post('', [PedidosController::class, 'inutilizar'])->name('inutilizar.create')->middleware(['auth', 'access.permission:master|admin|client-NFe|client-advanced1|client-advanced2']);
    });

    // RELATORIOS
    Route::prefix('relatorios')->group(function () {
        Route::get('', [RelatoriosController::class, 'show'])->middleware(['auth', 'can:menu-administracao']);
        Route::post('', [RelatoriosController::class, 'relatorio'])->name('relatorio')->middleware(['auth', 'can:menu-administracao']);
        Route::get('/mdfe', [RelatoriosController::class, 'indexMDFe'])->name('relatorio.indexMDFe')->middleware('auth');
        Route::post('/relatorio-pdf', [RelatoriosController::class, 'gerarPdf'])->name('relatorio-pdf')->middleware(['auth', 'can:menu-administracao']);
        Route::get('/dashboard-data', [RelatoriosController::class, 'dashboardData'])->name('relatorios.dashboard-data')->middleware(['auth', 'can:menu-administracao']);
    });

    // MDFe
    Route::prefix('mdfes')->group(function () {
        Route::get('', [MDFEController::class, 'index'])->name('mdfe.index')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/emitir', [MDFEController::class, 'create'])->name('mdfe.create')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::post('/emitir', [MDFEController::class, 'store'])->name('mdfe.store')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/{id}/editar', [MDFEController::class, 'edit'])->name('mdfe.edit')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::put('/{id}/update', [MDFEController::class, 'update'])->name('mdfe.update')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::delete('/deletar', [MDFEController::class, 'delete'])->name('mdfe.delete')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/{mdfeId}/visualizar', [MDFEController::class, 'visualizar'])->name('mdfe.view')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/{mdfeId}/download-xml', [MDFEController::class, 'downloadXML'])->name('mdfe.downloadXML')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/{mdfeId}/enviar-nota', [MDFEController::class, 'enviarMDFe'])->name('mdfe.enviar')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/{mdfeId}/encerrar-nota', [MDFEController::class, 'encerrarMDFe'])->name('mdfe.close')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::post('/cancelar-nota', [MDFEController::class, 'cancelarMDFe'])->name('mdfe.cancel')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/{mdfeId}/{mode}/imprimir-nota', [MDFEController::class, 'imprimirMDFe'])->name('mdfe.print')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/total-mes-mdfes', [MDFEController::class, 'totalMesMDFe'])->name('totalMesMDFe');
    });

    // NFCom
    Route::prefix('nfcom')->group(function () {
        Route::get('', [NFComController::class, 'index'])->name('nfcom.index')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::get('/emitir', [NFComController::class, 'create'])->name('nfcom.create')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::post('/emitir', [NFComController::class, 'store'])->name('nfcom.store')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::get('/{id}/editar', [NFComController::class, 'edit'])->name('nfcom.edit')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::put('/{id}/update', [NFComController::class, 'update'])->name('nfcom.update')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::delete('/deletar', [NFComController::class, 'delete'])->name('nfcom.delete')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::get('/{id}/visualizar', [NFComController::class, 'visualizar'])->name('nfcom.view')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::get('/{id}/download-xml', [NFComController::class, 'downloadXml'])->name('nfcom.downloadXml')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::get('/{id}/enviar-nota', [NFComController::class, 'enviarNFCom'])->name('nfcom.enviar')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::post('/cancelar-nota', [NFComController::class, 'cancelarNFCom'])->name('nfcom.cancel')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
    });

    // Serviços (catálogo NFCom)
    Route::prefix('servicos')->group(function () {
        Route::get('', [ServicosController::class, 'index'])->name('servicos.index')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::post('', [ServicosController::class, 'store'])->name('servicos.store')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::put('/{id}', [ServicosController::class, 'update'])->name('servicos.update')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
        Route::delete('', [ServicosController::class, 'destroy'])->name('servicos.destroy')->middleware(['auth', 'access.permission:master|admin|client-NFCom']);
    });

    // CTe
    Route::prefix('ctes')->group(function () {
        Route::get('', [CTeController::class, 'index'])->name('cte.index')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::get('/emitir', [CTeController::class, 'create'])->name('cte.create')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::post('/emitir', [CTeController::class, 'store'])->name('cte.store')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::get('/{id}/editar', [CTeController::class, 'edit'])->name('cte.edit')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::put('/{id}/update', [CTeController::class, 'update'])->name('cte.update')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::delete('/deletar', [CTeController::class, 'delete'])->name('cte.delete')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::get('/{id}/visualizar', [CTeController::class, 'visualizar'])->name('cte.view')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::get('/{id}/download-xml', [CTeController::class, 'downloadXml'])->name('cte.downloadXml')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::get('/{id}/enviar-nota', [CTeController::class, 'enviarCTe'])->name('cte.enviar')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
        Route::post('/cancelar-nota', [CTeController::class, 'cancelarCTe'])->name('cte.cancel')->middleware(['auth', 'access.permission:master|admin|client-CTe|client-advanced3']);
    });

    // MOTORISTAS
    Route::prefix('motoristas')->group(function () {
        Route::get('', [MotoristaController::class, 'index'])->name('motorista.index')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/registrar', [MotoristaController::class, 'create'])->name('motorista.create')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::post('/salvar', [MotoristaController::class, 'store'])->name('motorista.store')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/editar/{id}', [MotoristaController::class, 'edit'])->name('motorista.edit')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::put('/atualizar/{id}', [MotoristaController::class, 'update'])->name('motorista.update')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::delete('/deletar', [MotoristaController::class, 'delete'])->name('motorista.delete')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
    });

    // VEICULO
    Route::prefix('veiculos')->group(function () {
        Route::get('', [VeiculoController::class, 'index'])->name('veiculos.index')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/registrar', [VeiculoController::class, 'create'])->name('veiculos.create')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::post('/salvar', [VeiculoController::class, 'store'])->name('veiculos.salvar')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::get('/editar/{id}', [VeiculoController::class, 'edit'])->name('veiculos.edit')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::put('/atualizar/{id}', [VeiculoController::class, 'update'])->name('veiculos.update')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
        Route::delete('/deletar', [VeiculoController::class, 'delete'])->name('veiculos.delete')->middleware(['auth', 'access.permission:master|admin|client-MDFe|client-advanced1|client-advanced3']);
    });

    // NFCe
    Route::prefix('nfce')->group(function () {
        Route::get('/{id}/visualizar', [NFCeController::class, 'show'])->name('nfce.show')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::get('/{id}/enviar', [NFCeController::class, 'sendNFCe'])->name('nfce.send')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::post('/enviar-lote', [NFCeController::class, 'sendLotOfNFCe'])->name('nfce.sendLot')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::get('/inutilizar', [NFCeController::class, 'showUnuser'])->name('nfce.showUnuser')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::post('/inutilizar', [NFCeController::class, 'unuseNFCe'])->name('nfce.unuse')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::delete('/cancelar', [NFCeController::class, 'cancelNFCe'])->name('nfce.cancel')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::get('/xmls/download', [NFCeController::class, 'index'])->name('nfce.index')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::get('/download/xml/{nfceId}', [NFCeController::class, 'downloadXmlNFCe'])->name('nfce.downloadXml')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::post('/enviar-xmls-contador', [NFCeController::class, 'sendXmlsToAccountant'])->name('nfce.sendXmlsToAccountant')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::get('/total-mes-nfces', [NFCeController::class, 'totalMesNFCe'])->name('totalMesNFCe');
    });

    // CUPOM
    Route::prefix('cupom')->group(function () {
        Route::get('', [CupomController::class, 'index'])->name('cupom.index')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::get('/criar', [CupomController::class, 'create'])->name('cupom.create')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::post('/salvar', [CupomController::class, 'store'])->name('cupom.store')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::get('/{id}/visualizar', [CupomController::class, 'showPreView'])->name('cupom.showPreView')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
        Route::delete('/cancelar', [CupomController::class, 'destroyCoupon'])->name('cupom.destroy')->middleware(['auth', 'access.permission:master|admin|client-NFCe|client-advanced2']);
    });

    Route::resource('contas', PlanoDeContaController::class)->middleware(['auth', 'can:menu-administracao']);
    Route::resource('fluxo-caixa', FluxoDeCaixaController::class)->middleware(['auth', 'can:menu-administracao']);
    Route::get('/rel/fluxo-caixa', [FluxoDeCaixaController::class, 'telaRrelatorio'])->name('rel')->middleware(['auth', 'can:menu-administracao']);
    Route::get('/relatorio/fluxo-caixa', [FluxoDeCaixaController::class, 'gerarRelatorio'])->name('fluxo_caixa.gerarRelatorio')->middleware(['auth', 'can:menu-administracao']);
    Route::get('/dre', [DRE::class, 'index'])->name('dre.index')->middleware(['auth', 'can:menu-administracao']);
    Route::post('/dre', [DRE::class, 'index'])->name('dre.filtrar')->middleware(['auth', 'can:menu-administracao']);
    Route::get('/dre-pdf', [DRE::class, 'gerarPDF'])->name('dre.pdf')->middleware(['auth', 'can:menu-administracao']);

    // AJUDA / TUTORIAIS
    Route::prefix('ajuda')->name('ajuda.')->middleware('auth')->group(function () {
        Route::get('/', [AjudaController::class, 'index'])->name('index');
        Route::get('/categoria/{categoria}', [AjudaController::class, 'categoria'])->name('categoria');
        Route::get('/artigo/{artigo}', [AjudaController::class, 'artigo'])->name('artigo');
    });

    Route::prefix('notificacoes')->middleware(['auth'])->group(function () {
        Route::get('/central', [NotificacaoController::class, 'index'])->name('notificacoes.index')->middleware('access.permission:master');
        Route::post('/central', [NotificacaoController::class, 'store'])->name('notificacoes.store')->middleware('access.permission:master');
        Route::get('/minhas', [NotificacaoController::class, 'minhas'])->name('notificacoes.minhas');
        Route::get('/widget', [NotificacaoController::class, 'widget'])->name('notificacoes.widget');
        Route::post('/{id}/marcar-lida', [NotificacaoController::class, 'marcarLida'])->name('notificacoes.marcarLida');
        Route::post('/marcar-todas-lidas', [NotificacaoController::class, 'marcarTodasLidas'])->name('notificacoes.marcarTodasLidas');
    });

});
// PIX
Route::post('/pix/cob', [PixController::class, 'criar'])->name('pix.criar');
Route::get('/pix/cob/{txid}', [PixController::class, 'consultar']);

// Página de pagamento PIX (GET /pagamento)
Route::post('/pagamento', [PixController::class, 'pagar'])->name('pix.pagamento');

Route::get('/pagamento/sucesso', function (Request $req) {
    return view('faturas.pagamento-sucesso', [
        'txid' => $req->query('txid'),
        'valor' => $req->query('valor'),
        'descricao' => $req->query('descricao'),
    ]);
})->name('pix.sucesso');

// FATURAS
Route::prefix('faturas')->middleware(['auth', 'can:menu-administracao'])->group(function () {
    Route::get('', [FaturaController::class, 'index'])->name('faturas.index');
    Route::get('/historico-pagamentos', [FaturaController::class, 'paymentHistory'])->name('faturas.paymentHistory');
    Route::get('/metodos-pagamentos', [FaturaController::class, 'paymentMethods'])->name('faturas.paymentMethods');
});

Route::get('/log', [TransactionLogController::class, 'index'])->name('log.index')->middleware(['auth', 'can:menu-administracao']);

Route::post('/clientes/check-cpf', [ClientesController::class, 'checkCpfCnpj'])->name('cliente.checkCpfCnpj');

Route::post('/pagamento/enviar-confirmacao', [PixController::class, 'enviarConfirmacaoEmail'])
    ->name('pix.enviarConfirmacao');

Route::get('/api/cclasstrib/{cst}', function ($cst) {
    // Busca códigos que batem exatamente com o CST, ordenados
    return CClassTrib::where('cst_compativel', $cst)
        ->orWhere('cst_compativel', intval($cst)) // Previne erro de '010' vs '10'
        ->select('codigo', 'descricao')
        ->orderBy('codigo')
        ->get();
});

Route::post('/notas/enviar-contador', [NotasFiscaisController::class, 'enviarXmlsContador'])->name('nfe.enviarContador');

require __DIR__.'/auth.php';
