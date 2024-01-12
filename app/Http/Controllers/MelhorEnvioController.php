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


class MelhorEnvioController extends Controller
{
    // public function authorize()
    // {
    //     // Redirecionar para a URL de autorização do Melhor Envio
    //     // Isso geralmente envolve redirecionar o usuário para a URL fornecida pelo Melhor Envio com os parâmetros apropriados
    // }


    //FUNÇÕES PASSIVAS

    public function Authorization()
    {
        $apiResponse = ENV('AUTHORIZATION');
        $responseData = json_decode($apiResponse, true);
        return $responseData;
    }

    public function lancarFrete($reference_id)
    {
        try {
            //buscando autorização
            $responseData = $this->Authorization();

            //buscando os dados
            $pedido = Pedido::where('reference_id', $reference_id)->first();

            if (isset($pedido)) {
                $endereco = Endereco::find($pedido->endereco_id);
                $usuario = Usuario::find($pedido->usuario_id);
                $itens = ItensPedido::where('pedido_id', $pedido->id)->get();
            }

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
                    'receipt' => true,
                    'own_hand' => true,
                    'reverse' => true,
                    'non_commercial' => true,
                    'insurance_value' => 180,
                    'tags' => [
                        [
                            'tag' => '187723',
                            'Url' => 'https://urbeatstore.com.br/melhorenvio/posenvio',
                        ],
                    ],
                ],
                'service' => $pedido->cod_entrega,
            ];

            foreach ($itens as $item) {
                $produto = Produto::find($item['id']);
                $product = [
                    'name' => $produto['nome'],
                    'quantity' => $item['quantidade'],
                    'unitary_value' => $item['valor'],
                ];
                $requestData['products'][] = $product;
                $volume = [
                    'height' => $produto->altura,
                    'width' => $produto->largura,
                    'length' => $produto->comprimento,
                    'weight' => $produto->peso,
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
                $response = $client->request('POST', 'https://sandbox.melhorenvio.com.br/api/v2/me/cart', [
                    'json' => $requestData,
                    'headers' => $headers,
                ]);
            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'MelhorEnvioController.lancarFrete', 'message' => $e->getMessage()]);
            }

            // Retornar a resposta da API
            return $response->getBody();
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'MelhorEnvioController.lancarFrete', 'message' => $e->getMessage()]);
        }
    }

    public function calcularFrete(string $cep)
    {

        //buscando autorização
        $responseData = $this->Authorization();

        // Criar uma instância do GuzzleHttp\Client
        $client = new Client();

        $prods = session('cart', []);

        // Inicializar a lista de produtos
        $products = [];

        foreach ($prods as $prod) {
            // Adicionar cada produto à lista 'products'
            $products[] = [
                'id' => $prod['id'],
                'width' => $prod['largura'],
                'height' => $prod['altura'],
                'length' => $prod['comprimento'],
                'weight' => $prod['peso'] / 1000,
                'insurance_value' => $prod['valor'] * $prod['quantidade'],
                'quantity' => $prod['quantidade'],
            ];
        }

        // Parâmetros para a requisição
        $postData = [
            'from' => ['postal_code' => '96020360'],
            'to' => ['postal_code' => $cep],
            'options' => ['receipt' => true, 'own_hand' => true],
            'products' => $products
        ];

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseData['access_token'],
            'Content-Type' => 'application/json',
            'User-Agent' => ENV('USER_AGENT'),
        ];

        // Fazer a requisição POST
        $response = $client->request('POST', 'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate', [
            'json' => $postData,
            'headers' => $headers,
        ]);

        //Retornar a resposta da API
        return $response->getBody();
    }



    public function redirectToAuthorization()
    {
        $client_id = env('CLIENT_ID');
        $redirect_uri = 'https://urbeatstore.com.br/melhorenvio/getAuthorization';
        $scope = env('SCOPE'); // Adicione outras permissões conforme necessário
        $authorizationUrl = "https://sandbox.melhorenvio.com.br/oauth/authorize?";
        $authorizationUrl .= "client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}";

        return redirect()->away($authorizationUrl);
    }



    public function getToken()
    {
        $client = new \GuzzleHttp\Client();

        //buscando o código de autorização obtino no getAuthorization
        $code = env('AUTHORIZATION_CODE');

        //montando a requisição
        $response = $client->request('POST', 'https://sandbox.melhorenvio.com.br/oauth/token', [
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

            //Buscando o conteudo do .env
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            // Verificar se a chave Authorization já existe
            if (strpos($envContent, 'AUTHORIZATION') !== false) {
                // Atualizar o valor se já existir
                $envContent = preg_replace('/(AUTHORIZATION)=(.*)/', 'AUTHORIZATION=' . json_encode($responseJson), $envContent);
            } else {
                // Adicionar uma nova linha com a chave Authorization e os tokens
                $envContent .= "\AUTHORIZATION=" . json_encode($responseJson);
            }

            // Salvar as alterações no arquivo .env
            file_put_contents($envPath, $envContent);

            // Informar ao usuário que os tokens foram armazenados com sucesso
            return "Tokens armazenados com sucesso!";
        } else {
            // Informar ao usuário que houve um problema ao obter os tokens
            return "Erro: Tokens não foram obtidos com sucesso.";
        }
    }

    public function getAuthorization(Request $request)
    {
        // Verifique se o código de autorização está presente na solicitação
        if ($request->has('code')) {
            // Obtenha o código de autorização da solicitação
            $authorizationCode = $request->input('code');

            // Salve o código de autorização no arquivo .env
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            // Verifique se o arquivo .env existe
            if (File::exists($envPath)) {
                // Abra o arquivo .env para leitura
                $envContent = File::get($envPath);

                // Adicione ou atualize a variável de ambiente do código de autorização
                // Verificar se a variável de ambiente AUTHORIZATION_CODE já existe
                if (strpos($envContent, 'AUTHORIZATION_CODE') !== false) {
                    // Atualizar o valor se já existir
                    $envContent = preg_replace('/(AUTHORIZATION_CODE)=(.*)/', 'AUTHORIZATION_CODE=' . $authorizationCode, $envContent);
                } else {
                    // Adicionar a variável se não existir
                    $envContent .= "\nAUTHORIZATION_CODE=" . $authorizationCode;
                }

                // Salve as alterações no arquivo .env
                file_put_contents($envPath, $envContent);

                // Informe ao usuário que o código de autorização foi armazenado com sucesso
                // Agora, chame a função getToken
                $tokenResponse = $this->getToken($request);

                // Se precisar fazer algo com a resposta da função getToken, pode fazer aqui

                return $tokenResponse; // ou alguma mensagem personalizada
            } else {
                // Informe ao usuário que o arquivo .env não foi encontrado
                return "Erro: Arquivo .env não encontrado!";
            }
        } else {
            // Informe ao usuário que o código de autorização não foi encontrado na solicitação
            return "Erro: Código de autorização não encontrado na solicitação!";
        }
    }

    //funções ativas

    public function melhorInfo()
    {
        // Função para imprimir opções de configuração do melhor envio

        //buscando autorização
        $responseData = $this->Authorization();

        $client = new Client();

        try {
            $response = $client->request('GET', 'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/app-settings', [
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
                var_dump($data);
            } else {
                // Exiba detalhes do erro
                echo "Erro na requisição: {$statusCode} - {$response->getBody()}";
            }
        } catch (\Exception $e) {
            // Exiba detalhes do erro
            echo "Erro ao fazer a requisição: {$e->getMessage()}";
        }
    }
}
