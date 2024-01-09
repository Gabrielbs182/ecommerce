@extends('layouts.app')

@section('scriptjs')
    <script>
        function buscarProduto(id) {
            var asset = $('#asset').val();
            $.getJSON('/produto/' + id, function(produto) {
                console.log(produto)
                var foto = '<img class="w-100 h-100" src="' + asset +
                    '/' + produto[0].id + '.png" alt="Image">';
                var foto2 = '<img class="w-100 h-100" src="' + asset +
                    '/' + produto[0].id + '_2.png" alt="Image">';
                $('#primeiroCarrosel').empty();
                $('#segundoCarrosel').empty();
                $('#nomeProdModal').empty();
                $('#valorProdModal').empty();
                $('#comprar').empty();
                $('#comprarButton').empty();
                $('#idProduto').empty();
                $('#idProduto').val(produto[0].id);
                $('#primeiroCarrosel').append(foto);
                $('#segundoCarrosel').append(foto2);
                $('#nomeProdModal').append(produto[0].nome);
                var val = produto[0].valor.replace('.', ',');
                var valor = 'R$ ' + val;
                $('#valorProdModal').append(valor);
                $('#exampleModalCenter').modal('show');
                $.getJSON('/estoque/' + id, function(estoque) {
                    var testeTamanho = true;
                    estoque.forEach(function(item) {
                        if (item.quantidade > 0) {
                            if (testeTamanho) {
                                testeTamanho = false;
                                var comprar =
                                    '<div class="form-row">' +
                                    '<div class="form-group col-md-6">' +
                                    '<label for="tamanhoModal">Tamanho</label>' +
                                    '<select id="tamanhoModal" class="form-control" disabled onchange="buscarEstoque()" required>' +
                                    '<option selected></option>' +
                                    '</select>' +
                                    '</div>' +
                                    '<div class="form-group col-md-6" id="inputZipDiv">' +
                                    '<label for="inputZip">Quantidade</label>' +
                                    '<input type="number" class="form-control" id="inputZip" required>' +
                                    '</div>' +
                                    '</div>' +
                                    '</form>' +
                                    '<div id="comprarButton">' +
                                    '<button type="button" class="btn btn-primary"' +
                                    'onclick="adicionarCarrinho(' + produto[0].id +
                                    ')">Comprar</button>' +
                                    '</div>';
                                $('#comprar').append(comprar);
                            }
                            var tamanho = '<option value="' + item.numero + '">' + item.numero +
                                '</option>';
                            $('#tamanhoModal').append(tamanho);
                            $("#tamanhoModal").prop("disabled", false);
                        }
                    });
                    if (testeTamanho) {
                        var aviso = '<h1 class="font-weight-light text-center">Produto Esgotado</h1>';
                        $('#comprar').append(aviso);
                    }
                });
            });
        }

        function obterDadosCEP(cep) {
            // Montar a URL com o CEP fornecido
            var url = `https://viacep.com.br/ws/${cep}/json`;

            // Fazer a requisição usando a API fetch
            fetch(url)
                .then(response => {
                    // Verificar se a resposta da requisição está OK (código 200)
                    if (!response.ok) {
                        throw new Error(`Erro na requisição: ${response.status}`);
                    }
                    // Parsear a resposta JSON
                    return response.json();
                })
                .then(data => {
                    // Manipular os dados obtidos
                    console.log(data);

                    // Aqui você pode fazer o que quiser com os dados, por exemplo, preencher um formulário
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        };

        function calcularFrete() {
            $("#loader-div").show();
            // Obtenha o valor do CEP
            var cepValue = document.getElementById("cep").value;

            obterDadosCEP(cepValue);

            // Verifique se o CEP é válido antes de fazer a requisição
            if (validarCEP(cepValue)) {
                console.log('Cep é valido');
                // Faça a requisição para calcular o frete
                $.getJSON('/melhorenvio/calcularFrete/' + cepValue, function(frete) {
                    console.log('iniciou calculo do');

                    document.getElementById("envios").innerHTML = '';

                    linhas =
                        '<legend class="col-form-label col-sm-2 pt-0">A domicílio</legend>' +
                        '<div class="col-sm-10">';

                    for (var transportadora of frete) {
                        if ('error' in transportadora) {
                            console.log("Tipo de envio " + transportadora.name + " Contem um erro:");
                            console.log("Transportadora = " + transportadora.company.name + "Contem um erro:");
                            console.log(transportadora.error);
                        } else {
                            linha = montarLinhaEnvio(transportadora);
                            linhas += linha;
                        }
                    }

                    linhas +=
                        '</div>' +
                        '<legend class="col-form-label col-sm-2 pt-0">Nossa loja</legend>' +
                        '<div class="col-sm-10">' +
                        '<div class="form-check">' +
                        '<input class="form-check-input" type="radio" name="frete"' +
                        'id="gratis" value="' + 0 + '" checked>' +
                        '<label class="form-check-label" for="gratis">' +
                        'Rua santa catarina, SN, Carapicuiba, Guarapari, Es, 29160-988 - Grátis' +
                        '</label>' +
                        '</div>' +
                        '</div>';

                    console.log(linhas);
                    document.getElementById("envios").innerHTML = linhas;
                });
            } else {
                // Exibir mensagem de erro para CEP inválido
                $("#cep-error-message").text("CEP inválido");
                console.log('Cep é invalido');
                // Adicionar classe para destacar o campo em vermelho
                $("#cep-group").addClass("has-error");
            }
        };

        function validarNumero(input) {
            // Remover caracteres não numéricos
            input.value = input.value.replace(/\D/g, '');

            // Limitar o comprimento conforme o atributo maxlength
            if (input.value.length > parseInt(input.getAttribute('maxlength'))) {
                input.value = input.value.slice(0, input.getAttribute('maxlength'));
            }
        }

        function validarCEP(cep) {
            // Remover caracteres não numéricos
            var cepNumerico = cep.replace(/\D/g, '');

            // Verificar se o CEP tem exatamente 8 dígitos
            if (cepNumerico.length === 8) {
                return true;
            } else {
                return false;
            }
        };

        function montarLinhaEnvio(transportadora) {

            linha =
                '<div class="form-check">' +
                '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
                transportadora.id + '/' + transportadora.name + '/' + transportadora.price + '" >' +
                '<label class="form-check-label" for="' + transportadora.id + '">' +
                transportadora.company.name + ' - ' + transportadora.name + ' R$ ' + transportadora.price +
                '</label>' +
                '</div>';
            return linha;
        }

        function buscarEstoque() {
            var numero = $('#tamanhoModal').val()
            var idProduto = $('#idProduto').val()
            $.getJSON('/estoqueNumero/' + idProduto + '/' + numero, function(estoque) {
                $('#estoque').val(estoque[0].quantidade);

            });
        };

        function removerProdutoCarrinho(id) {
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: '/' + id + '/excluirCarrinho',
                context: this,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function() {
                    console.log('item excluido')
                    $.getJSON('/buscarCarrinho', function(data) {
                        var total = 0;
                        var indice = 0;
                        $('#modalCarrinhoLista').empty();
                        if (data.length === 0) {
                            carregarCarrinho();
                            $('#modalCarrinho').modal('hide');
                        } else {
                            carregarCarrinho();
                        }
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });

        }

        function carregarCarrinho() {

            $.getJSON('/buscarCarrinho', function(data) {

                // O objeto retornado é um array
                var total = 0;
                var indice = 0;
                $('#modalCarrinhoLista').empty();
                console.log('carregarCarrinho');
                console.log(data);

                // Verificar se data é um objeto
                if (typeof data === 'object' && data !== null) {
                    // Iterar sobre as propriedades do objeto
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            var item = data[key];

                            var asset = $('#asset').val();
                            total += item.quantidade * item.valor;
                            var itemCarrinho =
                                '<tr>' +
                                '<td class="w-20 align-middle text-center">' +
                                '<img src="' + asset +
                                '/' + item.id +
                                '.png")" class="img-fluid img-thumbnail" width="100" height="100" ' +
                                'alt="Produto" >' + item.nome + '/' + item.numero + ' x ' + item.quantidade +
                                '</td>' +
                                '<td class="align-middle text-center"> R$' + item.valor + '</td>' +
                                '<td class="align-middle text-center"> R$' + (item.quantidade * item.valor) +
                                '</td>' +
                                '<td class="align-middle text-center">' +
                                '<a href="#" class="btn btn-danger btn-sm" onclick="removerProdutoCarrinho(' +
                                key + ')">' +
                                '<i class="fa fa-times"></i>' +
                                '</a>' +
                                '</td>' +
                                '</tr>';
                            $('#modalCarrinhoLista').append(itemCarrinho);
                            indice++
                        }
                    }

                    $('#totalCarrinho').empty();
                    $('#totalCarrinho').append(total);
                } else {
                    console.error('O JSON retornado não é um array:', data);
                }
                $('#totalCarrinho').empty();
                $('#totalCarrinho').append(total);
            });
            $.get('/atualizar-quantidade-carrinho', function(data) {
                // Atualize a quantidade na página
                if (data && data.quantidadeCarrinho !== undefined) {
                    $('#quantidade-carrinho').text(data.quantidadeCarrinho);
                }
            });
        };

        function abrirCarrinho() {
            $.getJSON('/buscarCarrinho', function(data) {
                var total = 0;
                var indice = 0;
                $('#modalCarrinhoLista').empty();
                if (data.length === 0) {
                    $('#modalCarrinhoVazio').modal('show');
                } else {
                    carregarCarrinho();
                    $('#modalCarrinho').modal('show');
                }
            });
        };

        function buscarEstoqueCarrinho(id, callback) {
            var numero = $('#tamanhoModal').val();
            console.log(numero);
            console.log(id);
            $.getJSON('/buscarItemCarrinho/' + id + '/' + numero, function(data) {
                console.log(data);
                callback(data); // Chama o callback com os dados recebidos
            });
        }

        function adicionarCarrinho(id) {
            var token = $('meta[name="csrf-token"]').attr('content');
            var estoque = $('#estoque').val();
            var numero = $('#tamanhoModal').val();
            quantidade = parseInt($('#inputZip').val());

            // Use uma função de callback para obter os dados assíncronos
            buscarEstoqueCarrinho(id, function($item) {
                console.log($item);
                if ($item == 1) {
                    console.log('Item não existe no carrinho');

                } else {
                    console.log('Item existe no carrinho');
                    console.log(quantidade);
                    quantidade += parseInt($item.quantidade);
                    console.log(quantidade);
                }
                // Restante do seu código...
                console.log(quantidade);
                console.log(estoque);
                if (parseInt(quantidade) > parseInt(estoque)) {
                    console.log('Não tem estoque');
                    $('#inputZip').val('');
                    var alerta =
                        '<div class="alert alert-danger alert-dismissible fade show text-center fixed-top" role="alert">' +
                        'Estoque insuficiente, tente uma quantidade menor.' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';
                    $('#modalAlert').append(alerta);
                } else {
                    console.log('Tem estoque');

                    $.getJSON('/produto/' + id, function(produto) {
                        var quantidade = $('#inputZip').val()
                        var token = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            type: 'POST',
                            url: '/carrinho/adicionar/' + id + '/' + quantidade + '/' + numero,
                            context: this,
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            success: function() {
                                $.get('/atualizar-quantidade-carrinho', function(data) {
                                    // Atualize a quantidade na página
                                    if (data && data.quantidadeCarrinho !== undefined) {
                                        $('#quantidade-carrinho').text(data
                                            .quantidadeCarrinho);
                                    }
                                });
                                $('#exampleModalCenter').modal('hide');
                            },
                            error: function(error) {
                                console.log(error);
                            }
                        });
                    });
                }
            });
        }
    </script>
