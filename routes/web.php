<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\PagSeguroController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
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
Route::get('/produto/buscar/{idProduto}', [ProdutoController::class, 'buscarProduto'])
    ->name('buscarProduto');
Route::get('/buscarCarrinho', [ProdutoController::class, 'buscarCarrinho'])
    ->name('buscarCarrinho');
Route::get('/estoqueNumero/{idProduto}/{numero}', [ProdutoController::class, 'buscarEstoqueQuantidade'])
    ->name('buscarEstoqueQuantidade');
Route::get('/estoque/{idProduto}', [ProdutoController::class, 'buscarEstoque'])
    ->name('buscarEstoque');
Route::get('/email/{id}', [ProdutoController::class, 'email']);
Route::get('/testeEmail', [ProdutoController::class, 'emailTeste']);


//Melhor Envio
Route::get('/melhorenvio/melhorInfo', [MelhorEnvioController::class, 'melhorInfo'])->name('melhorInfo')->middleware('IsAdmin');
Route::post('/melhorenvio/token', [MelhorEnvioController::class, 'getToken'])->name('getToken')->middleware('IsAdmin');
Route::get('/melhorenvio/authorize', [MelhorEnvioController::class, 'redirectToAuthorization'])->name('melhorenvio.authorize')->middleware('IsAdmin');
Route::get('/melhorenvio/getAuthorization', [MelhorEnvioController::class, 'getAuthorization'])->name('melhorenvio.getAuthorization')->middleware('IsAdmin');
Route::get('/melhorenvio/calcularFrete/{cep}', [MelhorEnvioController::class, 'calcularFrete'])->name('melhorenvio.calcularFrete');
Route::get('/melhorenvio/calcularFrete2/{cep}/{idPedido}', [MelhorEnvioController::class, 'calcularFrete2'])->name('melhorenvio.calcularFrete2');
Route::get('/melhorenvio/lancarFrete/{reference_id}', [MelhorEnvioController::class, 'lancarFrete'])->name('melhorenvio.lancarFrete');
//PagSeguro
Route::get('/pagseguro/criarapp', [PagSeguroController::class, 'PagSeguroCriarApp'])->name('pagseguro.criarapp')->middleware('IsAdmin');
Route::get('/pagbank/authorize', [PagSeguroController::class, 'redirectToAuthorization'])->name('pagseguro.authorization')->middleware('IsAdmin');
Route::get('/pagbank/auth', [PagSeguroController::class, 'PagSeguroAuth'])->name('pagseguro.auth')->middleware('IsAdmin');
Route::get('/pagbank/gettoken', [PagSeguroController::class, 'PagSeguroGetToken'])->name('pagseguro.gettoken')->middleware('IsAdmin');
Route::get('/pagbank/checkout', [PagSeguroController::class, 'PagSeguroCheckout'])->name('pagseguro.checkout');
Route::get('/pagbank/pospagar', [PagSeguroController::class, 'PosPagar'])->name('pagseguro.pospagar');


//REDIRECIONAMENTO
Route::get('/categoria/cadastrar', [ProdutoController::class, 'cadastrarCategoria'])
    ->name('categoria.cadastrar')->middleware('IsAdmin');
Route::get('/produto/categoria/{id}', [ProdutoController::class, 'verCategoria'])
    ->name('produto.categoria');
Route::get('/pedidos/pendentes', [ProdutoController::class, 'pedidosPendentes'])
    ->name('pedidos.pendentes')->middleware('IsAdmin');
Route::get('/estoque/produto/{id}', [ProdutoController::class, 'estoqueProduto'])
    ->name('estoque.index')->middleware('IsAdmin');
Route::get('/categoria/editar/{id}', [ProdutoController::class, 'editarCategoria'])
    ->name('categoria.editar')->middleware('IsAdmin');
Route::get('/categorias', [ProdutoController::class, 'categorias'])->name('categorias')->middleware('IsAdmin');
Route::get('/produto/cadastrar', [ProdutoController::class, 'cadastrarProduto'])
    ->name('produto.cadastrar')->middleware('IsAdmin');
Route::match(['get', 'post'], '/', [ProdutoController::class, 'index'])
    ->name('home');
Route::match(['get', 'post'], '/itens/{id}', [ProdutoController::class, 'itensPedido'])
    ->name('itensPedido');
Route::match(['get', 'post'], '/categoria', [ProdutoController::class, 'categoria'])
    ->name('categoria');
