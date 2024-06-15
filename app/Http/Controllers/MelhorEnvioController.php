<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Pedido;
use App\Models\Endereco;
use App\Models\ItensPedido;
use App\Models\Usuario;
use App\Models\Produto;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Env;

class MelhorEnvioController extends Controller
{
    // public function authorize()
    // {
    //     // Redirecionar para a URL de autorização do Melhor Envio
    //     // Isso geralmente envolve redirecionar o usuário para a URL fornecida pelo Melhor Envio com os parâmetros apropriados
    // }


    //FUNÇÕES PASSIVAS

    public function obterDadosCEP($cep)
    {
        //RETORNAR ENDEREÇO A PARTIR DE UM CEP
        $client = new Client();
        $response = $client->get("https://viacep.com.br/ws/{$cep}/json");

        if ($response->getStatusCode() == 200) {
            $dadosCep = json_decode($response->getBody(), true);
            return response()->json($dadosCep);
        } else {
            return response()->json(['erro' => 'Erro na requisição do CEP'], 500);
        }
    }

    public function Authorization()
    {
        //BUSCAR AUTORIZAÇÃO DO MELHOR ENVIO NO ARQUIVO DE VARIAVEIS DE AMBIENTE
        $apiResponse = ENV('AUTHORIZATION');
        $responseData = json_decode($apiResponse, true);
        return $responseData;
    }