@endsection

@section('content')
    {{-- Asset --}}
    <input type="hidden" value="{{ asset('images') }}" id="asset">
    <input type="hidden" value="" id="estoque">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Modais --}}
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content" id="modalbody">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid" id="modalAlert">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner border">
                                        <div class="carousel-item active" id="primeiroCarrosel">
                                            <img class="w-100 h-100" src="{{ asset('images/2.png') }}" alt="Image">
                                        </div>
                                        <div class="carousel-item" id="segundoCarrosel">
                                            <img class="w-100 h-100" src="{{ asset('images/2_2.png') }}" alt="Image">
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                                        <i class="fa fa-2x fa-angle-left text-dark"></i>
                                    </a>
                                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                                        <i class="fa fa-2x fa-angle-right text-dark"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 ml-auto">
                                <form>
                                    <input type="hidden" value="" id="idProduto">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <h1 class="display-4 font-weight-light" id="nomeProdModal">Tenis X</h1>
                                            <p class="h4 font-weight-bold" id="valorProdModal">R$ 246,00</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="descricaoProdModal">Descrição:</label>
                                            <p id="descricaoProdModal">Uma sandalia top, muito confortavel, estou escrevendo
                                                um grande texto para testar.</p>
                                        </div>
                                    </div>
                                    <div id="comprar">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="tamanhoModal">Tamanho</label>
                                                <select id="tamanhoModal" class="form-control" disabled
                                                    onchange="buscarEstoque()" required>
                                                    <option selected>Produto Esgotado</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6" id="inputZipDiv">
                                                <label for="inputZip">Quantidade</label>
                                                <input type="number" class="form-control" id="inputZip" required>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div id="comprarButton">
                                    <button type="button" class="btn btn-primary"
                                        onclick="adicionarCarrinho()">Comprar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- carrinho -->

    <div class="modal fade" id="modalCarrinho" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable justify-content-center"
            role="document">

            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Carrinho
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="carrinhoModal">
                    <div class="d-flex justify-content-center">
                        <p>Subtotal (sem frete): R$<span id="totalCarrinho"></span></p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">Produto</th>
                                        <th scope="col" class="text-center">Preço</th>
                                        <th scope="col" class="text-center">Total</th>
                                        <th scope="col" class="text-center">Excluir</th>
                                    </tr>
                                </thead>
                                <tbody id="modalCarrinhoLista">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <form action="{{ route('ver_carrinho') }}" method="POST">
                        @csrf
                        <div class="d-flex">
                            <p>
                                Meios de envio:
                            </p>
                        </div>
                        <div class="row">
                            <div class="d-flex col-md-6 form-group" id="cep-group">
                                <input placeholder="Seu Cep" name="cep" class="form-control" type="text"
                                    id="cep" maxlength="10" oninput="validarNumero(this)">
                            </div>
                            <div class="d-flex col-md-6 form-group">
                                <button type="button" class="btn btn-primary" onclick="calcularFrete()">Calcular
                                    Frete</button>
                            </div>
                        </div>

                        <fieldset class="form-group">
                            <div class="row" id="envios">
                                <legend class="col-form-label col-sm-2 pt-0">Nossa loja</legend>
                                <div class="col-sm-10">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="frete" id="gridRadios1"
                                            value="0" checked>
                                        <label class="form-check-label" for="gridRadios1">
                                            Rua santa catarina2, SN, Carapicuiba, Guarapari, Es, 29160-988 - Grátis
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                </div>

                <div class="modal-footer border-top-0 d-flex justify-content-right">

                    <button type="submit" class="btn btn-primary float-right">Finalizar Compra</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- carrinho vazio -->

    <div class="modal fade" id="modalCarrinhoVazio" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable justify-content-center"
            role="document">
            <div class="modal-content">
                <div class="container-fluid  mt-100">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body cart">
                                <div class="col-sm-12 empty-cart-cls text-center">
                                    <img src="https://i.imgur.com/dCdflKN.png" width="130" height="130"
                                        class="img-fluid mb-4 mr-3">
                                    <h3><strong>Seu carrinho está vazio</strong></h3>
                                    <h4>Adicione alguns produtos para que possa seguir com a compra :)</h4>
                                    <a href="#" class="btn btn-primary cart-btn-transform m-3" data-abc="true"
                                        data-dismiss="modal">Continuar Comprando</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- partial -->

    <!-- Navbar Start -->
    <div class="container-fluid mb-5">
        <div class="row border-top px-xl-5">
            {{-- DESKTOP --}}
            <div class="col-lg-3 d-none d-lg-block">
                <a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100"
                    data-toggle="collapse" href="#navbar-vertical"
                    style="height: 65px; margin-top: -1px; padding: 0 30px;">
                    <h6 class="m-0" style="color: #e63412;">Categorias</h6>
                    <i class="fa fa-angle-down text-dark"></i>
                </a>
                <nav class="collapse show navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0"
                    id="navbar-vertical">
                    <div class="navbar-nav w-100 overflow-hidden" style="height: 410px">
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link" data-toggle="dropdown">Casual <i
                                    class="fa fa-angle-down float-right mt-1"></i></a>
                            <div class="dropdown-menu position-absolute bg-secondary border-0 rounded-0 w-100 m-0">
                                <a href="" class="dropdown-item">Masculino</a>
                                <a href="" class="dropdown-item">Feminino</a>
                                <a href="" class="dropdown-item">Infantil</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link" data-toggle="dropdown">Esportivo <i
                                    class="fa fa-angle-down float-right mt-1"></i></a>
                            <div class="dropdown-menu position-absolute bg-secondary border-0 rounded-0 w-100 m-0">
                                <a href="" class="dropdown-item">Masculino</a>
                                <a href="" class="dropdown-item">Feminino</a>
                                <a href="" class="dropdown-item">Infantil</a>
                            </div>
                        </div>
                        <a href="" class="nav-item nav-link">Skate</a>
                        <a href="" class="nav-item nav-link">Sandalias</a>
                        <a href="" class="nav-item nav-link">Salto</a>
                        <a href="" class="nav-item nav-link">Tenis</a>
                    </div>
                </nav>
            </div>
            <div class="col-lg-9">

                {{-- SMARTPHONE --}}
                <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0">
                    <a href="" class="text-decoration-none d-block d-lg-none">
                        <h1 class="m-0 display-5 font-weight-semi-bold"><img src="{{ asset('URBEAT.png') }}"
                                alt="..." class="img-flui img-thumbnail pr-1" width="100" height="100">Urbeat
                        </h1>
                    </a>
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav mr-auto py-0">
                            <a href="index.html" class="nav-item nav-link active">Inicio</a>
                            <a href="contact.html" class="nav-item nav-link">Contato</a>
                        </div>
                        <div class="navbar-nav ml-auto py-0">
                            @guest
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Minha
                                        Conta</a>
                                    <div class="dropdown-menu rounded-0 m-0">
                                        <a href="{{ route('logar') }}" class="dropdown-item">Login</a>
                                        <a href="{{ route('cadastrar') }}" class="dropdown-item">Cadastro</a>
                                    </div>
                                </div>
                            @endguest
                            @auth
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle"
                                        data-toggle="dropdown">{{ Auth::user()->nome }}</a>
                                    <div class="dropdown-menu rounded-0 m-0">
                                        <a href="{{ route('compra_historico') }}" class="dropdown-item">Minhas Compras</a>
                                        @if (Auth::user()->isAdmin == 1)
                                            <a href="{{ route('melhorenvio.authorize') }}" class="dropdown-item">Melhor
                                                envio</a>
                                            <a href="{{ route('melhorInfo') }}" class="dropdown-item">Melhor
                                                envio - Informações</a>
                                        @endif
                                        <a href="{{ route('sair') }}" class="dropdown-item">Sair</a>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </nav>
                <div id="header-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="height: 410px;">
                            <img class="img-fluid" src="img/carousel-1.png" alt="Imagem">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 700px;">
                                    <h4 class="text-light text-uppercase font-weight-medium mb-3">10% de Desconto em
                                        Seu
                                        Primeiro Pedido</h4>
                                    <h3 class="display-4 text-white font-weight-semi-bold mb-4">Tenis Fashion</h3>
                                    <a href="" class="btn btn-light py-2 px-3">Compre Agora</a>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item" style="height: 410px;">
                            <img class="img-fluid" src="img/carousel-2.png" alt="Imagem">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 700px;">
                                    <h4 class="text-light text-uppercase font-weight-medium mb-3">10% de Desconto em
                                        Seu
                                        Primeiro Pedido</h4>
                                    <h3 class="display-4 text-white font-weight-semi-bold mb-4">Preço Razoável</h3>
                                    <a href="" class="btn btn-light py-2 px-3">Compre Agora</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                        <div class="btn btn-dark" style="width: 45px; height: 45px;">
                            <span class="carousel-control-prev-icon mb-n2"></span>
                        </div>
                    </a>
                    <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                        <div class="btn btn-dark" style="width: 45px; height: 45px;">
                            <span class="carousel-control-next-icon mb-n2"></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Products Start -->
    <div class="container-fluid pt-5">
        <div class="text-center mb-4">
            <h2 class="section-title px-5"><span class="px-2">Produtos</span></h2>
        </div>
        <div class="row px-xl-5 pb-3">
            @include('layouts._produtos', ['lista' => $lista])
        </div>
    </div>
    </div>
@endsection