Route::match(['get', 'post'], '/cadastrar', [ClienteController::class, 'cadastrar'])
    ->name('cadastrar');
Route::match(['get', 'post'], '/{idcategoria}/categoria', [ProdutoController::class, 'categoria'])
    ->name('categoria_por_id');
Route::match(['get', 'post'], '/carrinho', [ProdutoController::class, 'verCarrinho'])
    ->name('ver_carrinho');
Route::get('/compras/detalhes/{idproduto}', [ProdutoController::class, 'detalhes'])
    ->name('detalhes');
Route::match(['get', 'post'], '/keyController', [ProdutoController::class, 'keyController'])
    ->name('keyController');
Route::match(['get', 'post'], '/envio/autorizar', [ProdutoController::class, 'autorizarEnvio'])
    ->name('autorizarEnvio');
Route::match(['get'], '/configuracoes', [ProdutoController::class, 'configuracoes'])
    ->name('configuracoes')->middleware('IsAdmin');
Route::get('/produtos', [ProdutoController::class, 'produtos'])->name('produtos')->middleware('IsAdmin');
Route::get('/produto/editar/{id}', [ProdutoController::class, 'editarProduto'])
    ->name('produto.editar')->middleware('IsAdmin');

//METODOS
Route::get('/pedido/finalizar/{id}', [ProdutoController::class, 'finalizarPedido'])
    ->name('pedido.finalizar')->middleware('IsAdmin');
Route::post('/atualizar_estoque', [ProdutoController::class, 'atualizarEstoque'])->name('atualizar_estoque')->middleware('IsAdmin');;
Route::get('/categoria/excluir/{id}', [ProdutoController::class, 'deleteCategoria'])
    ->name('categoria.excluir')->middleware('IsAdmin');
Route::post('/categoria/update', [ProdutoController::class, 'updateCategoria'])
    ->name('categoria.update')->middleware('IsAdmin');
Route::post('/categoria/store', [ProdutoController::class, 'storeCategoria'])
    ->name('categoria.store')->middleware('IsAdmin');
Route::post('/produto/store', [ProdutoController::class, 'store'])
    ->name('produto.store')->middleware('IsAdmin');
Route::get('/produto/excluir/{id}', [ProdutoController::class, 'delete'])
    ->name('produto.excluir')->middleware('IsAdmin');
Route::post('/produto/update', [ProdutoController::class, 'update'])
    ->name('produto.update')->middleware('IsAdmin');
Route::match(['get', 'post'], '/logar', [UsuarioController::class, 'logar'])
    ->name('logar');
Route::get('/sair', [UsuarioController::class, 'sair'])
    ->name('sair');
Route::match(['get', 'post'], '/cliente/cadastrar', [ClienteController::class, 'cadastrarCliente'])
    ->name('cadastrar_cliente');
Route::match(['get', 'post'], '/carrinho/adicionar/{idProduto}/{quantidade}/{numero}', [ProdutoController::class, 'adicionarCarrinho'])
    ->name('adicionar_carrinho');
Route::match(['get', 'post'], '/{indice}/excluirCarrinho', [ProdutoController::class, 'excluirCarrinho'])
    ->name('excluirCarrinho');
Route::post('/carrinho/finalizar', [ProdutoController::class, 'finalizar'])
    ->name('carrinho_finalizar');
Route::get('/compras/historico', [ProdutoController::class, 'historico'])
    ->name('compra_historico')->middleware('auth');
Route::get('/compras/detalhes/{id}', [ProdutoController::class, 'compra_detalhes'])
    ->name('compra_detalhes');
Route::match(['get', 'post'], '/pagar', [PagSeguroController::class, 'PagSeguroCheckout'])
    ->name('pagar');
Route::get('/pagbank/consultar/{idCheckout}', [ProdutoController::class, 'consultarCheckout'])
    ->name('consultarCheckout');
Route::get('/atualizar-quantidade-carrinho', [ProdutoController::class, 'atualizarQuantidadeCarrinho'])->name('atualizar_quantidade_carrinho');
Route::get('/buscarItemCarrinho/{id}/{numero}', [ProdutoController::class, 'buscarItemCarrinho'])->name('buscarItemCarrinho');
Route::post('/configuracoes/update', [ProdutoController::class, 'configuracoesUpdate'])->name('configuracoes.update')->middleware('IsAdmin');;

//WEBHOOKS
Route::post('/webhook/pagbank', [PagSeguroController::class, 'handle']);
