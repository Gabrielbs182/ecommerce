@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border shadow-lg p-3 mb-5 bg-white rounded mt-5">
                    <div class="card-body">
                        <h2 class="mt-2 mb-4">Editar<b> Categoria</b></h2>
                        <form action="{{ route('categoria.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{$categoria->id}}" name="id">
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-12">
                                    <label for="inputEmail4">Nome</label>
                                    <input type="text" name="categoria" class="form-control" id="categoria" value="{{$categoria->categoria}}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Editar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
