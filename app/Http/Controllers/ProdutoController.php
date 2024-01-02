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

    // public function adicionarCarrinho($id,$quantidade)
    // {
    //     $prod = Produto::find($id);
    //     if ($prod) {
    //         $carrinho = session('cart', []);
    //         array_push($carrinho, $prod);
    //         session(['cart' => $carrinho]);
    //     }
    //     return redirect()->route("home");
    // }

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
        $carrinho = session('cart', []);
        $data = ['cart' => $carrinho];
        dd($request);
        // return view("carrinho", $data);
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

    public function postToPagseguro()
    {
        $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout';
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $client = new Client();

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'email' => $email,
                'token' => $token,
                'currency' => 'BRL',
                'itemId1' => '001',
                'itemDescription1' => 'Item 1',
                'itemAmount1' => '169.90',
                'itemQuantity1' => '1',
                'itemWeight1' => '300',
                'shippingAddressStreet' => 'Rua Santa Catarina',
                'shippingAddressNumber' => '198',
                'shippingAddressComplement' => 'Casa',
                'shippingAddressDistrict' => 'Carapina',
                'shippingAddressCity' => 'Serra',
                'shippingAddressState' => 'ES',
                'shippingAddressCountry' => 'BRA',
                'shippingAddressPostalCode' => '29160044',
                'shippingType' => 1,
                'reference' => '124665c23f7896adff508377925',
                'senderName' => 'Gabriel Bremenkamp',
                'senderAreaCode' => '51',
                'senderPhone' => '988888888',
                'senderEmail' => 'teste@pagseguro.com.br',
                'extraAmount' => '0.00',
                'senderCPF' => 'CPF',
                'senderCPF' => '00000000000',
                'redirectURL' => 'http://localhost:8000'
            ],
        ]);
        $responseBody = $response->getBody()->getContents();
        $xml = new SimpleXMLElement($responseBody);
        $code = (string)$xml->code;
        $data = [];
        $data["code"] = $code;
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
