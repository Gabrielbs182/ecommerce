@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
    <script></script>
@endsection

@section('content')

    <div class="container">
        @if (count($lista) > 0)
            <div class="table-wrapper mt-5 mb-5">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Meus<b> Pedidos</b></h2>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Data do pedido</th>
                            <th>Status</th>
                            <th>Código rastreamento</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ date('d/m/Y H:i', strtotime($p->datapedido)) }}</td>
                                <td>{{ $p->status }}</td>
                                <td>{{ $p->cod_rastreamento }}</td>
                                <td>R$ {{ number_format($p->total, 2, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('compra_detalhes', $p->id) }}">Detalhes</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Meus<b> Pedidos</b></h2>
                        </div>
                    </div>
                </div>
                <h2 class="text-center">Nenhum pedido realizado em seu nome, aproveite e de um presente para quem ama :)
                    </h1>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalcompra">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalcompraLabel">Detalhes da Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="conteudopedido">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
