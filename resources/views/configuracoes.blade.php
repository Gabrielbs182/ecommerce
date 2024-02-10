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
                        <h2 class="mt-2 mb-4">Configurações do<b> Sistema</b></h2>
                        <form action="{{ route('configuracoes.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <p><b>Sistema</b></p>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="banner1">Foto Banner 1</label>
                                    <input type="file" name="banner1"
                                        class="form-control" id="banner1">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="banner2">Foto Banner 2</label>
                                    <input type="file" name="banner2"
                                        class="form-control" id="banner2">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="banner3">Foto Banner 3</label>
                                    <input type="file" name="banner3"
                                        class="form-control" id="banner3">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" name="freteFixo" type="checkbox" id="gridCheck" @if($freteFixo == 1) checked @endif>
                                    <label class="form-check-label" for="gridCheck">
                                        Frete Fixo
                                    </label>
                                </div>
                            </div>
                            <p><b>Pag Seguro</b></p>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Email</label>
                                    <input value="{{ $pagEmail }}" type="email" name="pagSeguroEmail"
                                        class="form-control" id="pagSeguroEmail" placeholder="EX: email@email.com">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Token</label>
                                    <input value="{{ $pagToken }}" type="text" name="pagSeguroToken"
                                        class="form-control" id="pagSeguroToken">
                                </div>
                            </div>
                            <p><b>Melhor Envio</b></p>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="melhorEnvioNome">Nome</label>
                                    <input value="{{ $melhorName }}" type="text" name="melhorEnvioNome"
                                        class="form-control" id="melhorEnvioNome" placeholder="Nome do aplicativo">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="melhorEnvioTelefone">Telefone</label>
                                    <input value="{{ $melhorPhone }}" type="text" class="form-control"
                                        id="melhorEnvioTelefone" name="melhorEnvioTelefone" placeholder="EX: 27999999999">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="melhorEnvioEmail">Email</label>
                                    <input value="{{ $melhorEmail }}" type="email" class="form-control"
                                        id="melhorEnvioEmail" name="melhorEnvioEmail" placeholder="EX: email@email.com">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="melhorEnvioDocumento">Documento</label>
                                    <input value="{{ $melhorDocument }}" type="text" class="form-control"
                                        id="melhorEnvioDocumento" name="melhorEnvioDocumento"
                                        placeholder="CPF ou CNPJ, somente numeros.">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="melhorEnvioClientSecret">Client Secret</label>
                                    <input value="{{ $melhorClientSecret }}" type="text" class="form-control"
                                        id="melhorEnvioClientSecret" name="melhorEnvioClientSecret">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="melhorEnvioUserAgent">Contato do usuário chave</label>
                                    <input value="{{ $melhorUserAgent }}" type="text" class="form-control"
                                        id="melhorEnvioUserAgent" name="melhorEnvioUserAgent"
                                        placeholder="EX: NomeAplicativo (usuariochave@email.com)">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="melhorEnvioClientID">Client ID</label>
                                    <input value="{{ $melhorClientID }}" type="text" class="form-control"
                                        id="melhorEnvioClientID" name="melhorEnvioClientID">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="melhorEnvioLog">Logradouro</label>
                                    <input value="{{ $melhorLog }}" type="text" class="form-control"
                                        id="melhorEnvioLog" name="melhorEnvioLog">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="melhorEnvioComp">UF</label>
                                    <input value="{{ $melhorUF }}" type="text" class="form-control"
                                        id="melhorEnvioUF" name="melhorEnvioUF">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="melhorEnvioComp">Complemento</label>
                                    <input value="{{ $melhorComple }}" type="text" class="form-control"
                                        id="melhorEnvioComp" name="melhorEnvioComp">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="melhorEnvioNumber">Numero</label>
                                    <input value="{{ $melhorNumber }}" type="text" class="form-control"
                                        id="melhorEnvioNumber" name="melhorEnvioNumber">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="melhorDistrict">Bairro</label>
                                    <input value="{{ $melhorDistrict }}" type="text" class="form-control"
                                        id="melhorDistrict" name="melhorDistrict">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="melhorCity">Cidade</label>
                                    <input value="{{ $melhorCity }}" type="text" class="form-control"
                                        id="melhorCity" name="melhorCity">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="melhorCep">Cep</label>
                                    <input value="{{ $melhorCep }}" type="text" class="form-control"
                                        id="melhorCep" name="melhorCep">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-12">
                                    <label for="melhorEnvioEscopo">Escopo dos acessos do site</label>
                                    <textarea class="form-control" id="melhorEnvioEscopo" name="melhorEnvioEscopo" rows="3"
                                        placeholder="EX: cart-read cart-write companies-read">{{ $melhorScope }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Salvar</button>
                            <a href="{{ route('melhorenvio.authorize') }}" class="btn btn-primary float-left">Melhor
                                Envio - Autorização</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
