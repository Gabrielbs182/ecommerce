@if (isset($lista))
    @foreach ($lista as $prod)
        <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
            <div class="card product-item border-1 mb-4" style="height: 450px; max-width: 300px;">
                <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0"
                    style="height: 300px;">
                    <img class="img-fluid mw-100" src=" {{ asset('images') . '/' . $prod->id }}.png" alt="">
                </div>
                <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                    <h6 class="text-truncate mb-3">{{ $prod->nome }}</h6>
                    <div class="d-flex justify-content-center">
                        <h6>R${{ $prod->valor }}</h6>
                        @if ($prod->desconto > 0)
                            <h6 class="text-muted ml-2">
                                <del>R${{ $prod->valor - $prod->valor * ($prod->desconto / 100) }}</del>
                            </h6>
                        @endif
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between bg-light border">
                    <a class="btn btn-sm text-dark p-0" onclick="buscarProduto({{ $prod->id }})"><i
                            class="fas fa-shopping-cart mr-1" style="color:#e63412;"></i>Comprar</a>
                </div>
            </div>
        </div>
    @endforeach
@endif
