<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MelhorEnvioController extends Controller
{


    // public function authorize()
    // {
    //     // Redirecionar para a URL de autorização do Melhor Envio
    //     // Isso geralmente envolve redirecionar o usuário para a URL fornecida pelo Melhor Envio com os parâmetros apropriados
    // }

    public function getToken2(Request $request)
    {
        $baseUrl = 'https://sandbox.melhorenvio.com.br/oauth/token';

        // Parâmetros necessários para obter o token
        $params = [
            'grant_type'    => 'refresh_token',
            'client_id'     => '3946',
            'client_secret' => 'REMOVIDO_MELHORENVIO_CLIENT_SECRET',
            'redirect_uri'  => 'https://urbeatstore.com.br',
            'code'          => $request->input('code'), // Este código é fornecido após a autorização
        ];

        // Configuração do cliente Guzzle
        $client = new Client();

        try {
            // Faça uma solicitação POST para obter o token
            $response = $client->post($baseUrl, [
                'form_params' => $params,
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent'   => 'SUA-APLICACAO-USER-AGENT', // Substitua por um nome relevante
                ],
            ]);

            // Decodifique a resposta JSON
            $data = json_decode($response->getBody(), true);

            // Aqui você pode manipular os dados da resposta conforme necessário
            dd($data);
        } catch (\Exception $e) {
            // Trate qualquer erro que possa ocorrer durante a solicitação
            dd($e->getMessage());
        }
    }

    public function getToken(Request $request)
    {
        // Verificar se a solicitação contém os parâmetros necessários
        $this->validate($request, [
            'grant_type' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
            'redirect_uri' => 'required',
        ]);

        // Fazer solicitação HTTP para obter o token
        $client = new Client();
        $response = $client->post('https://sandbox.melhorenvio.com.br/oauth/token', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'email@exemplo.com',
            ],
            'json' => [
                'grant_type' => $request->input('grant_type'),
                'client_id' => $request->input('client_id'),
                'client_secret' => $request->input('client_secret'),
                'redirect_uri' => $request->input('redirect_uri'),
                // Adicione outros parâmetros conforme necessário
            ],
        ]);

        // Obter e retornar a resposta JSON
        return json_decode($response->getBody(), true);
    }

    public function teste()
    {

        return view('teste');
    }


    public function getAuthorization(Request $request)
    {
        // Verifique se o código de autorização está presente na solicitação
        if ($request->has('code')) {
            // Obtenha o código de autorização da solicitação
            $authorizationCode = $request->input('code');

            // Salve o código de autorização no arquivo .env
            $envPath = base_path('.env');

            // Verifique se o arquivo .env existe
            if (File::exists($envPath)) {
                // Abra o arquivo .env para leitura
                $envContent = File::get($envPath);

                // Adicione ou atualize a variável de ambiente do código de autorização
                if (Str::contains($envContent, 'AUTHORIZATION_CODE')) {
                    // Atualize o valor se já existir
                    $envContent = Str::replaceFirst(
                        'AUTHORIZATION_CODE=' . config('app.AUTHORIZATION_CODE'),
                        'AUTHORIZATION_CODE=' . $authorizationCode,
                        $envContent
                    );
                } else {
                    // Adicione a variável se não existir
                    $envContent .= "\nAUTHORIZATION_CODE=" . $authorizationCode;
                }

                // Salve as alterações no arquivo .env
                File::put($envPath, $envContent);

                // Informe ao usuário que o código de autorização foi armazenado com sucesso
                return "Código de autorização armazenado com sucesso!";
            } else {
                // Informe ao usuário que o arquivo .env não foi encontrado
                return "Erro: Arquivo .env não encontrado!";
            }
        } else {
            // Informe ao usuário que o código de autorização não foi encontrado na solicitação
            return "Erro: Código de autorização não encontrado na solicitação!";
        }
    }
}
