<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Illuminate\Support\Facades\Http;
use App\Models\Pedido;
use App\Models\Endereco;
use App\Models\Usuario;
use App\Models\ItensPedido;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\MelhorEnvioController;
use App\Models\Estoque;
use DateTime;

class PagSeguroController extends Controller
{

    public function consultarCheckout($checkoutId)
    {
        $token = env('PAGSEGURO_TOKEN'); // Substitua pela obtenção do seu token

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get('https://sandbox.api.pagseguro.com/checkouts/' . $checkoutId);
        } catch (\EXCEPTION $e) {
            Log::error("ERRO", ['file' => 'ProdutoController.consultarCheckout', 'message' => $e->getMessage()]);
            return redirect()->route('home');
        }
        return response()->json($response->json(), $response->status());
    }

    public function posPagar(Request $request)
    {
        $request->session()->flash('ok', 'Compra realizada, um email de confirmação e o código de rastreamento do pacote sera enviado para você assim que o pagamento for confirmado.');
        return redirect()->route("home");
    }

    public function PagSeguroCheckout(Request $request)
    {

        $token = env('PAGSEGURO_TOKEN'); //recebendo o token do pagbank

        //iniciando variaveis
        $semEstoque = false;
        $pedido = new Pedido;
        $referenceId = uniqid('', true);
        $carrinho = session('cart', []);
        $produtoSemEstoque = "";
        $produtoController = new ProdutoController;

        //buscando inputs
        $vlrFrete = ($request->input('valorFrete') * 100);
        $total = $request->input('valorFrete');
        $idFrete = $request->input('idFrete');
        $nomeCliente = $request->input('nome') . " " . $request->input('Sobrenome');
        $ddd = $request->input('ddd');
        $telefone = $request->input('telefone');
        $emailCliente = $request->input('email');
        $cpf = str_replace(['.', '-'], '', $request->input('cpf'));
        $logradouro = $request->input('logradouro');
        $bairro = $request->input('bairro');
        $numero = $request->input('numero');
        $cidade = $request->input('cidade');
        $uf = $request->input('uf');
        $cep = $request->input('cep');

        //testando estoque
        foreach ($carrinho as $item) {
            $quantidadeCarrinho = $item['quantidade'];
            $estoque = $produtoController->buscarEstoqueQuantidade($item['id'], $item['numero']);
            $quantidadeEstoque = $estoque[0]->quantidade;
            if ($quantidadeCarrinho > $quantidadeEstoque) {
                $semEstoque = true;
                $produtoSemEstoque = $item;
                break;
            }
        };

        //verificando estoque
        if ($semEstoque) {

            foreach ($carrinho as $key => $item) {
                if ($item == $produtoSemEstoque) {
                    unset($carrinho[$key]);
                    session(["cart" => $carrinho]);
                    break;
                }
            }
            $request->session()->flash("err", "Quantidade do carrinho maior que a quantidade em estoque do produto " . $produtoSemEstoque['nome'] . "/" . $produtoSemEstoque['numero']);
            return redirect()->route("home");
        } else {

            //iniciando corpo da requisição
            $customerPhone = [
                'country' => '+55',
                'area' => $ddd,
                'number' => $telefone,
            ];

            $customerName = $nomeCliente;
            $customerEmail = $emailCliente;
            $customerTaxId = $cpf;

            $shippingAddress = [
                'street' => $logradouro,
                'number' => $numero,
                'locality' => $bairro,
                'city' => $cidade,
                'region_code' => $uf,
                'country' => 'BRA',
                'postal_code' => $cep,
            ];

            $shippingAmount = $vlrFrete;

            $redirectUrl = 'https://urbeatstore.com.br/pagbank/pospagar';
            $notification_urls = [
                'https://urbeatstore.com.br/webhook/pagbank'
                // Adicione mais URLs conforme necessário
            ];
            $payment_notification_urls = [
                'https://urbeatstore.com.br/webhook/pagbank'
                // Adicione mais URLs conforme necessário
            ];

            // Construa o corpo dinamicamente usando as variáveis
            $bodyData = [
                'reference_id' => $referenceId,
                'customer' => [
                    'phone' => $customerPhone,
                    'name' => $customerName,
                    'email' => $customerEmail,
                    'tax_id' => $customerTaxId,
                ],
                'shipping' => [
                    'address' => $shippingAddress,
                    'type' => 'FIXED',
                    'address_modifiable' => false,
                    'amount' => $shippingAmount,
                ],
                'customer_modifiable' => false,
                'redirect_url' => $redirectUrl,
                'notification_urls' => $notification_urls,
                'payment_notification_urls' => $payment_notification_urls,
            ];



            foreach ($carrinho as $item) {
                $produto = Produto::find($item['id']);
                $total += ($item['valor'] * $item['quantidade']);
                $item = [
                    'reference_id' => $item['id'],
                    'name' => $produto['nome'],
                    'description' => $produto['nome'],
                    'quantity' => $item['quantidade'],
                    'unit_amount' => (($item['valor']-($item['valor']*($item['desconto']/100))) * 100),
                ];
                $bodyData['items'][] = $item;
            }

            // Convertendo o array em JSON para ser usado como corpo da requisição
            $body = json_encode($bodyData);

            $client = new \GuzzleHttp\Client();

            try {
                // Enviando a requisição
                $response = $client->request('POST', 'https://sandbox.api.pagseguro.com/checkouts', [
                    'body' => $body,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-type' => 'application/json',
                        'accept' => 'application/json',
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'ProdutoController.salvarUsuario', 'message' => $e->getMessage()]);
                $request->session()->flash('err', 'erro ao tentar iniciar pagamento');
                session()->forget('cart');
                return redirect()->route('home');
            }

            //buscando o retorno da requisição
            $jsonResponse = $response->getBody();
            $data = json_decode($jsonResponse, true);
            $idCheckout = $data['id'];

            // Verifique se o campo 'links' existe e se contém o link 'PAY'
            if (isset($data['links']) && is_array($data['links'])) {
                foreach ($data['links'] as $link) {
                    if (isset($link['rel']) && $link['rel'] === 'PAY' && isset($link['href'])) {

                        //criar Usuario, Pedido e enviar o usuario para o pagseguro
                        //criar usuario
                        $usuarioPedido = new Usuario;
                        $usuarioCadastrado = Usuario::where('email',  $emailCliente)->first(); //buscando cadastro do usuario no sistema
                        if ($usuarioCadastrado) {
                            $usuarioPedido = $usuarioCadastrado;
                            $usuarioPedido->telefone = $ddd . '' . $telefone;
                            DB::beginTransaction();
                            $usuarioPedido->save();
                            DB::commit();
                        } else {
                            try {
                                $usuarioPedido->email = $emailCliente;
                                $usuarioPedido->cpf = $cpf;
                                $usuarioPedido->password = HASH::make($cpf);
                                $usuarioPedido->nome = $nomeCliente;
                                $usuarioPedido->isAdmin = 0;
                                $usuarioPedido->ativo = 0;
                                $usuarioPedido->telefone = $ddd . '' . $telefone;
                                DB::beginTransaction();
                                $usuarioPedido->save();
                                DB::commit();
                            } catch (\Exception $e) {
                                Log::error("ERRO", ['file' => 'ProdutoController.PagSeguroCheckout', 'message' => $e->getMessage()]);
                                DB::rollback();
                                $request->session()->flash('err', 'erro ao tentar iniciar pagamento');
                                session()->forget('cart');
                                return redirect()->route('home');
                            }
                        }
                        //guardando endereço
                        $endereco = new Endereco;
                        $endereco->logradouro = $logradouro;
                        $endereco->bairro = $bairro;
                        $endereco->numero = $numero;
                        $endereco->cidade = $cidade;
                        $endereco->uf = $uf;
                        $endereco->cep = $cep;
                        try {
                            DB::beginTransaction();
                            $endereco->save();
                            DB::commit();
                        } catch (\Exception $e) {
                            Log::error("ERRO", ['file' => 'ProdutoController.PagSeguroCheckout', 'message' => $e->getMessage()]);
                            DB::rollback();
                            $request->session()->flash('err', 'erro ao tentar iniciar pagamento');
                            session()->forget('cart');
                            return redirect()->route('home');
                        }

                        //criar pedido
                        $dateTimeAtual = new DateTime();
                        $dataHoraAtual = $dateTimeAtual->format('Y-m-d H:i:s');
                        $pedido->datapedido = $dataHoraAtual;
                        $pedido->status = 'Pendente pagamento';
                        $pedido->status_checkout = 'ACTIVE';
                        $pedido->reference_id = $referenceId;
                        $pedido->cod_checkout = $idCheckout;
                        $pedido->usuario_id = $usuarioPedido->id;
                        $pedido->endereco_id = $endereco->id;
                        $pedido->cod_entrega = $idFrete;
                        $pedido->total = $total;
                        try {
                            DB::beginTransaction();
                            $pedido->save();
                            DB::commit();
                        } catch (\Exception $e) {
                            Log::error("ERRO", ['file' => 'ProdutoController.PagSeguroCheckout', 'message' => $e->getMessage()]);
                            DB::rollback();
                            $request->session()->flash('err', 'erro ao tentar iniciar pagamento');
                            session()->forget('cart');
                            return redirect()->route('home');
                        }

                        //salvando itens
                        foreach ($carrinho as $item) {
                            $iten = new ItensPedido;
                            $iten->quantidade = $item['quantidade'];
                            $iten->numero = $item['numero'];
                            $iten->pedido_id = $pedido->id;
                            $iten->produto_id = $item['id'];
                            $iten->valor = $item['valor'];
                            try {
                                DB::beginTransaction();
                                $iten->save();
                                DB::commit();
                            } catch (\Exception $e) {
                                Log::error("ERRO", ['file' => 'ProdutoController.PagSeguroCheckout', 'message' => $e->getMessage()]);
                                DB::rollback();
                                $request->session()->flash('err', 'erro ao tentar iniciar pagamento');
                                session()->forget('cart');
                                return redirect()->route('home');
                            }
                        }

                        session()->forget('cart');

                        $PAYlink = $link['href'];
                        return redirect($PAYlink);
                        break;
                    }
                }
            } else {
                Log::error("ERRO", ['file' => 'ProdutoController.PagSeguroCheckout', 'message' => 'PagSeguro não respondeu']);
                $request->session()->flash('err', 'erro ao tentar iniciar pagamento');
                session()->forget('cart');
                return redirect()->route('home');
            }
        }
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        $melhorEnvio = new MelhorEnvioController;
        $produtoController = new ProdutoController;

        if (isset($payload['reference_id'])) {
            $referenceId = $payload['reference_id'];
            Log::info('Recebeu webhook', ['reference_id' => $referenceId]);

            // Verifica se 'charges' e 'status' existem antes de acessá-los
            if (isset($payload['charges'][0]['status'])) {
                $status_pagamento = $payload['charges'][0]['status'];
                Log::info('Recebeu webhook', ['reference_id' => $referenceId, 'status_pagamento' => $status_pagamento]);
            }

            // Verifica se 'status' existe antes de acessá-lo
            // if (isset($payload['status'])) {
            //     $status_checkout = $payload['status'];
            // }

            $pedido = Pedido::where('reference_id', $referenceId)->first();

            if ($pedido) {
                // Atualizando o status no sistema
                $pedido->status_pag = $status_pagamento;
                $pedido->save();

                if ($pedido->status_pag == 'DECLINED' or $pedido->status_pag == 'CANCELED') {
                    $pedido->status = 'Cancelado';
                    $pedido->save();
                    $produtoController->email($pedido->id);
                    // $this->InativarCheckout($pedido->cod_checkout);
                } else if ($pedido->status_pag == 'PAID') {
                    $pedido->status = 'Pagamento aprovado';
                    $pedido->save();
                    $itens_pedido = ItensPedido::where('pedido_id',  $pedido->id)->get();
                    foreach ($itens_pedido as $item) {
                        $estoque = Estoque::where('produto_id', $item->produto_id)->where('numero', $item->numero)->first();
                        $estoque->quantidade -= $item->quantidade;
                        $estoque->save();
                    }
                    if ($pedido->cod_entrega != 'Gratis') {
                        //lançar frete
                        $resposta = $melhorEnvio->lancarFrete($referenceId);
                        // Verifique se a resposta é um JSON válido
                        $respostaJson = json_decode($resposta);
                        $codEnvio = $respostaJson->id;

                        // Verifique se a decodificação do JSON foi bem-sucedida e se a chave "protocol" existe
                        if ($respostaJson && isset($respostaJson->protocol)) {
                            $respostaPagamento = $melhorEnvio->pagarFrete($codEnvio);
                            $respostaPagamentoJson = json_decode($respostaPagamento);
                            if ($respostaPagamentoJson && isset($respostaPagamentoJson->purchase->status)) {
                                $pedido->cod_entrega = $respostaJson->protocol;
                                $pedido->cod_envio = $codEnvio;
                                $pedido->status_env = 'Envio iniciado';
                                $pedido->save();
                                $respostaEtiqueta = $melhorEnvio->gerarEtiqueta($codEnvio);
                                Log::info("SUCESSO", ['file' => 'PagSeguroController.Handle', 'message' => 'Envio iniciado com sucesso']);
                                $produtoController->email($pedido->id);
                            } else {
                                $pedido->cod_entrega = $respostaJson->protocol;
                                $pedido->status_env = 'Erro na geração de etiqueta';
                                $pedido->status = 'Pendente de acompanhamento';
                                $pedido->cod_envio = $codEnvio;
                                $pedido->save();
                            }
                        } else {
                            Log::error("ERRO", ['file' => 'PagSeguroController.Handle', 'message' => 'Não conseguiu gerar envio']);
                            $pedido->status_env = 'Erro no lançamento';
                            $pedido->status = 'Pendente de acompanhamento';
                            $pedido->save();
                        }
                    } else {
                        $pedido->status_env = 'Preparando encomenda';
                        $pedido->save();
                        $produtoController->email($pedido->id);
                    }
                }
                Log::info('Webhook processado com sucesso', ['reference_id' => $referenceId]);
                // Se necessário, você pode adicionar lógica adicional para lidar com outros eventos, como pagamento confirmado, cancelado, etc.
            }
        }
        // Responda para confirmar o recebimento do Webhook
        return response()->json(['status' => 'Webhook recebido com sucesso']);
    }




    //DESUSO

    // public function PagSeguroCriarApp(Request $request)
    // {
    //     $token = env('PAGSEGURO_TOKEN');
    //     $produtoController = new ProdutoController;

    //     $client = new \GuzzleHttp\Client();

    //     try {
    //         $response = $client->request('POST', 'https://sandbox.api.pagseguro.com/oauth2/application', [
    //             'body' => '{"name":"Urbeat","site":"https://urbeatstore.com.br","redirect_uri":"https://urbeatstore.com.br/pagbank/auth"}',
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . $token,
    //                 'accept' => 'application/json',
    //                 'content-type' => 'application/json',
    //             ],
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error("ERRO", ['file' => 'ProdutoController.PagSeguroCriarApp', 'message' => $e->getMessage()]);
    //         $message = 'Erro ao tentar realizar pagamento!';
    //         $status = 'err';

    //         $request->session()->flash($status, $message);
    //         return redirect()->route('home');
    //     }

    //     $teste = 1;

    //     $responseJson = json_decode($response->getBody(), true);

    //     // Verifique se os tokens foram obtidos com sucesso
    //     if (isset($responseJson['client_id']) && isset($responseJson['client_secret'])) {

    //         $produtoController->AtualizarVariaveisAmbiente('PAGSEGURO_CLIENT_ID', $responseJson['client_id']);
    //         $produtoController->AtualizarVariaveisAmbiente('PAGSEGURO_CLIENT_SECRET', $responseJson['client_secret']);

    //         // Informar ao usuário que os tokens foram armazenados com sucesso
    //         return "Aplicativo criado com sucesso.";
    //     } else {
    //         // Informar ao usuário que houve um problema ao obter os tokens
    //         return "Erro: Aplicativo não foi criado.";
    //     }
    // }

    // public function redirectToAuthorization()
    // {
    //     $client_id = env('PAGSEGURO_CLIENT_ID');
    //     $redirect_uri = 'https://urbeatstore.com.br/pagbank/auth';
    //     $scope = env('PAGSEGURO_SCOPE'); // Adicione outras permissões conforme necessário
    //     $authorizationUrl = "https://connect.sandbox.pagseguro.uol.com.br/oauth2/authorize?";
    //     $authorizationUrl .= "response_type=code&client_id={$client_id}&redirect_uri={$redirect_uri}&scope={$scope}&state=xyz";

    //     return redirect()->away($authorizationUrl);
    // }

    // public function PagSeguroGetToken(String $code)
    // {

    //     $token = env('PAGSEGURO_TOKEN');
    //     $clientId = env('PAGSEGURO_CLIENT_ID');
    //     $clientSecret = env('PAGSEGURO_CLIENT_SECRET');
    //     $produtoController = new ProdutoController;

    //     $client = new \GuzzleHttp\Client();

    //     try {
    //         $response = $client->request('POST', 'https://sandbox.api.pagseguro.com/oauth2/token', [
    //             'json' => [
    //                 'grant_type' => 'authorization_code',
    //                 'code' => $code,
    //                 'redirect_uri' => 'https://urbeatstore.com.br/pagbank/auth',
    //             ],
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . $token,
    //                 'X_CLIENT_ID' => $clientId,
    //                 'X_CLIENT_SECRET' => $clientSecret,
    //                 'accept' => 'application/json',
    //                 'content-type' => 'application/json',
    //             ],
    //         ]);
    //     } catch (\EXCEPTION $e) {
    //         Log::error("ERRO", ['file' => 'ProdutoController.PagSeguroGetToken', 'message' => $e->getMessage()]);
    //         return redirect()->route('home');
    //     }


    //     $responseJson = json_decode($response->getBody(), true);

    //     // Verifique se os tokens foram obtidos com sucesso
    //     if (isset($responseJson['access_token']) && isset($responseJson['refresh_token']) && isset($responseJson['account_id'])) {

    //         $produtoController->AtualizarVariaveisAmbiente('PAGSEGURO_ACCESS_TOKEN', $responseJson['access_token']);
    //         $produtoController->AtualizarVariaveisAmbiente('PAGSEGURO_REFRESH_TOKEN', $responseJson['refresh_token']);
    //         $produtoController->AtualizarVariaveisAmbiente('PAGSEGURO_ACCOUNT_ID', $responseJson['account_id']);

    //         // Informar ao usuário que os tokens foram armazenados com sucesso
    //         return "Tokens salvos com sucesso";
    //     } else {
    //         // Informar ao usuário que houve um problema ao obter os tokens
    //         return "Erro: Tokens não vieram.";
    //     }
    // }

    // public function pagar(Request $request)
    // {
    //     $data = [];
    //     $carrinho = session('cart', []);
    //     $data = ['cart' => $carrinho];

    //     return view("pagar", $data);
    // }

    // public function pagamento(Request $request)
    // {
    //     $data = [];
    //     return view("payment.pagamento", $data);
    // }

    // public function keyController(Request $request)
    // {
    //     $data = [];
    //     return view("payment.KeyController", $data);
    // }



    // public function PagSeguroAuth(Request $request)
    // {
    //     // Verifica se o código está presente na URL
    //     if ($request->has('code')) {
    //         $code = $request->input('code');

    //         // Verifique se os tokens foram obtidos com sucesso
    //         if (isset($code)) {
    //             // Informe ao usuário que o código de autorização foi armazenado com sucesso
    //             // Agora, chame a função getToken
    //             $tokenResponse = $this->PagSeguroGetToken($code);
    //             // Se precisar fazer algo com a resposta da função getToken, pode fazer aqui
    //             return $tokenResponse; // ou alguma mensagem personalizada
    //         } else {
    //             // Informar ao usuário que houve um problema ao obter os tokens
    //             return "Erro: algo deu errado.";
    //         }
    //     }
    // }
}
