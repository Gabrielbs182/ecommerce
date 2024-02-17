<!-- resources/views/emails/acompanhamento-pedido.blade.php -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">


<div class="container-fluid">
    <div class="jumbotron">
        <h1 class="display-4">Olá, {{ $data['nome'] }}!</h1>
        <p class="lead">Seu pedido código: {{ $data['id'] }}, teve uma atualização, confira.</p>
        <table class="table table-borderless table-responsive">
            <thead>
                <tr class="table-active">
                    <th scope="col">Código do pedido</th>
                    <th scope="col">Data do pedido</th>
                    <th scope="col">Status do pedido</th>
                    <th scope="col">Código de rastreamento</th>
                    <th scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data['id'] }}</td>
                    <td>{{ $data['data'] }}</td>
                    <td>{{ $data['status'] }}</td>
                    <td>{{ $data['cod_rastreamento'] }}</td>
                    <td>R$ {{ number_format($data['total'], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @if ($data['cod_rastreamento'] != '')
            @if ($data['cod_rastreamento'] == 'Motoboy')
                <p class="lead">
                    Pedido enviado via motoboy
                </p>
            @else
                <p class="lead">
                    <a class="btn btn-primary btn-lg float-right"
                        href="https://app.melhorrastreio.com.br/app/melhorenvio/{{ $data['cod_rastreamento'] }}"
                        role="button">Acompanhar Pedido</a>
                </p>
            @endif
            <p>Para acompanhar o seu pedido pelo nosso sistema, faça já o seu cadastro. <a
                    href="https://urbeatstore.com.br/cadastrar">cadastrar.</a></p>
        @endif
        <p>Caso tenha algum problema com o pedido, favor ler a nossa <a href="{{ route('politica') }}">política.</a> de
            troca e devolução, e entrar em contato através dos contatos disponiveis na nossa pagina de <a
                href="{{ route('politica') }}">política.</a> de devolução e troca. </p>
    </div>
</div>
