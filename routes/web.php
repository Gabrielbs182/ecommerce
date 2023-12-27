<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CidadeControllador;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\MelhorEnvioController;


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



// API'S
Route::get('/cidades/{idCidade}', [CidadeControllador::class, 'buscarCidades'])
    ->name('buscarCidades');
Route::get('/produto/{idProduto}', [ProdutoController::class, 'buscarProduto'])
    ->name('buscarProduto');
Route::get('/buscarCarrinho', [ProdutoController::class, 'buscarCarrinho'])
    ->name('buscarCarrinho');
Route::get('/estoque/{idProduto}', [ProdutoController::class, 'buscarEstoque'])
    ->name('buscarEstoque');
Route::get('/estoque/{idProduto}/{numero}', [ProdutoController::class, 'buscarEstoqueQuantidade'])
    ->name('buscarEstoqueQuantidade');
Route::get('/melhorenvio/authorize', [MelhorEnvioController::class, 'redirectToAuthorization'])->name('authorize');
Route::get('/melhorenvio/melhorInfo', [MelhorEnvioController::class, 'melhorInfo'])->name('melhorInfo');
Route::post('/melhorenvio/token', [MelhorEnvioController::class, 'getToken'])->name('getToken');
Route::get('/melhorenvio/teste', [MelhorEnvioController::class, 'teste'])->name('teste');
Route::get('/melhorenvio/authorize', [MelhorEnvioController::class, 'redirectToAuthorization'])->name('melhorenvio.authorize')->middleware('IsAdmin');
Route::get('/melhorenvio/getAuthorization', [MelhorEnvioController::class, 'getAuthorization'])->name('melhorenvio.getAuthorization')->middleware('IsAdmin');
Route::get('/melhorenvio/calcularFrete', [MelhorEnvioController::class, 'calcularFrete'])->name('melhorenvio.calcularFrete')->middleware('IsAdmin');


//REDIRECIONAMENTO
Route::match(['get', 'post'], '/', [ProdutoController::class, 'index'])
    ->name('home');
Route::match(['get', 'post'], '/categoria', [ProdutoController::class, 'categoria'])
    ->name('categoria');
Route::match(['get', 'post'], '/cadastrar', [ClienteController::class, 'cadastrar'])
    ->name('cadastrar');
Route::match(['get', 'post'], '/{idcategoria}/categoria', [ProdutoController::class, 'categoria'])
    ->name('categoria_por_id');
Route::match(['get', 'post'], '/carrinho', [ProdutoController::class, 'verCarrinho'])
    ->name('ver_carrinho');
Route::post('/compras/detalhes', [ProdutoController::class, 'detalhes'])
    ->name('compra_detalhes');
Route::get('/compras/{idproduto}', [ProdutoController::class, 'detalhes'])
    ->name('detalhes');
Route::match(['get', 'post'], '/pagamento', [ProdutoController::class, 'pagamento'])
    ->name('pagamento');
Route::match(['get', 'post'], '/keyController', [ProdutoController::class, 'keyController'])
    ->name('keyController');
Route::match(['get', 'post'], '/envio/autorizar', [ProdutoController::class, 'autorizarEnvio'])
    ->name('autorizarEnvio');

//METODOS
Route::match(['get', 'post'], '/logar', [UsuarioController::class, 'logar'])
    ->name('logar');
Route::get('/sair', [UsuarioController::class, 'sair'])
    ->name('sair');
Route::match(['get', 'post'], '/cliente/cadastrar', [ClienteController::class, 'cadastrarCliente'])
    ->name('cadastrar_cliente');
Route::match(['get', 'post'], '/carrinho/adicionar/{idProduto}/{quantidade}', [ProdutoController::class, 'adicionarCarrinho'])
    ->name('adicionar_carrinho');
Route::match(['get', 'post'], '/{indice}/excluirCarrinho', [ProdutoController::class, 'excluirCarrinho'])
    ->name('excluirCarrinho');
Route::post('/carrinho/finalizar', [ProdutoController::class, 'finalizar'])
    ->name('carrinho_finalizar');
Route::get('/compras/historico', [ProdutoController::class, 'historico'])
    ->name('compra_historico');
Route::match(['get', 'post'], '/pagar', [ProdutoController::class, 'postToPagseguro'])
    ->name('pagar');
Route::get('/atualizar-quantidade-carrinho', [ProdutoController::class, 'atualizarQuantidadeCarrinho'])->name('atualizar_quantidade_carrinho');
