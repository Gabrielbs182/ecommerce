@extends('layouts.app')

@section('scriptjs')
    <script src="{{ asset('lib/urbeatFunctions/urbeat.js') }}"></script>
@endsection

@section('content')
    {{-- Asset --}}
    <input type="hidden" value="{{ asset('images') }}" id="asset">
    <input type="hidden" value="" id="estoque">
    <input type="hidden" value="{{ $frete299 }}" id="frete299">
    <input type="hidden" value="{{ $freteFixo }}" id="freteFixo">
    <input type="hidden" value="{{ $motoboy }}" id="motoboy">
    <input type="hidden" value="{{ $motoboyVix }}" id="motoboyVix">
    <input type="hidden" value="{{ $motoboySerra }}" id="motoboySerra">
    <input type="hidden" value="{{ $motoboyCariacica }}" id="motoboyCariacica">
    <input type="hidden" value="{{ $motoboyVV }}" id="motoboyVV">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Modais --}}
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="height: 45%;" role="document">
            <div class="modal-content" id="modalbody">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
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
                                    <div class="carousel-item" id="terceiroCarrosel">
                                        <img class="w-100 h-100" src="{{ asset('images/2_3.png') }}" alt="Image">
                                    </div>
                                    <div class="carousel-item" id="quartoCarrosel">
                                        <img class="w-100 h-100" src="{{ asset('images/2_4.png') }}" alt="Image">
                                    </div>
                                    <div class="carousel-item" id="quintoCarrosel">
                                        <img class="w-100 h-100" src="{{ asset('images/2_5.png') }}" alt="Image">
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
                        <div class="col-md-6 ml-auto scrollable-div">
                            <form>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <h1 class="display-4 font-weight-light" id="nomeProdModal">Tenis X</h1>
                                        <p class="h4 font-weight-bold" id="valorProdModal">R$ 246,00</p>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="descricaoProdModal">Descrição:</label>
                                        <p id="descricaoProdModal">Uma sandalia top, muito confortavel, estou
                                            escrevendo
                                            um grande texto para testar.</p>
                                    </div>
                                </div>
                                <div id="comprar">
                                    <div class="form-row">
                                        <input type="hidden" value="" id="idProduto">
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
                    <div class="row">
                        <div class="d-flex col-md-2">

                        </div>
                        <div class="form-group col-md-8">
                            <form action="{{ route('ver_carrinho') }}" method="POST">
                                @csrf
                                <div class="d-flex">
                                    <p>
                                        Meios de envio:
                                    </p>
                                </div>
                                <div class="row">
                                    <div class="d-flex col-md-8 form-group" id="cep-group">
                                        <input placeholder="Seu Cep" name="cep" class="form-control" type="text"
                                            id="cep" maxlength="10" oninput="validarNumero(this)">
                                    </div>
                                    <div class="d-flex col-md-4 form-group">
                                        <button type="button" class="btn btn-primary" onclick="calcularFrete()">Calcular
                                            Frete</button>
                                    </div>
                                </div>

                                <fieldset class="form-group">
                                    <div class="row" id="envios">
                                    </div>
                                </fieldset>
                        </div>
                        <div class="d-flex col-md-2">

                        </div>
                    </div>

                </div>
                </form>
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
            <div class="col-lg-3 d-lg-block">
                <a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100"
                    data-toggle="collapse" href="#navbar-vertical"
                    style="height: 65px; margin-top: -1px; padding: 0 30px;">
                    <h6 class="m-0" style="color: #e63412;">Categorias</h6>
                    <i class="fa fa-angle-down text-dark"></i>
                </a>
                <nav class="navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0 collapse"
                    id="navbar-vertical">
                    <div class="navbar-nav w-100 overflow-hidden">
                        @if (isset($categorias))
                            @foreach ($categorias as $c)
                                <a href="{{ route('produto.categoria', $c->id) }}"
                                    class="nav-item nav-link">{{ $c->categoria }}</a>
                            @endforeach
                        @endif
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
                        </div>
                        <div class="navbar-nav ml-auto py-0">
                            @guest
                                <a href="{{ route('logar') }}" class="nav-item nav-link">Logar</a>
                            @endguest
                            @auth
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle"
                                        data-toggle="dropdown">{{ Auth::user()->nome }}</a>
                                    <div class="dropdown-menu rounded-0 m-0">
                                        @if (Auth::user()->isAdmin == 0)
                                            <a href="{{ route('compra_historico') }}" class="dropdown-item">Minhas
                                                Compras</a>
                                        @endif
                                        @if (Auth::user()->isAdmin == 1)
                                            <a href="{{ route('produtos') }}" class="dropdown-item">Produtos</a>
                                            <a href="{{ route('categorias') }}" class="dropdown-item">Categorias</a>
                                            <a href="{{ route('cupons') }}" class="dropdown-item">Cupons</a>
                                            <a href="{{ route('configuracoes') }}" class="dropdown-item">Configurações</a>
                                            <a href="{{ route('pedidos.pendentes') }}" class="dropdown-item">Pedidos
                                                Pendentes</a>
                                            <a href="{{ route('pedidos.todos') }}" class="dropdown-item">Todos os pedidos</a>
                                            {{-- <a href="{{ route('melhorenvio.authorize') }}" class="dropdown-item">Melhor
                                                envio</a>
                                            <a href="{{ route('melhorInfo') }}" class="dropdown-item">Melhor
                                                envio - Informações</a> --}}
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
                        <div class="carousel-item active">
                            <img class="img-fluid" src="{{ asset('img') }}/carousel-1.png" alt="Imagem">
                            
                        </div>
                        <div class="carousel-item">
                            <img class="img-fluid" src="{{ asset('img') }}/carousel-2.png" alt="Imagem">
                            
                        </div>
                        <div class="carousel-item">
                            <img class="img-fluid" src="{{ asset('img') }}/carousel-3.png" alt="Imagem">
                            
                        </div>
                    </div>
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
