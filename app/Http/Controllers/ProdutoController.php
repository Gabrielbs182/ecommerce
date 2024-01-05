<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Tamanho;
use App\Models\Categoria;
use App\Models\ItensPedido;
use App\Models\Pedido;
use App\Services\VendaService;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use PagSeguro\Configuration\Configure;
use GuzzleHttp\Client;
use SimpleXMLElement;
use Symfony\Component\VarDumper\VarDumper;

class ProdutoController extends Controller
{
    //

    private $_configs;

    public function __construct()
    {
        $this->_configs = new Configure();
        $this->_configs->setCharset("UTF-8");
        $this->_configs->setAccountCredentials(env('PAGSEGURO_EMAIL'), env('PAGSEGURO_TOKEN'));
        $this->_configs->setEnvironment(env('PAGSEGURO_AMBIENTE'));
        $this->_configs->setLog(true, storage_path('logs/pagseguro_' . date('Ymd') . '.log'));
    }

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

    public function getCredential()
    {
        return $this->_configs->getAccountCredentials();
    }

    public function index(Request $request)
    {
        $data = [];
        $listaProdutos = Produto::all();
        $data["lista"] = $listaProdutos;
        return view("home", $data);
    }

    public function categoria($idcategoria = 0, Request $request)
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

    public function adicionarCarrinho($id, $quantidade)
    {
        $prod = Produto::find($id);
        if ($prod) {
            $carrinho = session('cart', []);
            // Verifique se o produto já está no carrinho
            $produtoExistente = null;
            foreach ($carrinho as $key => $item) {
                if ($item->id == $prod->id) {
                    $produtoExistente = $key;
                    break;
                }
            }
            if ($produtoExistente !== null) {
                // Se o produto já existe no carrinho, adicione a quantidade à quantidade existente
                $carrinho[$produtoExistente]->quantidade += $quantidade;
            } else {
                // Caso contrário, adicione o produto ao carrinho
                $prod->quantidade = $quantidade;
                array_push($carrinho, $prod);
            }
            session(['cart' => $carrinho]);
        }
        return redirect()->route("home");
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

    public function excluirCarrinho($indice, Request $request)
    {
        $carrinho = session('cart', []);
        if (isset($carrinho[$indice])) {
            unset($carrinho[$indice]);
        }
        session(["cart" => $carrinho]);
        return redirect()->route("ver_carrinho");
    }

    public function posPagar(Request $request)
    {
        dd($request);
    }

    public function postToPagseguro(Request $request)
    {

        $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout';
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $vlrFrete = $request->input('valorFrete');
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

        $carrinho = session('cart', []);

        $formParams = [
            'email' => $email,
            'token' => $token,
            'currency' => 'BRL',
            'shippingAddressStreet' => $logradouro,
            'shippingAddressNumber' => $numero,
            'shippingAddressComplement' => 'Casa',
            'shippingAddressDistrict' => $bairro,
            'shippingAddressCity' => $cidade,
            'shippingAddressState' => $uf,
            'shippingAddressCountry' => 'BRA',
            'shippingAddressPostalCode' => $cep,
            'shippingType' => 3,
            'reference' => '124665c23f7896adff508377925',
            'senderName' => $nomeCliente,
            'senderAreaCode' => $ddd,
            'senderPhone' => $telefone,
            'senderEmail' => $emailCliente,
            'extraAmount' => '0.00',
            'senderCPF' => 'CPF',
            'senderCPF' => '00000000000',
            'redirectURL' => 'http://localhost:8000',
        ];

        if ($vlrFrete > 0) {
            $formParams['shippingCost'] = $vlrFrete;
        }

        foreach ($carrinho as $index => $item) {
            $produto = Produto::find($item['id']); // Corrigi aqui, substituí "Produto.find" por "Produto::find"
            $formParams["itemId" . ($index + 1) . ""] = $item['id'];
            $formParams["itemDescription" . ($index + 1) . ""] = $produto['nome'];
            $formParams["itemAmount" . ($index + 1) . ""] = $item['valor'];
            $formParams["itemQuantity" . ($index + 1) . ""] = $item['quantidade'];
            $formParams["itemWeight" . ($index + 1) . ""] = $produto['peso'];
        }

        $client = new Client();

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => $formParams,
        ]);

        $responseBody = $response->getBody()->getContents();
        $xml = new SimpleXMLElement($responseBody);
        $code = (string)$xml->code;
        $data = [
            "code" => $code,
        ];

        return view('pagar', $data);
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
