@extends('layouts.app')

@section('scriptjs')
<script src="{{ secure_asset('js/jquery.js') }}"></script>
<script>
    function abrirCarrinho(id) {
        carregarItens(id);
        $('#modalDetalhes').modal('show');
    };

    function abrirEndereco(id) {
        carregarEndereco(id);
        $('#modalDetalhesEndereco').modal('show');
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
                        let valorItem = (item.valor * 1).toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });
                        let valorTotal = (item.quantidade * item.valor).toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });
                        var asset = $('#asset').val();
                        var itemCarrinho =
                            '<tr>' +
                            '<td class="w-20 align-middle text-center">' +
                            '<img src="' + asset +
                            '/' + item.produto_id +
                            '.png")" class="img-fluid img-thumbnail" width="100" height="100" ' +
                            'alt="Produto" >' + item.nome_produto + '/' + item.numero + ' x ' + item
                            .quantidade +
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

    function carregarEndereco(id) {

        $.getJSON('/endereco/' + id, function(data) {

            console.log(id);
            console.log(data);

            // O objeto retornado é um array
            var total = 0;
            var indice = 0;
            $('#modalEnderecoLista').empty();

            // Verificar se data é um objeto
            if (typeof data === 'object' && data !== null) {
                // Iterar sobre as propriedades do objeto
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        var item = data[key];

                        var asset = $('#asset').val();
                        var itemCarrinho =
                            '<tr>' +
                            '<td class="align-middle text-center"> ' + item.logradouro + '-' + item.numero + '</td>' +
                            '<td class="align-middle text-center"> ' + item.bairro + '</td>' +
                            '<td class="align-middle text-center"> ' + item.complemento + '</td>' +
                            '<td class="align-middle text-center"> ' + item.cep + '</td>' +
                            '<td class="align-middle text-center"> ' + item.cidade + '</td>' +
                            '<td class="align-middle text-center"> ' + item.uf + '</td>' +
                            '</tr>';
                        $('#modalEnderecoLista').append(itemCarrinho);
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
    @csrf
    @if (count($lista) > 0)
    <div class="table-wrapper mt-5 mb-5 table-responsive">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-6">
                    <h2><b>Pedidos</b></h2>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Data do pedido</th>
                    <th>Status</th>
                    <th>Status Envio</th>
                    <th>Código rastreamento</th>
                    <th>Código melhor envio</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->nome }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->telefone }}</td>
                    <td>{{ date('d/m/Y H:i', strtotime($p->datapedido)) }}</td>
                    <td>{{ $p->status }}</td>
                    <td>{{ $p->status_env }}</td>
                    <td>{{ $p->cod_rastreamento }}</td>
                    <td>{{ $p->cod_entrega }}</td>
                    <td>R$ {{ number_format($p->total, 2, ',', '.') }}</td>
                    <td>
                        <a href="#" class="edit" onclick="abrirCarrinho({{ $p->id }})">Produtos</a>
                    </td>
                    <td>
                        <a href="#" class="edit" onclick="abrirEndereco({{ $p->endereco_id }})">Endereco</a>
                    </td>
                    @if (Route::is('pedidos.pendentes'))
                    <td>
                        <a href="{{ route('pedido.finalizar', $p->id) }}" class="edit">Finalizar</a>
                    </td>
                    @endif
                    <td>
                    @if (Route::is('pedidos.todos') && ($p->status <> "Pagamento aprovado"))
                    
                    <form method="POST" action="{{ route('pedido.excluir', $p->id) }}">
                        @csrf
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger float-right mt-3">Excluir</button>
                    </form>
                
                    @endif
                </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div>
        <h2 class="text-center">Nenhum pedido pendente.
        </h2>
    </div>
    @endif

</div>

<div class="modal fade" id="modalDetalhes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable justify-content-center" role="document">

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

<div class="modal fade" id="modalDetalhesEndereco" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable justify-content-center" role="document">

        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="exampleModalLabel">
                    Endereço
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="enderecoModal">
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">Logradouro</th>
                                    <th scope="col" class="text-center">Bairro</th>
                                    <th scope="col" class="text-center">Complemento</th>
                                    <th scope="col" class="text-center">Cep</th>
                                    <th scope="col" class="text-center">Cidade</th>
                                    <th scope="col" class="text-center">UF</th>
                                </tr>
                            </thead>
                            <tbody id="modalEnderecoLista">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection