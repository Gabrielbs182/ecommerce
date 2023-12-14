<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MelhorEnvioController extends Controller
{
    // public function authorize()
    // {
    //     // Redirecionar para a URL de autorização do Melhor Envio
    //     // Isso geralmente envolve redirecionar o usuário para a URL fornecida pelo Melhor Envio com os parâmetros apropriados
    // }

    public function redirectToAuthorization()
    {
        $client_id = '3946';
        $redirect_uri = 'https://urbeatstore.com.br/melhorenvio/getAuthorization';
        $scope = 'cart-read'; // Adicione outras permissões conforme necessário
        $authorizationUrl = "https://sandbox.melhorenvio.com.br/oauth/authorize?";
        $authorizationUrl .= "client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}";

        return redirect()->away($authorizationUrl);
    }

    public function getToken()
    {
        $client = new \GuzzleHttp\Client();

        $code = env('AUTHORIZATION_CODE');

        $response = $client->request('POST', 'https://sandbox.melhorenvio.com.br/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => 3946,
                'client_secret' => 'REMOVIDO_MELHORENVIO_CLIENT_SECRET',
                'redirect_uri' => 'https://urbeatstore.com.br/melhorenvio/getAuthorization',
                'code' => $code, // Certifique-se de ajustar conforme necessário
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'User-Agent' => 'Urbeat (email@exemplo.com)',
            ],
        ]);

        // Obter e retornar a resposta JSON
        $responseJson = json_decode($response->getBody(), true);

        // Verifique se os tokens foram obtidos com sucesso
        if (isset($responseJson['access_token']) && isset($responseJson['refresh_token'])) {
            // Salvar o access token no .env
            $envPath = base_path('.env');
            $envContent = File::get($envPath);

            // Atualizar ou adicionar a variável de ambiente do access token
            if (Str::contains($envContent, 'MELHORENVIO_ACCESS_TOKEN')) {
                $envContent = Str::replaceFirst(
                    'MELHORENVIO_ACCESS_TOKEN=' . config('app.MELHORENVIO_ACCESS_TOKEN'),
                    'MELHORENVIO_ACCESS_TOKEN=' . $responseJson['access_token'],
                    $envContent
                );
            } else {
                $envContent .= "\nMELHORENVIO_ACCESS_TOKEN=" . $responseJson['access_token'];
            }

            // Salvar o refresh token no .env
            if (Str::contains($envContent, 'MELHORENVIO_REFRESH_TOKEN')) {
                $envContent = Str::replaceFirst(
                    'MELHORENVIO_REFRESH_TOKEN=' . config('app.MELHORENVIO_REFRESH_TOKEN'),
                    'MELHORENVIO_REFRESH_TOKEN=' . $responseJson['refresh_token'],
                    $envContent
                );
            } else {
                $envContent .= "\nMELHORENVIO_REFRESH_TOKEN=" . $responseJson['refresh_token'];
            }

            // Salvar as alterações no arquivo .env
            File::put($envPath, $envContent);

            // Informar ao usuário que os tokens foram armazenados com sucesso
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            // Verificar se a chave Authorization já existe
            if (strpos($envContent, 'Authorization') !== false) {
                // Atualizar o valor se já existir
                $envContent = preg_replace('/(Authorization)=(.*)/', 'Authorization=' . json_encode($responseJson), $envContent);
            } else {
                // Adicionar uma nova linha com a chave Authorization e os tokens
                $envContent .= "\nAuthorization=" . json_encode($responseJson);
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


    public function melhorInfo()
    {

        // Obter o token do .env
        $token = env('Authorization'); // Supondo que você tenha armazenado o token como AUTHORIZATION_CODE

        // Verificar se o token está presente
        if (!$token) {
            return response()->json(['error' => 'Token não encontrado.']);
        }

        // Fazer a solicitação à API
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'User-Agent' => 'Aplicação (email para contato)',
            ])->get('https://sandbox.melhorenvio.com.br/api/v2/me/shipment/app-settings');

            // Retornar os dados da resposta da API
            return $response->json();
        } catch (\Exception $e) {
            // Lidar com erros de solicitação
            return response()->json(['error' => 'Erro ao fazer a solicitação à API: ' . $e->getMessage()]);
        }

        // return view('melhorInfo');
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
}
