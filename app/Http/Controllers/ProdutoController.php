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

    public function finalizarPedido($id, Request $request)
    {
        $pedido = Pedido::find($id);
        $pedido->status_env = 'Enviado';
        $melhorEnvio = new MelhorEnvioController();
        $resposta = $melhorEnvio->buscarRastreamento($pedido->cod_envio);
        $respostaJson = json_decode($resposta);

        // Verifique se a respostaJson é um objeto e tem a chave desejada
        if ($respostaJson && is_object($respostaJson) && property_exists($respostaJson, $pedido->cod_envio)) {
            $rastreamentoObj = $respostaJson->{$pedido->cod_envio};

            // Verifique se a chave "tracking" existe dentro do objeto de rastreamento
            if (is_object($rastreamentoObj) && property_exists($rastreamentoObj, 'tracking')) {
                $cod_rastreamento = $rastreamentoObj->tracking;
                $pedido->cod_rastreamento = $cod_rastreamento;
                $pedido->save();

                $message = 'Pedido finalizado.';
                $status = 'ok';
                $request->session()->flash($status, $message);

                return route('pedidos.pendentes');
            }
        }

        // Lida com o caso em que a estrutura do JSON não é como esperado
        Log::error("ERRO", ['file' => 'NomeDoSeuArquivo', 'message' => 'Não foi possível obter o código de rastreamento']);
        // Adicione qualquer tratamento de erro adicional necessário

        return redirect()->back(); // ou redirecione para outra página, dependendo do seu fluxo de trabalho
    }

    public function pedidosPendentes()
    {
        $data = [];
        $pedidos = Pedido::where('status_env', 'Envio iniciado')->get();
        if (isset($pedidos)) {
            $data["lista"] = $pedidos;
        }
        return view('pedidosPendentes', $data);
    }

    public function atualizarEstoque(Request $request)
    {
        $dadosEstoque = json_decode($request->input('dados_estoque'), true);

        foreach ($dadosEstoque as $item) {
            $estoque = Estoque::find($item['id']);
            if ($estoque) {
                $estoque->update(['quantidade' => $item['quantidade']]);
            }
        }

        $message = 'Estoque atualizado com sucesso.';
        $status = 'ok';
        $request->session()->flash($status, $message);
        return redirect()->route('produtos');
    }

    public function estoqueProduto($id)
    {
        $listaEstoque = Estoque::join('produtos', 'estoque.produto_id', '=', 'produtos.id')
            ->select('estoque.*', 'produtos.nome as nome_produto')
            ->where('estoque.produto_id', '=', $id)
            ->get();
        $data["lista"] = $listaEstoque;
        return view("estoque.index", $data);
    }

    public function configuracoes()
    {
        $pagEmail = env('PAGSEGURO_EMAIL');
        $pagToken = env('PAGSEGURO_TOKEN');
        $melhorName = env('MELHORENVIO_NAME');
        $melhorEmail = env('MELHORENVIO_EMAIL');
        $melhorPhone = env('MELHORENVIO_PHONE');
        $melhorDocument = env('MELHORENVIO_DOCUMENT');
        $melhorUF = env('MELHORENVIO_STATE_REGISTER');
        $melhorLog = env('MELHORENVIO_ADDRESS');
        $melhorComple = env('MELHORENVIO_COMPLEMENT');
        $melhorNumber = env('MELHORENVIO_NUMBER');
        $melhorDistrict = env('MELHORENVIO_DISTRICT');
        $melhorCep = env('MELHORENVIO_POSTAL_CODE');
        $melhorCity = env('MELHORENVIO_CITY');
        $melhorUFAbbr = env('MELHORENVIO_STATE_ABBR');
        $melhorClientID = env('CLIENT_ID');
        $melhorClientSecret = env('CLIENT_SECRET');
        $melhorScope = env('SCOPE');
        $melhorUserAgent = env('USER_AGENT');
        $data = [];
        $data['pagEmail'] = $pagEmail;
        $data['pagToken'] = $pagToken;
        $data['melhorName'] = $melhorName;
        $data['melhorEmail'] = $melhorEmail;
        $data['melhorPhone'] = $melhorPhone;
        $data['melhorDocument'] = $melhorDocument;
        $data['melhorUF'] = $melhorUF;
        $data['melhorLog'] = $melhorLog;
        $data['melhorComple'] = $melhorComple;
        $data['melhorNumber'] = $melhorNumber;
        $data['melhorDistrict'] = $melhorDistrict;
        $data['melhorCity'] = $melhorCity;
        $data['melhorCep'] = $melhorCep;
        $data['melhorUFAbbr'] = $melhorUFAbbr;
        $data['melhorClientID'] = $melhorClientID;
        $data['melhorClientSecret'] = $melhorClientSecret;
        $data['melhorScope'] = $melhorScope;
        $data['melhorUserAgent'] = $melhorUserAgent;
        return view('configuracoes', $data);
    }

    public function configuracoesUpdate(Request $request)
    {
        $PAGSEGURO_EMAIL = $request->input('pagSeguroEmail');
        $PAGSEGURO_TOKEN = $request->input('pagSeguroToken');
        $MELHORENVIO_NAME = $request->input('melhorEnvioNome');
        $MELHORENVIO_PHONE = $request->input('melhorEnvioTelefone');
        $MELHORENVIO_EMAIL = $request->input('melhorEnvioEmail');
        $MELHORENVIO_DOCUMENT = $request->input('melhorEnvioDocumento');
        $MELHORENVIO_STATE_REGISTER = $request->input('melhorEnvioUF');
        $MELHORENVIO_ADDRESS = $request->input('melhorEnvioLog');
        $MELHORENVIO_COMPLEMENT = $request->input('melhorEnvioComp');
        $MELHORENVIO_NUMBER = $request->input('melhorEnvioNumber');
        $MELHORENVIO_DISTRICT = $request->input('melhorDistrict');
        $MELHORENVIO_CITY = $request->input('melhorCity');
        $MELHORENVIO_POSTAL_CODE = $request->input('melhorCep');
        $CLIENT_SECRET = $request->input('melhorEnvioClientSecret');
        $USER_AGENT = $request->input('melhorEnvioUserAgent');
        $SCOPE = $request->input('melhorEnvioEscopo');
        $CLIENT_ID = $request->input('melhorEnvioClientID');
        $this->AtualizarVariaveisAmbiente('PAGSEGURO_EMAIL', $PAGSEGURO_EMAIL);
        $this->AtualizarVariaveisAmbiente('PAGSEGURO_TOKEN', $PAGSEGURO_TOKEN);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_NAME', $MELHORENVIO_NAME);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_PHONE', $MELHORENVIO_PHONE);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_EMAIL', $MELHORENVIO_EMAIL);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_DOCUMENT', $MELHORENVIO_DOCUMENT);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_STATE_REGISTER', $MELHORENVIO_STATE_REGISTER);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_STATE_ABBR', $MELHORENVIO_STATE_REGISTER);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_ADDRESS', $MELHORENVIO_ADDRESS);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_COMPLEMENT', $MELHORENVIO_COMPLEMENT);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_NUMBER', $MELHORENVIO_NUMBER);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_DISTRICT', $MELHORENVIO_DISTRICT);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_CITY', $MELHORENVIO_CITY);
        $this->AtualizarVariaveisAmbienteComAspas('MELHORENVIO_POSTAL_CODE', $MELHORENVIO_POSTAL_CODE);
        $this->AtualizarVariaveisAmbienteComAspas('CLIENT_SECRET', $CLIENT_SECRET);
        $this->AtualizarVariaveisAmbiente('CLIENT_ID', $CLIENT_ID);
        $this->AtualizarVariaveisAmbienteComAspas('SCOPE', $SCOPE);
        $this->AtualizarVariaveisAmbienteComAspas('USER_AGENT', $USER_AGENT);
        $message = 'Variáveis atulizadas com sucesso.';
        $status = 'ok';
        $request->session()->flash($status, $message);
        return redirect()->route('configuracoes');
    }

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
                    $uploadSucesso = $imagem->move(public_path('images'), $nomeArquivo);;
                    if (!$uploadSucesso) {
                        // Adicione logs ou mensagens de erro, se necessário
                        throw new \Exception('Erro ao fazer upload da foto principal.');
                    }
                }
                if ($request->hasFile('foto2')) {
                    $imagem = $request->file('foto2');
                    $nomeArquivo = $produto->id . '_1.png';
                    // Armazenar a imagem no caminho desejado
                    $uploadSucesso = $imagem->move(public_path('images'), $nomeArquivo);
                    if (!$uploadSucesso) {
                        // Adicione logs ou mensagens de erro, se necessário
                        throw new \Exception('Erro ao fazer upload da foto secundaria 1.');
                    }
                }
                if ($request->hasFile('foto3')) {
                    $imagem = $request->file('foto3');
                    $nomeArquivo = $produto->id . '_2.png';
                    // Armazenar a imagem no caminho desejado
                    $uploadSucesso = $imagem->move(public_path('images'), $nomeArquivo);
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
            Log::error("ERRO", "AtualizarVariaveisAmbiente, Chave ou valor vazios.");
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

        Log::info("Variavel atualizada, Chave:"  . $chave . " Valor: " . $valor . "");
    }

    public function AtualizarVariaveisAmbienteComAspas(String $chave, String $valor)
    {
        // Verificações de parâmetros
        if (empty($chave) || empty($valor)) {
            // Trate como um erro ou apenas retorne, dependendo da lógica do seu aplicativo
            Log::error("ERRO", "Erro: Chave ou valor vazios.");
            return "Erro: Chave ou valor vazios.";
        }
        // Buscando o conteúdo do .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        // Adicione aspas simples ao redor do valor
        $valorComAspasSimples = "'" . $valor . "'";
        // Atualizar ou adicionar as chaves no arquivo .env
        if (strpos($envContent, $chave) !== false) {
            // Atualizar o valor se já existir
            $envContent = preg_replace('/(' . $chave . ')=(.*)/', '' . $chave . '=' . $valorComAspasSimples, $envContent);
        } else {
            // Adicionar uma nova linha com a chave e os tokens
            $envContent .= "\n" . $chave . "=" . $valorComAspasSimples;
        }
        // Salvar as alterações no arquivo .env
        file_put_contents($envPath, $envContent);

        Log::info("Variavel atualizada, Chave:"  . $chave . " Valor: " . $valorComAspasSimples . "");
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
