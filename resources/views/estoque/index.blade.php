@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
    <script>
        function atualizarEstoque() {
            var formData = [];
            $('tbody tr').each(function() {
                var id = $(this).find('input[name="id"]').val();
                var quantidade = $(this).find('input[name="quantidade"]').val();
                formData.push({
                    id: id,
                    quantidade: quantidade
                });
            });

            // Adicione os dados ao formulário como um campo JSON
            $('#formAtualizarEstoque').append('<input type="hidden" name="dados_estoque" value=\'' + JSON.stringify(
                formData) + '\'>');

            // Envie o formulário
            $('#formAtualizarEstoque').submit();
        }
    </script>
@endsection

@section('content')
    <input type="hidden" value="{{ asset('images') }}" id="asset">

    <div class="container">
        <form id="formAtualizarEstoque" action="{{ route('atualizar_estoque') }}" method="POST">
            @csrf
            @if (count($lista) > 0)
                <div class="table-wrapper mt-5 mb-5 table-responsive">
                    <div class="table-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h2><b>Estoque</b></h2>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Quantidade</th>
                                <th>Numero</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lista as $p)
                                <tr>
                                    <input type="hidden" value="{{ $p->id }}" name="id">
                                    <td>{{ $p->id }}</td>
                                    <td>{{ $p->nome_produto }}</td>
                                    <td> <input type="number" value="{{ $p->quantidade }}" name="quantidade"></td>
                                    <td>{{ $p->numero }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="table-wrapper table-responsive">
                    <div class="table-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h2><b>Estoque</b></h2>
                            </div>
                        </div>
                    </div>
                    <h2 class="text-center">Produto Sem Estoque.
                        </h1>
                </div>
            @endif
            <button type="button" onclick="atualizarEstoque()" class="btn btn-primary">Atualizar Estoque</button>
        </form>

    </div>

@endsection
