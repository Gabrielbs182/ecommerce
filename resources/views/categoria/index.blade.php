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
                            <h2>Minhas<b> Categorias</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('categoria.cadastrar')}}" class="float-right mt-4">+ Novo</a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Categoria</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->categoria }}</td>
                                <td>
                                    <a href="{{ route('categoria.editar', ['id' => $p->id]) }}">Editar</a>
                                </td>
                                <td>
                                    <a href="{{ route('categoria.excluir', ['id' => $p->id]) }}">Excluir</a>
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
                            <h2>Minhas<b> Categorias</b></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('categoria.cadastrar')}}" class="float-right mt-4">+ Novo</a>
                </div>
                <h2 class="text-center">Nenhuma categoria cadastrada.
                    </h1>
            </div>
        @endif
    </div>

@endsection
