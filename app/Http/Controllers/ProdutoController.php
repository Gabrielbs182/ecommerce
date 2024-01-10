<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Categoria;
use App\Models\ItensPedido;
use App\Models\Pedido;
use App\Models\Endereco;
use App\Services\VendaService;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DateTime;

class ProdutoController extends Controller
{
    //

    public function buscarProduto($id)
    {
        $produto = Produto::where('id', '=', $id)->get();
        return $produto;
    }

    public function buscarCarrinho()
    {
        $carrinho = session('cart', []);
        return $carrinho;
    }

    public function redirectToAuthorization()
    {
        $client_id = env('PAGSEGURO_CLIENT_ID');
        $redirect_uri = 'https://urbeatstore.com.br/pagbank/auth';
        $scope = env('PAGSEGURO_SCOPE'); // Adicione outras permissões conforme necessário
        $authorizationUrl = "https://connect.sandbox.pagseguro.uol.com.br/oauth2/authorize?";
        $authorizationUrl .= "response_type=code&client_id={$client_id}&redirect_uri={$redirect_uri}&scope={$scope}&state=xyz";

        return redirect()->away($authorizationUrl);
    }

    public function atualizarQuantidadeCarrinho()
    {
        $carrinho = session('cart', []);
        $quantidadeCarrinho = count($carrinho);

        return response()->json(['quantidadeCarrinho' => $quantidadeCarrinho]);
    }


    public function buscarEstoque($id)
    {
        $estoque = Estoque::where('produto_id', '=', $id)->get();
        return $estoque;
    }

    public function buscarEstoqueQuantidade($id, $numero)
    {
        $estoque = Estoque::where('produto_id', $id)->where('numero', $numero)->get();
        return $estoque;
    }

    public function index()
    {
        $data = [];
        $listaProdutos = Produto::all();
        $data["lista"] = $listaProdutos;
        return view("home", $data);
    }

    public function categoria($idcategoria = 0)
    {
        $data = [];
        $listaCategorias = Categoria::all();
        $queryProduto = Produto::limit(4);
        if ($idcategoria != 0) {
            $queryProduto->where("categoria_id", $idcategoria);
        }
        $listaProduto = $queryProduto->get();
        $data["lista"] = $listaProduto;
        $data["listaCategoria"] = $listaCategorias;
        return view("categoria", $data);
    }

    public function buscarItemCarrinho($id, $numero)
    {
        $carrinho = session('cart', []);
        // Verifique se o produto já está no carrinho
        $produtoExistente = 1;

        foreach ($carrinho as  $item) {
            if ($item->id == $id and $item->numero == $numero) {
                $produtoExistente = $item;
                break;
            }
        }

        return $produtoExistente;
    }

    public function adicionarCarrinho($id, $quantidade, $numero)
    {
        $prod = Produto::find($id);
        if ($prod) {
            $carrinho = session('cart', []);
            // Verifique se o produto já está no carrinho
            $produtoExistente = null;
            foreach ($carrinho as $key => $item) {
                if ($item->id == $prod->id and $item->numero == $numero) {
                    $produtoExistente = $key;
                    break;
                }
            }
            if ($produtoExistente !== null) {
                // Se o produto já existe no carrinho, adicione a quantidade à quantidade existente
                $produtoCarrinho = $carrinho[$produtoExistente];
                $produtoCarrinho->quantidade += $quantidade;
                $estoque = $this->buscarEstoqueQuantidade($produtoCarrinho->id, $numero);
            } else {
                // Caso contrário, adicione o produto ao carrinho
                $prod->quantidade = $quantidade;
                $prod->numero = $numero;
                array_push($carrinho, $prod);
            }
            echo "<br>";
            var_dump($carrinho);
            session(['cart' => $carrinho]);
        }
        //return redirect()->route("home");
    }

