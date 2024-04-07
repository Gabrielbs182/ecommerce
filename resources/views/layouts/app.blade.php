@php
    $carrinho = session('cart', []);
    if ($carrinho > 0) {
        $quantidadeCarrinho = count($carrinho);
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-WR7GM9QG');
    </script>
    <!-- End Google Tag Manager -->

    <meta charset="utf-8">
    <title>Urbeat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="Urbeat Store" name="keywords">
    <meta content="Urbeat Store" name="description">
    <!-- Favicon -->
    <link href="{{ secure_asset('img/favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Libraries Stylesheet -->
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    @yield('head')
    @yield('scriptjs')

    <style>
        .modal-content {
            /* ou qualquer altura desejada em viewport height */
            overflow-y: auto;
        }

        .scrollable-div {
            overflow-y: auto;
            /* Adiciona rolagem vertical quando o conteúdo excede a altura */
            padding: 10px;
            /* Ajuste o preenchimento conforme necessário */
        }
    </style>
    <meta name="facebook-domain-verification" content="2q9pi2oocjb6nherzdll4l7znsazw0" />
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WR7GM9QG" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <style>
        #loadingSpinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            /* Ajuste conforme necessário para garantir que esteja acima de outros elementos */
        }
    </style>
    <div id="loadingSpinner" class="hidden">
        <!-- Coloque aqui a animação de carregamento (pode ser um gif, ícone animado, etc.) -->
        <img src="{{ asset('loading.gif') }}" alt="Carregando...">
    </div>
    <!-- Topbar Start -->
    <div class="container-fluid">
        @if ($message = Session::get('err'))
            <div class="alert alert-danger alert-dismissible fade show text-center fixed-top" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($message = Session::get('ok'))
            <div class="alert alert-success alert-dismissible fade show text-center fixed-top" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row bg-secondary py-2 px-xl-5">

            <div class="col-lg-6 d-none d-lg-block">

            </div>
            <div class="col-lg-6 text-center text-lg-right">
                <div class="d-inline-flex align-items-center">
                    <a class="text-dark px-2" href="https://wa.me/5527996658538">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a class="text-dark px-2" href="https://www.facebook.com/mulherurbanastore?mibextid=uzlsIk">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a class="text-dark px-2" href="https://instagram.com/use_mulherurbana?igshid=MzRlODBiNWFlZA==">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="row align-items-center py-3 px-xl-5">
            <div class="col-lg-3 d-none d-lg-block">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <h1 class="m-0 display-5 font-weight-semi-bold"><img src="{{ asset('URBEAT.png') }}" alt="..."
                            class="img-flui img-thumbnail pr-1" width="100" height="100">Urbeat</h1>
                </a>
            </div>

            <div class="col-lg-6 col-6 text-left">
                @if (Route::is('home') or Route::is('produto.categoria'))
                    <form action="">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar...">
                            <div class="input-group-append">
                                <span class="input-group-text bg-transparent text-primary">
                                    <i class="fa fa-search" style="color: #e63412;"></i>
                                </span>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
            <div class="col-lg-3 col-6 text-right"
                style="position: fixed;
            right: 30px;
            bottom: 30px;
            z-index: 11;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;">
                @if (Route::is('home') or Route::is('produto.categoria'))
                    <a class="btn border" onclick="abrirCarrinho()"><i class="fas fa-shopping-cart"
                            style="color: #e63412;"></i>
                        <span class="badge" id="quantidade-carrinho">
                            @if (isset($quantidadeCarrinho))
                                {{ $quantidadeCarrinho }}
                            @else
                                0
                            @endif
                            <a href="#" data-html="true" id="popover" data-placement="bottom"
                                data-toggle="popover" title="Produto adicionado"
                                data-content="Some content inside the popover"></a>
                        </span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <!-- Topbar End -->
    @yield('content')
    <!-- Footer Start -->
    <div class="container-fluid bg-secondary text-dark mt-5 pt-5">
        <div class="row px-xl-5 pt-5">
            <div class="col-lg-4 col-md-12 mb-5 pr-3 pr-xl-5">
                <a href="" class="text-decoration-none">
                    <h1 class="m-0 display-5 font-weight-semi-bold"><img src="{{ asset('URBEAT.png') }}"
                            alt="..." class="img-flui img-thumbnail pr-1" width="100" height="100">Urbeat
                    </h1>
                </a>
                <p>Seu estilo, nossa batida.</p>
                <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>contato@urbeatstore.com.br</p>
                <p class="mb-0"><i class="fa fa-phone-alt text-primary mr-3"></i>27 99665-8538</p>
            </div>
            <div class="col-lg-8 col-md-12">
                <div class="row">
                    <div class="col-md-4 mb-5">
                        <h5 class="font-weight-bold text-dark mb-4">Navegação</h5>
                        <div class="d-flex flex-column justify-content-start">
                            <a class="text-dark" href="{{ route('home') }}"><i
                                    class="fa fa-angle-right mr-2"></i>Início</a>
                            <a class="text-dark" href="{{ route('politica') }}"><i
                                    class="fa fa-angle-right mr-2"></i>Política de troca e devolução</a>
                            <a class="text-dark" href="https://wa.me/5527996658538"><i
                                    class="fa fa-angle-right mr-2"></i>Fale
                                Conosco</a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-5">

                    </div>
                    <div class="col-md-4 mb-5">
                        <h5 class="font-weight-bold text-dark mb-4">Contato</h5>
                        <form action="">
                            <div class="form-group">
                                <input type="text" class="form-control border-0 py-4" placeholder="Seu Nome"
                                    required="required" />
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control border-0 py-4" placeholder="Seu Email"
                                    required="required" />
                            </div>
                            <div>
                                <button class="btn btn-primary btn-block border-0 py-3" type="submit">Inscreva-se
                                    Agora</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row border-top border-light mx-xl-5 py-4">
        <div class="col-md-6 px-xl-0">
            <p class="mb-md-0 text-center text-md-left text-dark">
                &copy; <a class="text-dark font-weight-semi-bold" href="#">Urbeat</a>. All Rights Reserved.
                Designed
                by
                GB Consultoria</a><br>
            </p>
        </div>
        <div class="col-md-6 px-xl-0 text-center text-md-right">
            <img class="img-fluid" src="{{ asset('img/payments.png') }}" alt="">
        </div>
    </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();
        });
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
