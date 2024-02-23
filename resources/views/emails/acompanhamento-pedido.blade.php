<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
        }

        p {
            color: #555555;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4caf50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Olá, {{ $data['nome'] }}!</h1>
        <p>Seu pedido código: {{ $data['id'] }}, teve uma atualização, confira.</p>
        <table>
            <thead>
                <tr>
                    <th>Código do pedido</th>
                    <th>Data do pedido</th>
                    <th>Status do pedido</th>
                    <th>Código de rastreamento</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data['id'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($data['data'])->format('d/m/y H:i:s') }}</td>
                    <td>{{ $data['status'] }}</td>
                    <td>{{ $data['cod_rastreamento'] }}</td>
                    <td>R$ {{ number_format($data['total'], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @if ($data['cod_rastreamento'] != '')
            @if ($data['cod_rastreamento'] == 'Motoboy enviado')
                <p>Pedido enviado via motoboy</p>
            @else
                <p>
                    <a class="btn" href="https://app.melhorrastreio.com.br/app/melhorenvio/{{ $data['cod_rastreamento'] }}">
                        Acompanhar Pedido
                    </a>
                </p>
            @endif
            <p>Para acompanhar o seu pedido pelo nosso sistema, faça já o seu cadastro. <a href="https://urbeatstore.com.br/cadastrar">Cadastrar</a>.</p>
        @endif
        <p>Caso tenha algum problema com o pedido, favor ler a nossa <a href="{{ route('politica') }}">política</a> de troca e devolução, e entrar em contato através dos contatos disponíveis na nossa página de <a href="{{ route('politica') }}">política</a> de devolução e troca.</p>
    </div>

</body>

</html>