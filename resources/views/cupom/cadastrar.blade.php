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
                        <h2 class="mt-2 mb-4">Cadastrar<b> Cupom</b></h2>
                        <form action="{{ route('cupom.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="nome">Nome</label>
                                    <input type="text" name="nome" class="form-control" id="nome">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="codigo">Código</label>
                                    <input type="text" name="codigo" class="form-control" id="codigo">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="tipo">Tipo</label>
                                    <select id="tipo" class="form-control" name="tipo">
                                        <option value="0">Frete Grátis</option>
                                        <option value="1">Desconto</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="desconto">Desconto</label>
                                    <input type="number" name="desconto" class="form-control" id="desconto"
                                        placeholder="EX: 5%">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
