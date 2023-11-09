@extends('layouts.app')

@section('content')

    <h2>Categoria</h2>

    @if (isset($listaCategoria) && count($listaCategoria) > 0)
        <ul>
            @foreach ($listaCategoria as $cat)
                <li><a href="{{route('categoria_por_id', ['idcategoria' => $cat->id])}}">{{ $cat->categoria }}</a></li>
            @endforeach
        </ul>
    @endif

    <div class="row">
        @include('layouts._produtos', ['lista' => $lista])
    </div>

@endsection
