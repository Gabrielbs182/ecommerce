<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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

    public function getToken(Request $request)
    {
        // Verificar se a solicitação contém os parâmetros necessários
        $this->validate($request, [
            'client_id' => 'required',
            'client_secret' => 'required',
            'redirect_uri' => 'required',
            'code' => 'required',
            'grant_type' => 'required',
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
                'code' => $request->input('code'),
                // Adicione outros parâmetros conforme necessário
            ],
        ]);

        // Obter e retornar a resposta JSON
        return json_decode($response->getBody(), true);
    }

    public function getToken2(Request $request)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://sandbox.melhorenvio.com.br/oauth/token', [
            'body' => '{"grant_type":"authorization_code","client_id":3946,"client_secret":"REMOVIDO_MELHORENVIO_CLIENT_SECRET","redirect_uri":"https://urbeatstore.com.br/melhorenvio/getAuthorization","code":"REMOVIDO_AUTH_CODE"}',
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Urbeat (email@exemplo.com)',
            ],
        ]);

        echo $response->getBody();
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
                // Agora, chame a função getToken
                return redirect('/melhorenvio/teste');
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
