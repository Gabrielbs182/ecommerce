@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
@endsection

@section('content')
    <input type="hidden" value="{{ asset('images') }}" id="asset">

    <div class="container">
        @if (count($lista) > 0)
            <div class="table-wrapper mt-5 mb-5 table-responsive">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Meus<b> Produtos</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('produto.cadastrar')}}" class="float-right mt-4">+ Novo</a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Valor</th>
                            <th>Peso</th>
                            <th>Altura</th>
                            <th>Largura</th>
                            <th>Comprimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->nome }}</td>
                                <td>{{ $p->nome_categoria }}</td>
                                <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                                <td>{{ $p->peso }}</td>
                                <td>{{ $p->altura }}</td>
                                <td>{{ $p->largura }}</td>
                                <td>{{ $p->comprimento }}</td>
                                <td>
                                    <a href="{{ route('estoque.index', ['id' => $p->id]) }}">Estoque</a>
                                </td>
                                <td>
                                    <a href="{{ route('produto.editar', ['id' => $p->id]) }}">Editar</a>
                                </td>
                                <td>
                                    <a href="{{ route('produto.excluir', ['id' => $p->id]) }}">Excluir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="table-wrapper table-responsive" >
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Meus<b> Produtos</b></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('produto.cadastrar')}}" class="float-right mt-4">+ Novo</a>
                </div>
                <h2 class="text-center">Nenhum Produto cadastrado.
                    </h1>
            </div>
        @endif
    </div>

@endsection