    public function verCarrinho(Request $request)
    {
        $transporte = explode("/", $request->input("frete"));
        $carrinho = session('cart', []);

        if ($transporte[0] != 0) {
            $data = [
                'cart' => $carrinho, 'idFrete' => $transporte[0], 'cep' => $request->input("cep"),
                'nomeFrete' => $transporte[1], 'valor' => $transporte[2]
            ];
        } else {
            $valorFrete = 0;
            $data = [
                'cart' => $carrinho, 'idFrete' => 'Gratis', 'valor' => $valorFrete
            ];
        }

        return view("carrinho", $data);
    }

    public function excluirCarrinho($indice)
    {
        $carrinho = session('cart', []);
        if (isset($carrinho[$indice])) {
            unset($carrinho[$indice]);
            session(["cart" => $carrinho]);
            return $carrinho;
        } else {
            return $carrinho;
        }
    }

    public function AtualizarVariaveisAmbiente(String $chave, String $valor)
    {

        // Verificações de parâmetros
        if (empty($chave) || empty($valor)) {
            // Trate como um erro ou apenas retorne, dependendo da lógica do seu aplicativo
            return "Erro: Chave ou valor vazios.";
        }

        // Buscando o conteúdo do .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        // Atualizar ou adicionar as chaves no arquivo .env
        if (strpos($envContent, $chave) !== false) {

            // Atualizar o valor se já existir
            $envContent = preg_replace('/(' . $chave . ')=(.*)/', '' . $chave . '=' . $valor, $envContent);
        } else {
            // Adicionar uma nova linha com a chave Authorization e os tokens
            $envContent .= "\n" . $chave . "=" . $valor;
        }

        // Salvar as alterações no arquivo .env
        file_put_contents($envPath, $envContent);
    }

    public function PagSeguroCheckout(Request $request)
    {

        $token = env('PAGSEGURO_TOKEN');

        $semEstoque = false;

        $vlrFrete = ($request->input('valorFrete') * 100);
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
        $pedido = new Pedido;
        $referenceId = uniqid('', true);

        $carrinho = session('cart', []);

        $produtoSemEstoque = "";

        foreach ($carrinho as $item) {
            $quantidadeCarrinho = $item['quantidade'];
            $estoque = $this->buscarEstoqueQuantidade($item['id'], $item['numero']);
            $quantidadeEstoque = $estoque[0]->quantidade;
            if ($quantidadeCarrinho > $quantidadeEstoque) {
                $semEstoque = true;
                $produtoSemEstoque = $item;
                break;
            }
        };

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
            $payment_notification_urls = 'https://urbeatstore.com.br/pagbank/handle';
            $notification_urls = 'https://urbeatstore.com.br/pagbank/handle';

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
                //'payment_notification_urls' => $payment_notification_urls,
            ];



            foreach ($carrinho as $item) {
                $produto = Produto::find($item['id']);
                $item = [
                    'reference_id' => $item['id'],
                    'name' => $produto['nome'],
                    'description' => $produto['nome'],
                    'quantity' => $item['quantidade'],
                    'unit_amount' => ($item['valor'] * 100),
                    // Adicione outros campos conforme necessário
                ];
                $bodyData['items'][] = $item;
            }

            // Converta o array em JSON para ser usado como corpo da requisição
            $body = json_encode($bodyData);

            $client = new \GuzzleHttp\Client();

