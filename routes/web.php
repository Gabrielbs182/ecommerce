<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\PagSeguroController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MelhorEnvioController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;


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



// API'S Internas
Route::get('/produto/buscar/{idProduto}', [ProdutoController::class, 'buscarProduto'])
    ->name('buscarProduto');
Route::get('/buscarCarrinho', [ProdutoController::class, 'buscarCarrinho'])
    ->name('buscarCarrinho');
Route::get('/estoqueNumero/{idProduto}/{numero}', [ProdutoController::class, 'buscarEstoqueQuantidade'])
    ->name('buscarEstoqueQuantidade');
Route::get('/estoque/{idProduto}', [ProdutoController::class, 'buscarEstoque'])
    ->name('buscarEstoque');
Route::get('/endereco/{id}', [ProdutoController::class, 'buscarEndereco'])
    ->name('buscarEstoque');

//Melhor Envio
Route::get('/melhorenvio/melhorInfo', [MelhorEnvioController::class, 'melhorInfo'])->name('melhorInfo')->middleware('IsAdmin');
Route::get('/melhorenvio/authorize', [MelhorEnvioController::class, 'redirectToAuthorization'])->name('melhorenvio.authorize')->middleware('IsAdmin');
Route::get('/melhorenvio/getAuthorization', [MelhorEnvioController::class, 'getAuthorization'])->name('melhorenvio.getAuthorization')->middleware('IsAdmin');
Route::get('/melhorenvio/calcularFrete/{cep}', [MelhorEnvioController::class, 'calcularFrete'])->name('melhorenvio.calcularFrete');
Route::get('/melhorenvio/freteFixo', [MelhorEnvioController::class, 'consultarFreteFixo'])->name('melhorenvio.consultarFreteFixo');
Route::get('/melhorenvio/frete299', [MelhorEnvioController::class, 'consultarFrete299'])->name('melhorenvio.consultarFrete299');
Route::get('/melhorenvio/motoboy/{cidade}', [MelhorEnvioController::class, 'consultarMotoboy'])->name('melhorenvio.consultarMotoboy');
Route::get('/melhorenvio/lancarFrete/{reference_id}', [MelhorEnvioController::class, 'lancarFrete'])->name('melhorenvio.lancarFrete');

//PagSeguro
Route::get('/pagbank/checkout', [PagSeguroController::class, 'PagSeguroCheckout'])->name('pagseguro.checkout');
Route::get('/pagbank/pospagar', [PagSeguroController::class, 'PosPagar'])->name('pagseguro.pospagar');

//Usuario
Route::get('/email/verify/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    $request->session()->flash("ok", "Email verificado.");
    return redirect('/');
})->middleware(['auth'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    $request->session()->flash("ok", "Email de verificação reenviado.");
    return redirect('/');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/reset-password', [UsuarioController::class, 'store'])->name('password.store');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    $request->session()->flash("ok", "Email de recuperação enviado.");

    return $status === Password::RESET_LINK_SENT
        ? redirect()->route('home')
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
})->middleware('guest')->name('password.reset');


//REDIRECIONAMENTO
Route::get('/categoria/cadastrar', [ProdutoController::class, 'cadastrarCategoria'])
    ->name('categoria.cadastrar')->middleware('IsAdmin');

Route::get('/politica', [ProdutoController::class, 'politica'])
    ->name('politica');

Route::get('/cupom/cadastrar', [ProdutoController::class, 'cadastrarCupom'])
    ->name('cupom.cadastrar')->middleware('IsAdmin');
Route::get('/produto/categoria/{id}', [ProdutoController::class, 'verCategoria'])
    ->name('produto.categoria');

Route::get('/email/{id}', [ProdutoController::class, 'email'])
    ->name('email.teste')->middleware('isAdmin');

Route::get('/pedidos/pendentes', [ProdutoController::class, 'pedidosPendentes'])
    ->name('pedidos.pendentes')->middleware('IsAdmin');
Route::get('/pedidos/todos', [ProdutoController::class, 'todosPedidos'])
    ->name('pedidos.todos')->middleware('IsAdmin');
Route::delete('/pedido/{id}', [ProdutoController::class, 'deletePedido'])
    ->name('pedido.excluir')->middleware('IsAdmin');
Route::get('/estoque/produto/{id}', [ProdutoController::class, 'estoqueProduto'])
    ->name('estoque.index')->middleware('IsAdmin');
Route::get('/categoria/editar/{id}', [ProdutoController::class, 'editarCategoria'])
    ->name('categoria.editar')->middleware('IsAdmin');
Route::get('/cupom/editar/{id}', [ProdutoController::class, 'editarCupom'])
    ->name('cupom.editar')->middleware('IsAdmin');
