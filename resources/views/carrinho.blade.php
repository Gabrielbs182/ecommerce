@extends('layouts.app')

@section('content')
    <!-- Checkout Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5">
            <div class="col-lg-8">
                <div class="mb-4">
                    <h4 class="font-weight-semi-bold mb-4">Endereço de entrega</h4>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Endereço 1</label>
                            <input class="form-control" type="text" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Endereço 2</label>
                            <input class="form-control" type="text" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Cidade</label>
                            <input class="form-control" type="text" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>UF</label>
                            <input class="form-control" type="text" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>CEP</label>
                            <input class="form-control" type="text" required>
                        </div>
                    </div>
                </div>
            </div>

            @if (isset($cart) && count($cart) > 0)
                <div class="col-lg-4">
                    <div class="card border-secondary mb-5">
                        <div class="card-header bg-secondary border-0">
                            <h4 class="font-weight-semi-bold m-0">Total do pedido</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="font-weight-medium mb-3">Produtos</h5>
                            @php $total = 0; @endphp
                            @foreach ($cart as $indice => $p)
                                <div class="d-flex justify-content-between">
                                    <p>{{ $p->nome }} x {{ $p->quantidade }}</p>
                                    <p>{{ $p->valor * $p->quantidade }}</p>
                                </div>
                                @php $total += ($p->valor*$p->quantidade); @endphp
                            @endforeach
                            <hr class="mt-0">
                            <div class="d-flex justify-content-between mb-3 pt-1">
                                <h6 class="font-weight-medium">Subtotal</h6>
                                <h6 class="font-weight-medium">R$ {{ $total }}</h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-medium">Frete</h6>
                                <h6 class="font-weight-medium">R$10</h6>
                            </div>
                        </div>
                        <div class="card-footer border-secondary bg-transparent">
                            <div class="d-flex justify-content-between mt-2">
                                <h5 class="font-weight-bold">Total</h5>
                                <h5 class="font-weight-bold">R$ {{ $total + 10 }}</h5>
                            </div>
                        </div>
                    </div>

                    @auth
                        <div class="card-footer border-secondary bg-transparent">
                            <form action="{{ route('pagar') }}" method="POST">
                                @csrf
                                <input type="submit" value="Finalizar Compra"
                                    class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">
                            </form>
                        </div>
                    @endauth
                    @guest
                        <p>Para finalizar a compra você precisa se cadastrar, cadastre-se agora é muito pratico.</p>
                        <div class="card-footer border-secondary bg-transparent">
                            <form action="{{ route('cadastrar') }}" method="GET">
                                @csrf
                                <input type="submit" value="Cadastrar"
                                    class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">
                            </form>
                        </div>
                    @endguest
                @else
                    <p>Nenhum item no carrinho</p>
            @endif
        </div>
    </div>
    </div>
    <!-- Checkout End -->

    {{-- <h3>Carrinho</h3>

    @if (isset($cart) && count($cart) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Foto</th>
                    <th>Valor</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($cart as $indice => $p)
                    <tr>
                        <td>{{ $p->nome }}</td>
                        <td><img src="{{ asset($p->foto) }}" alt="" height="50"></td>
                        <td>{{ $p->valor }}</td>
                        <td>{{ $p->descricao }}</td>
                        <td>
                            <a href="{{ route('excluirCarrinho', ['indice' => $indice]) }}" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @php $total += $p->valor; @endphp
                @endforeach
            </tbody>
            <tFoot>
                <tr>
                    <td colspan="5">Total do carrinho: R$ {{ $total }}</td>
                </tr>
            </tFoot>
        </table>

        <form action="{{ route('pagar') }}" method="POST">
            @csrf
            <input type="submit" value="Finalizar Compra" class="btn btn-lg btn-success">
        </form>
    @else
        <p>Nenhum item no carrinho</p>
    @endif --}}
@endsection