    public function pagarFrete($order)
    {
        //buscando autorização
        $responseData = $this->Authorization();
        $client = new \GuzzleHttp\Client();

        try {
            //TENTANDO DAR POST NO MELHOR ENVIO COM A AUTORIZAÇÃO ATUAL DO SISTEMA.
            $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/checkout', [
                'body' => json_encode(['orders' => [$order]]),
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $responseData['access_token'],
                    'Content-Type' => 'application/json',
                    env('USER_AGENT'),
                ],
            ]);
            return $response->getBody();
        } catch (\Exception $e) {
            try {
                //SE A AUTORIZAÇÃO FOR NEGADA, EU TENTO ATUALIZAR O TOKEN PARA ENVIAR OUTRA O POST NOVAMENTE
                $this->refreshToken();
                $responseData = $this->Authorization();
                $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/checkout', [
                    'body' => json_encode(['orders' => [$order]]),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $responseData['access_token'],
                        'Content-Type' => 'application/json',
                        env('USER_AGENT'),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'MelhorEnvioController.pagarFrete', 'message' => $e->getMessage()]);
            }
        }
    }

    public function gerarEtiqueta($order)
    {
        //buscando autorização
        $responseData = $this->Authorization();
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/generate', [
                'body' => json_encode(['orders' => [$order]]),
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $responseData['access_token'],
                    'Content-Type' => 'application/json',
                    env('USER_AGENT'),
                ],
            ]);
            return $response->getBody();
        } catch (\Exception $e) {
            try {
                $this->refreshToken();
                $responseData = $this->Authorization();
                $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/generate', [
                    'body' => json_encode(['orders' => [$order]]),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $responseData['access_token'],
                        'Content-Type' => 'application/json',
                        env('USER_AGENT'),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'MelhorEnvioController.gerarEtiqueta', 'message' => $e->getMessage()]);
            }
        }
    }

    public function buscarRastreamento($order)
    {
        //buscando autorização
        $responseData = $this->Authorization();
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/tracking', [
                'body' => json_encode(['orders' => [$order]]),
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $responseData['access_token'],
                    'Content-Type' => 'application/json',
                    env('USER_AGENT'),
                ],
            ]);
            return $response->getBody();
        } catch (\Exception $e) {
            try {
                $this->refreshToken();
                $responseData = $this->Authorization();
                $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/tracking', [
                    'body' => json_encode(['orders' => [$order]]),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $responseData['access_token'],
                        'Content-Type' => 'application/json',
                        env('USER_AGENT'),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'MelhorEnvioController.buscarRastreamento', 'message' => $e->getMessage()]);
            }
        }
    }

    public function lancarFrete($reference_id)
    {

        try {
            //buscando autorização
            $responseData = $this->Authorization();
            $pedido = Pedido::where('reference_id', $reference_id)->first();

            if (isset($pedido)) {
                $endereco = Endereco::find($pedido->endereco_id);
                $usuario = Usuario::find($pedido->usuario_id);
            }
            $respostaApi = $this->calcularFrete2($endereco->cep, $pedido->id);
            // Array para armazenar os volumes associados a cada ID de transporte
            $volumesPorTransporte = [];

            // Iterar sobre a resposta da API
            foreach ($respostaApi as $transporte) {
                // Verificar se o transporte possui volumes (packages)
                if (isset($transporte['packages']) && is_array($transporte['packages'])) {
                    // Obter o ID do transporte
                    $idTransporte = $transporte['id'];

                    // Armazenar os volumes associados ao ID do transporte
                    $volumesPorTransporte[$idTransporte] = $transporte['packages'];
                }
            }

            $volumes = $volumesPorTransporte[$pedido->cod_entrega];

            if (count($volumes) > 1 && in_array($pedido->cod_entrega, [1, 2, 17])) {

                // Criar uma instância do GuzzleHttp\Client
                $client = new Client();

                // Dados para a requisição
                $requestData = [
                    'from' => [
                        'name' => ENV('MELHORENVIO_NAME'),
                        'phone' => ENV('MELHORENVIO_PHONE'),
                        'email' => ENV('MELHORENVIO_EMAIL'),
                        'document' => ENV('MELHORENVIO_DOCUMENT'),
                        'state_register' => ENV('MELHORENVIO_STATE_REGISTER'),
                        'address' => ENV('MELHORENVIO_ADDRESS'),
                        'complement' => ENV('MELHORENVIO_COMPLEMENT'),
                        'number' => ENV('MELHORENVIO_NUMBER'),
                        'district' => ENV('MELHORENVIO_DISTRICT'),
                        'city' => ENV('MELHORENVIO_CITY'),
                        'country_id' => 'BR',
                        'postal_code' => ENV('MELHORENVIO_POSTAL_CODE'),
                        'state_abbr' => ENV('MELHORENVIO_STATE_ABBR'),
                        'note' => 'Teste',
                    ],
                    'to' => [
                        'name' => $usuario->nome,
                        'phone' => $usuario->telefone,
                        'email' => $usuario->email,
                        'document' => $usuario->cpf,
                        'address' => $endereco->logradouro,
                        'complement' => $endereco->complemento,
                        'number' => $endereco->numero,
                        'district' => $endereco->bairro,
                        'city' => $endereco->cidade,
                        'country_id' => 'BR',
                        'postal_code' => $endereco->cep,
                        'state_abbr' => $endereco->uf,
                        'note' => 'teste',
                    ],
                    'options' => [
                        'receipt' => false,
                        'own_hand' => false,
                        'reverse' => true,
                        'non_commercial' => true,
                        'tags' => [
                            [
                                'tag' => '187723',
                                'Url' => 'https://urbeatstore.com.br/melhorenvio/posenvio',
                            ],
                        ],
                    ],
                    'service' => $pedido->cod_entrega,
                ];

                foreach ($volumes as $v) {
                    $product = $v['products'];
                    $dimensions = $v['dimensions'];
                    foreach ($product as $p) {
                        $produto = Produto::find($p['id']);
                        $product = [
                            'name' => $produto->nome,
                            'quantity' => $p['quantity'],
                            'unitary_value' => $produto->valor,
                        ];

                        $requestData['products'][] = $product;
                    }

                    $volume = [
                        'height' => $dimensions['height'],
                        'width' => $dimensions['width'],
                        'length' => $dimensions['length'],
                        'weight' => $v['weight'],
                    ];

                    $requestData['volumes'][] = $volume;

                    // Headers da requisição
                    $headers = [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $responseData['access_token'],
                        'Content-Type' => 'application/json',
                        'User-Agent' => env('USER_AGENT'),
                    ];

                    // Fazer a requisição POST
                    try {
                        $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/cart', [
                            'json' => $requestData,
                            'headers' => $headers,
                        ]);
                    } catch (\Exception $e) {
                        try {
                            $this->refreshToken();
                            $responseData = $this->Authorization();

                            // Headers da requisição
                            $headers = [
                                'Accept' => 'application/json',
                                'Authorization' => 'Bearer ' . $responseData['access_token'],
                                'Content-Type' => 'application/json',
                                'User-Agent' => env('USER_AGENT'),
                            ];

                            $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/cart', [
                                'json' => $requestData,
                                'headers' => $headers,
                            ]);
                        } catch (\Exception $e) {
                            Log::error("ERRO", ['file' => 'MelhorEnvioController.lancarFrete_volumes', 'message' => $e->getMessage()]);
                        }
                    }
                }
            } else {

                // Criar uma instância do GuzzleHttp\Client
                $client = new Client();

                // Dados para a requisição
                $requestData = [
                    'from' => [
                        'name' => ENV('MELHORENVIO_NAME'),
                        'phone' => ENV('MELHORENVIO_PHONE'),
                        'email' => ENV('MELHORENVIO_EMAIL'),
                        'document' => ENV('MELHORENVIO_DOCUMENT'),
                        'state_register' => ENV('MELHORENVIO_STATE_REGISTER'),
                        'address' => ENV('MELHORENVIO_ADDRESS'),
                        'complement' => ENV('MELHORENVIO_COMPLEMENT'),
                        'number' => ENV('MELHORENVIO_NUMBER'),
                        'district' => ENV('MELHORENVIO_DISTRICT'),
                        'city' => ENV('MELHORENVIO_CITY'),
                        'country_id' => 'BR',
                        'postal_code' => ENV('MELHORENVIO_POSTAL_CODE'),
                        'state_abbr' => ENV('MELHORENVIO_STATE_ABBR'),
                        'note' => 'Teste',
                    ],
                    'to' => [
                        'name' => $usuario->nome,
                        'phone' => $usuario->telefone,
                        'email' => $usuario->email,
                        'document' => $usuario->cpf,
                        'address' => $endereco->logradouro,
                        'complement' => $endereco->complemento,
                        'number' => $endereco->numero,
                        'district' => $endereco->bairro,
                        'city' => $endereco->cidade,
                        'country_id' => 'BR',
                        'postal_code' => $endereco->cep,
                        'state_abbr' => $endereco->uf,
                        'note' => 'teste',
                    ],
                    'options' => [
                        'receipt' => false,
                        'own_hand' => false,
                        'reverse' => true,
                        'non_commercial' => true,
                        'tags' => [
                            [
                                'tag' => '187723',
                                'Url' => 'https://urbeatstore.com.br/melhorenvio/posenvio',
                            ],
                        ],
                    ],
                    'service' => $pedido->cod_entrega,
                ];

                foreach ($volumes as $v) {

                    $product = $v['products'];

                    $dimensions = $v['dimensions'];

                    foreach ($product as $p) {
                        $produto = Produto::find($p['id']);
                        $product = [
                            'name' => $produto->nome,
                            'quantity' => $p['quantity'],
                            'unitary_value' => $produto->valor,
                        ];

                        $requestData['products'][] = $product;
                    }

                    $volume = [
                        'height' => $dimensions['height'],
                        'width' => $dimensions['width'],
                        'length' => $dimensions['length'],
                        'weight' => $v['weight'],
                    ];

                    $requestData['volumes'][] = $volume;
                }

                // Headers da requisição
                $headers = [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $responseData['access_token'],
                    'Content-Type' => 'application/json',
                    'User-Agent' => env('USER_AGENT'),
                ];

                // Fazer a requisição POST
                try {
                    $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/cart', [
                        'json' => $requestData,
                        'headers' => $headers,
                    ]);
                } catch (\Exception $e) {
                    try {
                        $this->refreshToken();
                        $responseData = $this->Authorization();
                        // Headers da requisição
                        $headers = [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer ' . $responseData['access_token'],
                            'Content-Type' => 'application/json',
                            'User-Agent' => env('USER_AGENT'),
                        ];
                        $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/cart', [
                            'json' => $requestData,
                            'headers' => $headers,
                        ]);
                    } catch (\Exception $e) {
                        Log::error("ERRO", ['file' => 'MelhorEnvioController.lancarFrete', 'message' => $e->getMessage()]);
                    }
                }
            }

            // Retornar a resposta da API
            return $response->getBody();
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'MelhorEnvioController.lancarFrete', 'message' => $e->getMessage()]);
        }
    }

    public function calcularFrete2(string $cep, $idPedido)
    {
        //FUNÇÃO PARA BUSCAR O ORÇAMENTO DE FRETE ANTES DE LANÇAR O FRETE.
        //buscando autorização
        $responseData = $this->Authorization();

        $client = new Client();
        $itens = ItensPedido::where('pedido_id', $idPedido)->get();
        $prods = $itens;
        $products = [];

        foreach ($prods as $prod) {
            // Adicionar cada produto à lista 'products'
            $p = Produto::find($prod->produto_id);
            $products[] = [
                'id' => $p->id,
                'width' => $p->largura,
                'height' => $p->altura,
                'length' => $p->comprimento,
                'weight' => ($p->peso / 1000),
                'insurance_value' => $p->valor,
                'quantity' => $prod['quantidade'],
            ];
        }

        // Parâmetros para a requisição
        $postData = [
            'from' => ['postal_code' => env('MELHORENVIO_POSTAL_CODE')],
            'to' => ['postal_code' => $cep],
            'options' => [
                'receipt' => false, 'own_hand' => false, 'reverse' => true,
                'non_commercial' => true
            ],
            'products' => $products
        ];

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseData['access_token'],
            'Content-Type' => 'application/json',
            'User-Agent' => ENV('USER_AGENT'),
        ];

        try {

            // Fazer a requisição POST
            $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/calculate', [
                'json' => $postData,
                'headers' => $headers,
            ]);
        } catch (\Exception $e) {
            try {
                $this->refreshToken();
                $responseData = $this->Authorization();
                $headers = [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $responseData['access_token'],
                    'Content-Type' => 'application/json',
                    'User-Agent' => ENV('USER_AGENT'),
                ];
                // Fazer a requisição POST
                $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/calculate', [
                    'json' => $postData,
                    'headers' => $headers,
                ]);

            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'MelhorEnvioController.calcularFrete2', 'message' => $e->getMessage()]);
            }
        }
        $responseJson = json_decode($response->getBody(), true);
        //Retornar a resposta da API
        return $responseJson;
    }

    public function consultarFreteFixo()
    {
        $freteFixo = ENV('FRETE_FIXO');
        return $freteFixo;
    }

    public function consultarFrete299()
    {
        $frete299 = ENV('FRETE_GRATIS299');
        return $frete299;
    }

    public function consultarMotoboy(String $cidade)
    {
        $motoboy = env('MOTOBOY');

        $valor = 0;

        if ($motoboy == 2) {
            $valor = 0;
            switch ($cidade) {
                case "serra":
                    $valor = intval(env('MOTOBOY_SERRA', 0));
                    break;
                case "vitoria":
                    $valor = intval(env('MOTOBOY_VIX', 0));
                    break;
                case "vilavelha":
                    $valor = intval(env('MOTOBOY_VV', 0));
                    break;
                case "cariacica":
                    $valor = intval(env('MOTOBOY_CARIACICA', 0));
                    break;
            }
        }

        return $valor;
    }

    public function calcularFrete(string $cep)
    {

        //FUNCAO PARA BUSCAR ORÇAMENTO DE FRETE
        //buscando autorização
        $responseData = $this->Authorization();
        $client = new Client();
        $prods = session('cart', []);
        $products = [];

        foreach ($prods as $prod) {
            // Adicionar cada produto à lista 'products'
            $products[] = [
                'id' => $prod['id'],
                'width' => $prod['largura'],
                'height' => $prod['altura'],
                'length' => $prod['comprimento'],
                'weight' => $prod['peso'] / 1000,
                'insurance_value' => $prod['valor'],
                'quantity' => $prod['quantidade'],
            ];
        }

        // Parâmetros para a requisição
        $postData = [
            'from' => ['postal_code' => env('MELHORENVIO_POSTAL_CODE')],
            'to' => ['postal_code' => $cep],
            'options' => [
                'receipt' => false, 'own_hand' => false, 'reverse' => true,
                'non_commercial' => true
            ],
            'products' => $products
        ];

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseData['access_token'],
            'Content-Type' => 'application/json',
            'User-Agent' => ENV('USER_AGENT'),
        ];

        try {

            // Fazer a requisição POST
            $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/calculate', [
                'json' => $postData,
                'headers' => $headers,
            ]);
        } catch (\Exception $e) {
            try {
                //REFAZENDO REQUISIÇÃO DO POST CASO O POST TENHA SIDO NEGADO.
                $this->refreshToken();
                $responseData = $this->Authorization();
                $headers = [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $responseData['access_token'],
                    'Content-Type' => 'application/json',
                    'User-Agent' => ENV('USER_AGENT'),
                ];
                // Fazer a requisição POST
                $response = $client->request('POST', 'https://melhorenvio.com.br/api/v2/me/shipment/calculate', [
                    'json' => $postData,
                    'headers' => $headers,
                ]);

            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'MelhorEnvioController.calcularFrete', 'message' => $e->getMessage()]);
            }
        }

        //Retornar a resposta da API
        return $response->getBody();
    }



    public function redirectToAuthorization()
    {
        $client_id = env('CLIENT_ID');
        $redirect_uri = 'https://urbeatstore.com.br/melhorenvio/getAuthorization';
        $scope = env('SCOPE'); // Adicione outras permissões conforme necessário
        $authorizationUrl = "https://melhorenvio.com.br/oauth/authorize?";
        $authorizationUrl .= "client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}";
        return redirect()->away($authorizationUrl);
    }



    public function getToken(Request $request)
    {
        $produtoController = new ProdutoController;
        $client = new \GuzzleHttp\Client();
        $code = env('AUTHORIZATION_CODE');
        //montando a requisição
        $response = $client->request('POST', 'https://melhorenvio.com.br/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'redirect_uri' => 'https://urbeatstore.com.br/melhorenvio/getAuthorization',
                'code' => $code, // Certifique-se de ajustar conforme necessário
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'User-Agent' => ENV('USER_AGENT'),
            ],
        ]);
        // Obter e retornar a resposta JSON
        $responseJson = json_decode($response->getBody(), true);
        // Verifique se os tokens foram obtidos com sucesso
        if (isset($responseJson['access_token']) && isset($responseJson['refresh_token'])) {
            $produtoController->AtualizarVariaveisAmbiente('AUTHORIZATION', json_encode($responseJson));
            $message = 'Tokens armazenados com sucesso.';
            return $message;
        } else {
            // Informar ao usuário que houve um problema ao obter os tokens
            $message = 'Problema ao tentar armazenar os tokens.';
            return $message;
        }
    }

    public function refreshToken()
    {
        $produtoController = new ProdutoController;
        //FUNÇÃO PARA ATUALIZAR O TOKEN.
        $client = new \GuzzleHttp\Client();
        $responseData = $this->Authorization();
        //montando a requisição
        try {
            $response = $client->request('POST', 'https://melhorenvio.com.br/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => env('CLIENT_ID'),
                    'client_secret' => env('CLIENT_SECRET'),
                    'refresh_token' => $responseData['refresh_token']
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => ENV('USER_AGENT'),
                ],
            ]);

            // Obter e retornar a resposta JSON
            $responseJson = json_decode($response->getBody(), true);
            // Verifique se os tokens foram obtidos com sucesso
            if (isset($responseJson['access_token']) && isset($responseJson['refresh_token'])) {
                $produtoController->AtualizarVariaveisAmbiente('AUTHORIZATION', json_encode($responseJson));
            }
            Log::info('Atualizou_Token');
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'MelhorEnvioController.refreshToken', 'message' => $e->getMessage()]);
        }
    }

    public function getAuthorization(Request $request)
    {
        $produtoController = new ProdutoController;
        // Verifique se o código de autorização está presente na solicitação
        if ($request->has('code')) {
            // Obtenha o código de autorização da solicitação
            $authorizationCode = $request->input('code');
            $produtoController->AtualizarVariaveisAmbiente('AUTHORIZATION_CODE', $authorizationCode);
            $tokenResponse = $this->getToken($request);

            if ($tokenResponse == 'Tokens armazenados com sucesso.') {
                $status = 'ok';
                $request->session()->flash($status, $tokenResponse);
            } else {
                $status = 'err';
                $request->session()->flash($status, $tokenResponse);
            }
        } else {
            // Informe ao usuário que o código de autorização não foi encontrado na solicitação
            $status = 'err';
            $request->session()->flash($status, 'Código de autorização não encontrado na solicitação!');
        }
        return redirect()->route('configuracoes');
    }

    //funções ativas

    public function melhorInfo()
    {
        // Função para imprimir opções de configuração do melhor envio
        //buscando autorização
        $responseData = $this->Authorization();
        $client = new Client();
        try {
            $response = $client->request('GET', 'https://melhorenvio.com.br/api/v2/me/shipment/app-settings', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $responseData['access_token'],
                    'User-Agent' => ENV('USER_AGENT'),
                ],
            ]);

            $statusCode = $response->getStatusCode();

            // Verifique se a solicitação foi bem-sucedida (código de status 200)
            if ($statusCode === 200) {
                $data = json_decode($response->getBody(), true);
                // Faça algo com os dados aqui
                echo '<pre>';
                print_r($data);
                echo '</pre>';
            } else {
                // Exiba detalhes do erro
                echo "Erro na requisição: {$statusCode} - {$response->getBody()}";
            }
        } catch (\Exception $e) {
            try {
                $this->refreshToken();
                $responseData = $this->Authorization();
                $response = $client->request('GET', 'https://melhorenvio.com.br/api/v2/me/shipment/app-settings', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $responseData['access_token'],
                        'User-Agent' => ENV('USER_AGENT'),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'MelhorEnvioController.melhorInfo', 'message' => $e->getMessage()]);
            }
        }
    }
}