Route::get('/categorias', [ProdutoController::class, 'categorias'])->name('categorias')->middleware('IsAdmin');
Route::get('/cupons', [ProdutoController::class, 'cupons'])->name('cupons')->middleware('IsAdmin');
Route::get('/produto/cadastrar', [ProdutoController::class, 'cadastrarProduto'])
    ->name('produto.cadastrar')->middleware('IsAdmin');
Route::match(['get', 'post'], '/', [ProdutoController::class, 'index'])
    ->name('home');
Route::match(['get', 'post'], '/itens/{id}', [ProdutoController::class, 'itensPedido'])
    ->name('itensPedido');
Route::match(['get', 'post'], '/buscarCupom/{id}', [ProdutoController::class, 'buscarCupom'])
    ->name('buscarCupom');
Route::match(['get', 'post'], '/categoria', [ProdutoController::class, 'categoria'])
    ->name('categoria')->middleware('IsAdmin');
Route::match(['get', 'post'], '/cadastrar', [ClienteController::class, 'cadastrar'])
    ->name('cadastrar')->middleware('guest');
Route::match(['get', 'post'], '/{idcategoria}/categoria', [ProdutoController::class, 'categoria'])
    ->name('categoria_por_id');
Route::match(['get', 'post'], '/carrinho', [ProdutoController::class, 'verCarrinho'])
    ->name('ver_carrinho');
Route::get('/compras/detalhes/{idproduto}', [ProdutoController::class, 'detalhes'])
    ->name('detalhes')->middleware('isVerified');
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
Route::get('/cupom/excluir/{id}', [ProdutoController::class, 'deleteCupom'])
    ->name('cupom.excluir')->middleware('IsAdmin');
Route::post('/categoria/update', [ProdutoController::class, 'updateCategoria'])
    ->name('categoria.update')->middleware('IsAdmin');
Route::post('/cupom/update', [ProdutoController::class, 'updateCupom'])
    ->name('cupom.update')->middleware('IsAdmin');
Route::post('/categoria/store', [ProdutoController::class, 'storeCategoria'])
    ->name('categoria.store')->middleware('IsAdmin');
Route::post('/cupom/store', [ProdutoController::class, 'storeCupom'])
    ->name('cupom.store')->middleware('IsAdmin');
Route::post('/produto/store', [ProdutoController::class, 'store'])
    ->name('produto.store')->middleware('IsAdmin');
Route::get('/produto/excluir/{id}', [ProdutoController::class, 'delete'])
    ->name('produto.excluir')->middleware('IsAdmin');
Route::post('/produto/update', [ProdutoController::class, 'update'])
    ->name('produto.update')->middleware('IsAdmin');
Route::match(['get', 'post'], '/logar', [UsuarioController::class, 'logar'])
    ->name('logar')->middleware('guest');
Route::get('/sair', [UsuarioController::class, 'sair'])
    ->name('sair');
Route::match(['get', 'post'], '/cliente/cadastrar', [ClienteController::class, 'cadastrarCliente'])
    ->name('cadastrar_cliente')->middleware('isVerified');
Route::match(['get', 'post'], '/carrinho/adicionar/{idProduto}/{quantidade}/{numero}', [ProdutoController::class, 'adicionarCarrinho'])
    ->name('adicionar_carrinho');
Route::match(['get', 'post'], '/{indice}/excluirCarrinho', [ProdutoController::class, 'excluirCarrinho'])
    ->name('excluirCarrinho');
Route::post('/carrinho/finalizar', [ProdutoController::class, 'finalizar'])
    ->name('carrinho_finalizar')->middleware('isVerified');;
Route::get('/compras/historico', [ProdutoController::class, 'historico'])
    ->name('compra_historico')->middleware(['auth', 'verified']);
Route::get('/compras/detalhes/{id}', [ProdutoController::class, 'compra_detalhes'])
    ->name('compra_detalhes');
Route::match(['get', 'post'], '/pagar', [PagSeguroController::class, 'PagSeguroCheckout'])
    ->name('pagar')->middleware('isVerified');
Route::get('/pagbank/consultar/{idCheckout}', [ProdutoController::class, 'consultarCheckout'])
    ->name('consultarCheckout')->middleware('isVerified');
Route::get('/atualizar-quantidade-carrinho', [ProdutoController::class, 'atualizarQuantidadeCarrinho'])->name('atualizar_quantidade_carrinho');
Route::get('/buscarItemCarrinho/{id}/{numero}', [ProdutoController::class, 'buscarItemCarrinho'])->name('buscarItemCarrinho');
Route::post('/configuracoes/update', [ProdutoController::class, 'configuracoesUpdate'])->name('configuracoes.update')->middleware('IsAdmin');;

//WEBHOOKS
Route::post('/webhook/pagbank', [PagSeguroController::class, 'handle']);
