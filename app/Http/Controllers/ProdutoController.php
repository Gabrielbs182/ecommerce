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

    public function delete($id, Request $request)
    {
        $itens = ItensPedido::where('produto_id', $id)->get();
        $produto = Produto::find($id);
        if ($itens->count() > 0) {
            $message = 'Produto não pode ser deletado pois já teve movimentação, favor inativar o produto.';
            $status = 'err';
            $request->session()->flash($status, $message);
            return redirect()->route('produtos');
        } else {
            $produto->delete();
            $message = 'Produto deletado com sucesso.';
            $status = 'ok';
            $request->session()->flash($status, $message);
            return redirect()->route('produtos');
        }
    }

    public function deleteCategoria($id, Request $request)
    {
        $produtos = Produto::where('categoria_id', $id)->get();
        $categoria = Categoria::find($id);
        if ($produtos->count() > 0) {
            $message = 'Categoria não pode ser deletado pois já tem produto associados.';
            $status = 'err';
            $request->session()->flash($status, $message);
            return redirect()->route('categorias');
        } else {
            $categoria->delete();
            $message = 'Categoria deletada com sucesso.';
            $status = 'ok';
            $request->session()->flash($status, $message);
            return redirect()->route('categorias');
        }
    }

    public function update(Request $request)
    {
        $ativo = $request->has('ativo');
        $produto = Produto::find($request->input('id'));
        if ($ativo) {
            $produto->ativo = 1;
        } else {
            $produto->ativo = 0;
        }
        $produto->nome = $request->input('nome');
        $produto->descricao = $request->input('descricao');
        $produto->peso = $request->input('peso');
        $produto->altura = $request->input('altura');
        $produto->largura = $request->input('largura');
        $produto->comprimento = $request->input('comprimento');
        $produto->valor = $request->input('valor');
        $produto->save();

        try {
            if ($request->hasFile('foto1')) {
                $imagem = $request->file('foto1');
                $nomeArquivo = $produto->id . '.png';
                // Armazenar a imagem no caminho desejado
                $uploadSucesso = $imagem->storeAs('public/images', $nomeArquivo);
                if (!$uploadSucesso) {
                    // Adicione logs ou mensagens de erro, se necessário
                    throw new \Exception('Erro ao fazer upload da foto principal.');
                }
            }
            if ($request->hasFile('foto2')) {
                $imagem = $request->file('foto2');
                $nomeArquivo = $produto->id . '_1.png';
                // Armazenar a imagem no caminho desejado
                $uploadSucesso = $imagem->storeAs('public/images', $nomeArquivo);
                if (!$uploadSucesso) {
                    // Adicione logs ou mensagens de erro, se necessário
                    throw new \Exception('Erro ao fazer upload da foto secundaria 1.');
                }
            }
            if ($request->hasFile('foto3')) {
                $imagem = $request->file('foto3');
                $nomeArquivo = $produto->id . '_2.png';
                // Armazenar a imagem no caminho desejado
                $uploadSucesso = $imagem->storeAs('public/images', $nomeArquivo);
                if (!$uploadSucesso) {
                    // Adicione logs ou mensagens de erro, se necessário
                    throw new \Exception('Erro ao fazer upload da foto secundaria 2.');
                }
            }
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'ProdutoController.store', 'message' => $e->getMessage()]);
        }

        $message = 'Produto atualizado com sucesso.';
        $status = 'ok';
        $request->session()->flash($status, $message);
        return redirect()->route('produtos');
    }

    public function editarProduto($id)
    {
        $data = [];
        $produto = Produto::find($id);
        $data["produto"] = $produto;
        $listaCategorias = Categoria::all();
        $data["lista"] = $listaCategorias;
        return view("produto.editar", $data);
    }

    public function editarCategoria($id)
    {
        $data = [];
        $categoria = Categoria::find($id);
        $data["categoria"] = $categoria;
        return view("categoria.editar", $data);
    }

    public function updateCategoria(Request $request)
    {
        $categoria = Categoria::find($request->input('id'));
        $categoria->categoria = $request->input('categoria');
        $categoria->save();

        $message = 'Categoria atualizada com sucesso.';
        $status = 'ok';
        $request->session()->flash($status, $message);
        return redirect()->route('categorias');
    }

    public function storeCategoria(Request $request)
    {

        try {

            $categoria = new Categoria;
            $categoria->categoria = $request->input("categoria");
            $categoria->save();
            $message = 'Categoria cadastrada com sucesso.';
            $status = 'ok';
            $request->session()->flash($status, $message);
            return redirect()->route('categorias');
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'ProdutoController.storeCategoria', 'message' => $e->getMessage()]);
            $message = 'Erro ao tentar realizar cadastro de categoria!';
            $status = 'err';

            $request->session()->flash($status, $message);
            return redirect()->route('categorias');
        }
    }

    public function cadastrarCategoria()
    {
        return view("categoria.cadastrar");
    }

    public function cadastrarProduto()
    {
        $data = [];
        $listaCategorias = Categoria::all();
        $data["lista"] = $listaCategorias;
        return view("produto.cadastrar", $data);
    }

    public function store(Request $request)
    {
        try {

            $produto = new Produto;
            $produto->nome = $request->input("nome");
            $produto->valor = $request->input("valor");
            $produto->descricao = $request->input("descricao");
            $produto->peso = $request->input("peso");
            $produto->altura = $request->input("altura");
            $produto->largura = $request->input("largura");
            $produto->comprimento = $request->input("comprimento");
            $produto->categoria_id = $request->input("categoria");
            $produto->ativo = 1;
            $produto->save();

            for ($i = 29; $i <= 39; $i++) {
                $estoque = new Estoque([
                    'quantidade' => 0,
                    'numero' => $i,
                    'produto_id' => $produto->id
                ]);
                $estoque->save();
            }

            try {
                if ($request->hasFile('foto1')) {
                    $imagem = $request->file('foto1');
                    $nomeArquivo = $produto->id . '.png';
                    // Armazenar a imagem no caminho desejado
                    $uploadSucesso = $imagem->storeAs('public/images', $nomeArquivo);
                    if (!$uploadSucesso) {
                        // Adicione logs ou mensagens de erro, se necessário
                        throw new \Exception('Erro ao fazer upload da foto principal.');
                    }
                }
                if ($request->hasFile('foto2')) {
                    $imagem = $request->file('foto2');
                    $nomeArquivo = $produto->id . '_1.png';
                    // Armazenar a imagem no caminho desejado
                    $uploadSucesso = $imagem->storeAs('public/images', $nomeArquivo);
                    if (!$uploadSucesso) {
                        // Adicione logs ou mensagens de erro, se necessário
                        throw new \Exception('Erro ao fazer upload da foto secundaria 1.');
                    }
                }
                if ($request->hasFile('foto3')) {
                    $imagem = $request->file('foto3');
                    $nomeArquivo = $produto->id . '_2.png';
                    // Armazenar a imagem no caminho desejado
                    $uploadSucesso = $imagem->storeAs('public/images', $nomeArquivo);
                    if (!$uploadSucesso) {
                        // Adicione logs ou mensagens de erro, se necessário
                        throw new \Exception('Erro ao fazer upload da foto secundaria 2.');
                    }
                }
            } catch (\Exception $e) {
                Log::error("ERRO", ['file' => 'ProdutoController.store', 'message' => $e->getMessage()]);
            }

            $message = 'Produto cadastrado com sucesso.';
            $status = 'ok';
            $request->session()->flash($status, $message);
            return redirect()->route('produtos');
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'ProdutoController.store', 'message' => $e->getMessage()]);
            $message = 'Erro ao tentar realizar cadastro de produto!';
            $status = 'err';

            $request->session()->flash($status, $message);
            return redirect()->route('produtos');
        }
    }

    public function teste()
    {
        return view('teste');
    }

    public function produtos()
    {
        $listaProdutos = Produto::join('categorias', 'produtos.categoria_id', '=', 'categorias.id')
            ->select('produtos.*', 'categorias.categoria as nome_categoria')
            ->get();
        $data["lista"] = $listaProdutos;
        return view("produto.index", $data);
    }

    public function categorias()
    {
        $listaCategorias = Categoria::all();
        $data["lista"] = $listaCategorias;
        return view("categoria.index", $data);
    }

    public function itensPedido($id)
    {

        $itens = ItensPedido::join('produtos', 'itens_pedidos.produto_id', '=', 'produtos.id')
            ->where('itens_pedidos.pedido_id', $id)
            ->select('itens_pedidos.*', 'produtos.nome as nome_produto')
            ->get();

        return $itens;
    }

    public function exluirProduto($id)
    {
        $produto = Produto::find($id);
        return $produto;
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

    public function index()
    {
        $data = [];
        $listaProdutos = Produto::where('ativo', 1)->get();
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

    public function historico()
    {
        $data = [];
        $pedidos = Pedido::where('usuario_id', Auth::user()->id)->get();
        if (isset($pedidos)) {
            $data["lista"] = $pedidos;
        }
        return view('historico', $data);
    }

    public function compra_detalhes($id)
    {
        return $id;
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
