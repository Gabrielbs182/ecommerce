@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
    <script>
        function abrirCarrinho(id) {
            carregarItens(id);
            $('#modalDetalhes').modal('show');
        };

        function carregarItens(id) {

            $.getJSON('/itens/' + id, function(data) {

                console.log(id);

                // O objeto retornado é um array
                var total = 0;
                var indice = 0;
                $('#modalCarrinhoLista').empty();

                // Verificar se data é um objeto
                if (typeof data === 'object' && data !== null) {
                    // Iterar sobre as propriedades do objeto
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            var item = data[key];
                            valor = item.valor;
                            let valorItem = item.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                            let valorTotal = (item.quantidade*item.valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                            var asset = $('#asset').val();
                            var itemCarrinho =
                                '<tr>' +
                                '<td class="w-20 align-middle text-center">' +
                                '<img src="' + asset +
                                '/' + item.id +
                                '.png")" class="img-fluid img-thumbnail" width="100" height="100" ' +
                                'alt="Produto" >' + item.nome_produto + '/' + item.numero + ' x ' + item.quantidade +
                                '</td>' +
                                '<td class="align-middle text-center"> ' + valorItem + '</td>' +
                                '<td class="align-middle text-center"> ' + valorTotal +
                                '</td>' +
                                '<td class="align-middle text-center">' +
                                '</td>' +
                                '</tr>';
                            $('#modalCarrinhoLista').append(itemCarrinho);
                            indice++
                        }
                    }
                } else {
                    console.error('O JSON retornado não é um array:', data);
                }
            });
        };
    </script>
@endsection

@section('content')
    <input type="hidden" value="{{ asset('images') }}" id="asset">

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
                                    <a href="#" class="edit"
                                        onclick="abrirCarrinho({{ $p->id }})">Produtos</a>
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

    <div class="modal fade" id="modalDetalhes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable justify-content-center"
            role="document">

            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Produtos
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="carrinhoModal">
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">Produto</th>
                                        <th scope="col" class="text-center">Preço</th>
                                        <th scope="col" class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="modalCarrinhoLista">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
