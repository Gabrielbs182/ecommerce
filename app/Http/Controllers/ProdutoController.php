<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Categoria;
use App\Models\ItensPedido;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function handle(Request $request)
    {
        $payload = $request->all();

        if (isset($payload['reference_id'])) {
            $referenceId = $payload['reference_id'];

            // Verifica se 'charges' e 'status' existem antes de acessá-los
            if (isset($payload['charges'][0]['status'])) {
                $status_pagamento = $payload['charges'][0]['status'];
            }

            // Verifica se 'status' existe antes de acessá-lo
            // if (isset($payload['status'])) {
            //     $status_checkout = $payload['status'];
            // }

            Log::error('Recebeu webhook', ['reference_id' => $referenceId, 'status_pagamento' => $status_pagamento, 'status_checkout' => $status_checkout]);

            $pedido = Pedido::where('reference_id', $referenceId)->first();

            if ($pedido) {
                // Atualizando o status no sistema
                $pedido->status_pag = $status_pagamento;
                $pedido->save();

                if ($pedido->status_pag == 'DECLINED' or $pedido->status_pag == 'CANCELED') {
                    $pedido->status = 'Cancelado';
                    $pedido->save();
                    $this->InativarCheckout($pedido->cod_checkout);
                } else if ($pedido->status_pag == 'PAID') {
                    $pedido->status = 'Pagamento aprovado';
                    //lançar frete
                }
                Log::info('Webhook processado com sucesso', ['reference_id' => $referenceId]);
                // Se necessário, você pode adicionar lógica adicional para lidar com outros eventos, como pagamento confirmado, cancelado, etc.
            }
        }
        // Responda para confirmar o recebimento do Webhook
        return response()->json(['status' => 'Webhook recebido com sucesso']);
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

}
