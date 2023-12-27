<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

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

    public function calcularFrete(Request $request)
    {
        //buscando autorização
        $responseData = $this->Authorization();

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate', [
            'body' => '{"from":{"postal_code":"96020360"},"to":{"postal_code":"01018020"},"options":{"receipt":true,"own_hand":true},"products":[{"id":"x","width":11,"height":17,"length":11,"weight":0.3,"insurance_value":10.1,"quantity":1}]}',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $responseData['access_token'],
                'Content-Type' => 'application/json',
                'User-Agent' => ENV('USER_AGENT'),
            ],
        ]);

        echo $response->getBody();
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