            // Substitua o corpo na sua chamada de requisição
            $response = $client->request('POST', 'https://sandbox.api.pagseguro.com/checkouts', [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-type' => 'application/json',
                    'accept' => 'application/json',
                ],
            ]);

            $jsonResponse = $response->getBody();
            $data = json_decode($jsonResponse, true);
            $idCheckout = $data['id'];

            // Verifique se o campo 'links' existe e se contém o link 'SELF'
            if (isset($data['links']) && is_array($data['links'])) {
                foreach ($data['links'] as $link) {
                    if (isset($link['rel']) && $link['rel'] === 'PAY' && isset($link['href'])) {

                        //criar Usuario, Pedido e enviar o usuario para o pagseguro
                        //criar usuario
                        $usuarioPedido = new Usuario;
                        $usuarioCadastrado = Usuario::where('email',  $emailCliente)->first(); //buscando cadastro do usuario no sistema
                        if ($usuarioCadastrado) {
                            $usuarioPedido = $usuarioCadastrado;
                        } else {
                            try {
                                $usuarioPedido->email = $emailCliente;
                                $usuarioPedido->cpf = $cpf;
                                $usuarioPedido->password = HASH::make($cpf);
                                $usuarioPedido->nome = $nomeCliente;
                                $usuarioPedido->isAdmin = 0;
                                $usuarioPedido->ativo = 0;
                                // Armazenando usuario na sessao
                                $usuarioPedido->save();
                                $request->session()->put('usuarioPedido', $usuarioPedido);
                            } catch (\Exception $e) {
                                Log::error("ERRO", ['file' => 'ProdutoController.salvarUsuario', 'message' => $e->getMessage()]);
                                DB::rollback();
                                $request->session()->flash(["status" => 'err', 'message' => 'erro ao tentar iniciar pagamento']);
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
                        $endereco->save();
                        $request->session()->put('enderecoPedido', $endereco);

                        //criar pedido
                        $dateTimeAtual = new DateTime();
                        $dataHoraAtual = $dateTimeAtual->format('Y-m-d H:i:s');
                        $pedido->datapedido = $dataHoraAtual;
                        $pedido->status = 'Iniciado';
                        $pedido->reference_id = $referenceId;
                        $pedido->cod_checkout = $idCheckout;
                        $pedido->usuario_id = $usuarioPedido->id;
                        $pedido->endereco_id = $endereco->id;
                        $pedido->save();
                        $request->session()->put('pedido', $pedido);

                        $PAYlink = $link['href'];
                        return redirect($PAYlink);
                        break;
                    }
                }
            } else {
                echo 'PagSeguro não respondeu.';
            }
        }
    }

    public function handle(Request $request)
    {
        $payload = $request->all();


        // Aqui você pode validar a autenticidade da notificação, verificar as chaves de segurança, etc.

        // Exemplo de como acessar dados específicos do payload
        $referenceId = $payload['reference_id'];
        $status = $payload['charges'][0]['status'];

        Log::info('Recebeu webhook', ['reference_id' => $referenceId, 'status' => $status]);

        // Agora, atualize o status de pagamento no seu sistema
        $pedido = Pedido::where('cod_checkout', $referenceId)->first();

        if ($pedido) {
            // Atualize o status do pedido com base no status recebido do PagSeguro
            $pedido->status_pag = $status;
            $pedido->save();

            Log::info('Webhook processado com sucesso', ['reference_id' => $referenceId, 'status' => $status]);


            // Se necessário, você pode adicionar lógica adicional para lidar com outros eventos, como pagamento confirmado, cancelado, etc.
        }

        // Responda para confirmar o recebimento do Webhook
        return response()->json(['status' => 'Webhook recebido com sucesso']);
    }

    public function PagSeguroCriarApp()
    {
        $token = env('PAGSEGURO_TOKEN');

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://sandbox.api.pagseguro.com/oauth2/application', [
            'body' => '{"name":"Urbeat","site":"https://urbeatstore.com.br","redirect_uri":"https://urbeatstore.com.br/pagbank/auth"}',
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);

        $responseJson = json_decode($response->getBody(), true);

        // Verifique se os tokens foram obtidos com sucesso
        if (isset($responseJson['client_id']) && isset($responseJson['client_secret'])) {

            $this->AtualizarVariaveisAmbiente('PAGSEGURO_CLIENT_ID', $responseJson['client_id']);
            $this->AtualizarVariaveisAmbiente('PAGSEGURO_CLIENT_SECRET', $responseJson['client_secret']);

            // Informar ao usuário que os tokens foram armazenados com sucesso
            return "Aplicativo criado com sucesso.";
        } else {
            // Informar ao usuário que houve um problema ao obter os tokens
            return "Erro: Aplicativo não foi criado.";
        }
    }



    public function PagSeguroGetToken(String $code)
    {

        $token = env('PAGSEGURO_TOKEN');
        $clientId = env('PAGSEGURO_CLIENT_ID');
        $clientSecret = env('PAGSEGURO_CLIENT_SECRET');

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://sandbox.api.pagseguro.com/oauth2/token', [
            'json' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => 'https://urbeatstore.com.br/pagbank/auth',
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'X_CLIENT_ID' => $clientId,
                'X_CLIENT_SECRET' => $clientSecret,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);


        $responseJson = json_decode($response->getBody(), true);

        // Verifique se os tokens foram obtidos com sucesso
        if (isset($responseJson['access_token']) && isset($responseJson['refresh_token']) && isset($responseJson['account_id'])) {

            $this->AtualizarVariaveisAmbiente('PAGSEGURO_ACCESS_TOKEN', $responseJson['access_token']);
            $this->AtualizarVariaveisAmbiente('PAGSEGURO_REFRESH_TOKEN', $responseJson['refresh_token']);
            $this->AtualizarVariaveisAmbiente('PAGSEGURO_ACCOUNT_ID', $responseJson['account_id']);

            // Informar ao usuário que os tokens foram armazenados com sucesso
            return "Tokens salvos com sucesso";
        } else {
            // Informar ao usuário que houve um problema ao obter os tokens
            return "Erro: Tokens não vieram.";
        }
    }

    public function PagSeguroAuth(Request $request)
    {
        // Verifica se o código está presente na URL
        if ($request->has('code')) {
            $code = $request->input('code');

            // Verifique se os tokens foram obtidos com sucesso
            if (isset($code)) {

                // Informe ao usuário que o código de autorização foi armazenado com sucesso
                // Agora, chame a função getToken
                $tokenResponse = $this->PagSeguroGetToken($code);

                // Se precisar fazer algo com a resposta da função getToken, pode fazer aqui

                return $tokenResponse; // ou alguma mensagem personalizada
            } else {
                // Informar ao usuário que houve um problema ao obter os tokens
                return "Erro: algo deu errado.";
            }
        }
    }


    public function posPagar()
    {
        return redirect()->route("home");
    }

    public function finalizar(Request $request)
    {
        $prods = session('cart', []);
        $vendaService = new VendaService();
        $result = $vendaService->finalizarVenda($prods, Auth::user());
        echo $result;
        //preciso integrar aqui esse backend.
        $request->session()->flash($result["status"], $result["message"]);
        return redirect()->route("ver_carrinho");
    }

    public function historico()
    {
        $data = [];
        $pedidos = Pedido::where('usuario_id', Auth::user()->id)->get();

        if (isset($pedidos)) {
            $data["lista"] = $pedidos;
        }

        return view('historico', $data);
    }

    public function detalhes($idproduto)
    {
        $produto = Produto::where('id', $idproduto)->get();
        $data["produto"] = $produto;
        return view("detalhamento", $data);
    }

    public function detalhe(Request $request)
    {
        $idpedido = $request->input("idpedido");
        $listaItens = ItensPedido::join("produtos", "produtos.id", "=", "itens_pedidos.produto_id")
            ->where("pedido_id", $idpedido)
            ->get(['itens_pedidos.*', 'itens_pedidos.valor as valoritem', 'produtos.*']);

        $data["listaItens"] = $listaItens;

        return view("detalhes", $data);
    }

    public function pagar(Request $request)
    {
        $data = [];
        $carrinho = session('cart', []);
        $data = ['cart' => $carrinho];

        $sessionCode = \PagSeguro\Services\Session::create(
            $this->getCredential()
        );
        $IDSession = $sessionCode->getResult();
        $data["sessionId"] = $IDSession;

        return view("pagar", $data);
    }

    public function pagamento(Request $request)
    {
        $data = [];
        return view("payment.pagamento", $data);
    }

    public function keyController(Request $request)
    {
        $data = [];
        return view("payment.KeyController", $data);
    }
}
