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
                        <h2 class="mt-2 mb-4">Cadastrar<b> Produto</b></h2>
                        <form action="{{ route('produto.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Nome</label>
                                    <input type="text" name="nome" class="form-control" id="nome">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="categoria">Categoria</label>
                                    <select id="categoria" class="form-control" name="categoria">
                                        @foreach ($lista as $c)
                                            <option value="{{ $c->id }}">{{ $c->categoria }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="valor">Valor de venda</label>
                                    <input type="text" name="valor" class="form-control" id="valor"
                                        placeholder="Exemplo: 20,00">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="peso">peso</label>
                                    <input type="number" class="form-control" id="peso" name="peso" minlength="100"
                                        maxlength="1000" placeholder="Peso em gramas">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="altura">Altura</label>
                                    <input type="number" class="form-control" id="altura" name="altura" minlength="1"
                                        maxlength="100" placeholder="Altura em centimetros">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-3">
                                    <label for="largura">Largura</label>
                                    <input type="number" class="form-control" id="largura" name="largura" minlength="1"
                                        maxlength="100" placeholder="Largura em centimetros">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="comprimento">Comprimento</label>
                                    <input type="number" class="form-control" id="comprimento" name="comprimento"
                                        minlength="1" maxlength="100" placeholder="Comprimento em centimetros">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="foto1">Foto principal</label>
                                    <input type="file" class="form-control" id="foto1" name="foto1"
                                        placeholder="Tamanho sugerido 500x700">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="foto2">Foto secundaria 1</label>
                                    <input type="file" class="form-control" id="foto2" name="foto2"
                                        placeholder="Tamanho sugerido 500x700">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="foto3">Foto secundaria 2</label>
                                    <input type="file" class="form-control" id="foto3" name="foto3"
                                        placeholder="Tamanho sugerido 500x700">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="foto2">Foto secundaria 3</label>
                                    <input type="file" class="form-control" id="foto4" name="foto4"
                                        placeholder="Tamanho sugerido 500x700">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="foto3">Foto secundaria 4</label>
                                    <input type="file" class="form-control" id="foto5" name="foto5"
                                        placeholder="Tamanho sugerido 500x700">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-12">
                                    <label for="descricao">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
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
