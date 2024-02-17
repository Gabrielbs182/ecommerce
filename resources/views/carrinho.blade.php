@extends('layouts.app')

@section('scriptjs')
    <script src="{{ asset('lib/urbeatFunctions/urbeat.js') }}"></script>
@endsection


@section('content')
    <!-- Checkout Start -->
    <!-- Adicione uma div para mostrar a mensagem de erro -->
    <div id="cep-error-message" class="text-danger"></div>

    <div class="container-fluid pt-5">
        <div class="row px-xl-5">
            <div class="col-lg-8">
                <div class="mb-4">
                    <h4 class="font-weight-semi-bold mb-4">Endereço de entrega</h4>
                    <form action="{{ route('pagar') }}" method="POST">
                        @csrf
                        <input type="hidden" id="valorFrete" name="valorFrete" value="{{ $valor }}">
                        <input type="hidden" id="idFrete" name="idFrete" value="{{ $idFrete }}">
                        <input type="hidden" id="cupom" name="cupom">
                        @if ($idFrete == 'Gratis')
                            <div class="row">
                                <div class="col-md-6 form-group" id="logradouro-group">
                                    <label>Logradouro</label>
                                    <input class="form-control" value="Rua Santa alguma coisa" type="text"
                                        id="logradouro" name="logradouro" readonly>
                                </div>
                                <div class="col-md-6 form-group" value="Carapina" id="logradouro-group">
                                    <label>Bairro</label>
                                    <input class="form-control" value="Carapembas" type="text" id="bairro"
                                        name="bairro" readonly>
                                </div>
                                <div class="col-md-6 form-group" id="numero-group">
                                    <label>Numero</label>
                                    <input class="form-control" value="0" type="text" id="numero" name="numero"
                                        readonly>
                                </div>
                                <div class="col-md-6 form-group" id="cidade-group">
                                    <label>Cidade</label>
                                    <input class="form-control" value="Serra" type="text" id="cidade" name="cidade"
                                        readonly>
                                </div>
                                <div class="col-md-2 form-group" id="uf-group">
                                    <label>Uf</label>
                                    <input class="form-control" value="ES" type="text" id="uf" name="uf"
                                        readonly>
                                </div>
                                <div class="col-md-4 form-group" id="cep-group">
                                    <label>CEP</label>
                                    <input class="form-control" value="29160887" name="cep" type="text"
                                        id="cep" onblur="calcularFrete()" maxlength="10" oninput="validarNumero(this)"
                                        readonly>
                                </div>
                                <div class="col-md-4 form-group" id="complemento-group">
                                    <label>Complemento</label>
                                    <input class="form-control" value="" name="complemento" type="text"
                                        id="complemento">
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-6 form-group" id="logradouro-group">
                                    <label>Logradouro</label>
                                    @if ($logradouro != '')
                                        <input value="{{ $logradouro }}" class="form-control" type="text"
                                            id="logradouro" name="logradouro" readonly>
                                    @else
                                        <input value="" class="form-control" type="text" id="logradouro"
                                            name="logradouro" required>
                                    @endif
                                </div>
                                <div class="col-md-6 form-group" id="logradouro-group">
                                    <label>Bairro</label>
                                    @if ($bairro != '')
                                        <input value="{{ $bairro }}" class="form-control" type="text"
                                            id="bairro" name="bairro" readonly>
                                    @else
                                        <input value="" class="form-control" type="text" id="bairro"
                                            name="bairro" required>
                                    @endif
                                </div>
                                <div class="col-md-6 form-group" id="numero-group">
                                    <label>Numero</label>
                                    <input class="form-control" type="text" id="numero" name="numero" required>
                                </div>
                                <div class="col-md-6 form-group" id="cidade-group">
                                    <label>Cidade</label>
                                    @if ($cidade != '')
                                        <input value="{{ $cidade }}" class="form-control" type="text"
                                            id="cidade" name="cidade" readonly>
                                    @else
                                        <input value="" class="form-control" type="text" id="cidade"
                                            name="cidade" required>
                                    @endif
                                </div>
                                <div class="col-md-2 form-group" id="uf-group">

                                    <label>Uf</label>
                                    @if ($uf != '')
                                        <input value="{{ $uf }}" class="form-control" type="text"
                                            id="uf" name="uf" readonly>
                                    @else
                                        <input value="" class="form-control" type="text" id="uf"
                                            name="uf" required>
                                    @endif
                                </div>
                                <div class="col-md-4 form-group" id="cep-group">
                                    <label>CEP</label>
                                    <input value="{{ $cep }}" class="form-control" name="cep"
                                        type="text" id="cep" maxlength="10" readonly>
                                </div>

                                <div class="col-md-4 form-group" id="complemento-group">
                                    <label>Complemento</label>
                                    <input class="form-control" value="" name="complemento" type="text"
                                        id="complemento">
                                </div>
                            </div>
                        @endif

                        <div class="card-footer border-secondary bg-transparent">
                            <a class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3"
                                href="{{ route('home') }}" role="button">Cancelar</a>

                        </div>
                </div>
            </div>

            @if (isset($cart) && count($cart) > 0)
                <div class="col-lg-4">
                    <div class="card border-secondary mb-5">
                        <div class="card-header bg-secondary border-0">
                            <h4 class="font-weight-semi-bold m-0">Total do pedido</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="font-weight-medium mb-3">Produtos</h5>
                            @php $total = 0; @endphp
                            @foreach ($cart as $indice => $p)
                                <div class="d-flex justify-content-between">
                                    <p>{{ $p->nome }} x {{ $p->quantidade }}</p>
                                    <p>{{ number_format(($p->valor - $p->valor * ($p->desconto / 100)) * $p->quantidade, 2, ',', '.') }}
                                    </p>
                                </div>
                                @php $total += (($p->valor-($p->valor*($p->desconto/100)))*$p->quantidade); @endphp
                            @endforeach
                            <hr class="mt-0">
                            <div class="d-flex justify-content-between mb-3 pt-1">
                                <h6 class="font-weight-medium">Subtotal</h6>
                                <h6 class="font-weight-medium" id="subtotal">R$ {{ number_format($total, 2, ',', '.') }}
                                </h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-medium">Frete</h6>
                                <h6 class="font-weight-medium" id="valorFreteTexto">
                                    @if ($valor == 0)
                                        Grátis
                                    @else
                                        R$ {{ number_format($valor, 2, ',', '.') }}
                                    @endif

                                </h6>
                            </div>
                        </div>
                        <div class="card-footer border-secondary bg-transparent">
                            <div class="d-flex justify-content-between mt-2">
                                <h5 class="font-weight-bold">Total</h5>
                                <h5 class="font-weight-bold" id="total">R$
                                    {{ number_format($total + $valor, 2, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="row border">
                        <p class="ml-2"><b>Forma de pagamento</b></p>
                        <div class="col-md-9 form-group ml-3" id="logradouro-group">
                            <input class="form-check-input" type="radio" name="formaPagamento" id="cartao"
                                value="cartao" checked>
                            <label class="form-check-label" for="cartao">
                                Cartão Crédito/Débito
                            </label>
                        </div>
                        <div class="col-md-9 form-group ml-3" id="logradouro-group">
                            <input class="form-check-input" type="radio" name="formaPagamento" id="pix"
                                value="pix">
                            <label class="form-check-label" for="pix">
                                Pix (10% off)
                            </label>
                        </div>
                        <div class="col-md-9 form-group ml-3" id="logradouro-group">
                            <input class="form-check-input" type="radio" name="formaPagamento" id="boleto"
                                value="boleto">
                            <label class="form-check-label" for="boleto">
                                Boleto
                            </label>
                        </div>
                    </div>

                    <div class="row border">
                        <hr>
                        <div class="col-md-9 form-group" id="logradouro-group">
                            <label>Cupom</label>
                            <input maxlength="15" class="form-control" type="text" id="cupomTexto"
                                name="cupomTexto">
                            <div id="mensagem"></div>
                        </div>

                        <div class="col-md-3 form-group" id="logradouro-group">
                            <button type="button" id="cupomButton"
                                class="btn btn-block btn-primary font-weight-bold my-3 py-3"
                                onclick="buscarCupom()">Aplicar</button>
                        </div>
                        <hr>
                    </div>

                    @guest
                        <div class="row">
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Nome</label>
                                <input class="form-control" minlength="3" maxlength="15" type="text" id="nome"
                                    name="nome" required>
                            </div>
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Sobrenome</label>
                                <input class="form-control" type="text" id="Sobrenome" name="Sobrenome" required>
                            </div>
                            <div class="col-md-6 form-group" id="numero-group">
                                <label>Email</label>
                                <input class="form-control" placeholder="EX: exemplo@exemplo.com.br" type="text"
                                    id="email" name="email" required>
                            </div>
                            <div class="col-md-6 form-group" id="numero-group">
                                <label>Cpf</label>
                                <input class="form-control" type="cpf" id="cpf" name="cpf" required>
                            </div>
                            <div class="col-md-2 form-group" id="cidade-group">
                                <label>ddd</label>
                                <input class="form-control" maxlength="2" minlength="2" placeholder="EX: 27"
                                    type="text" id="ddd" name="ddd" required>
                            </div>
                            <div class="col-md-4 form-group" id="cidade-group">
                                <label>Telefone</label>
                                <input class="form-control" maxlength="9" minlength="9" type="text" id="telefone"
                                    name="telefone" placeholder="EX: 999999999" required>
                            </div>
                        </div>
                    @endguest

                    @auth
                        <div class="row">
                            @php
                                $nomeCompleto = explode(' ', Auth::user()->nome);
                                if (count($nomeCompleto) == 2) {
                                    $nome = $nomeCompleto[0];
                                    $sobrenome = $nomeCompleto[1];
                                } else {
                                    $nome = $nomeCompleto[0];
                                    $sobrenome = ' ';
                                }

                            @endphp
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Nome</label>
                                <input class="form-control" value="{{ $nome }}" type="text" id="nome"
                                    name="nome" required readonly>
                            </div>
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Sobrenome</label>
                                <input class="form-control" value="{{ $sobrenome }}" type="text" id="Sobrenome"
                                    name="Sobrenome" required readonly>
                            </div>
                            <div class="col-md-6 form-group" id="numero-group">
                                <label>Email</label>
                                <input class="form-control" value="{{ Auth::user()->email }}" type="text" id="email"
                                    name="email" required readonly>
                            </div>
                            <div class="col-md-6 form-group" id="numero-group">
                                <label>Cpf</label>
                                <input class="form-control" value="{{ Auth::user()->cpf }}" type="cpf" id="cpf"
                                    name="cpf" required readonly>
                            </div>
                            <div class="col-md-2 form-group" id="cidade-group">
                                <label>ddd</label>
                                <input class="form-control" type="text" id="ddd" name="ddd" required>
                            </div>
                            <div class="col-md-4 form-group" id="cidade-group">
                                <label>Telefone</label>
                                <input class="form-control" type="text" id="telefone" name="telefone" required>
                            </div>
                        </div>
                    @endauth

                    <div class="card-footer border-secondary bg-transparent">
                        <input type="submit" value="Finalizar Compra"
                            class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">
                        </form>
                    </div>
                @else
                    <p>Nenhum item no carrinho</p>
            @endif
        </div>
    </div>
    </div>

@endsection

@section('scriptjs')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $('#cpf').on('input', function() {
                // Remove todos os caracteres não numéricos
                var cpf = $(this).val().replace(/\D/g, '');

                // Aplica a máscara de CPF
                if (cpf.length >= 11) {
                    cpf = cpf.substring(0, 11); // Limita o CPF a 11 dígitos
                    cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4'); // Aplica a máscara
                }

                // Atualiza o valor do campo
                $(this).val(cpf);
            });
        });


        function validarNumero(input) {
            // Remover caracteres não numéricos
            input.value = input.value.replace(/\D/g, '');

            // Limitar o comprimento conforme o atributo maxlength
            if (input.value.length > parseInt(input.getAttribute('maxlength'))) {
                input.value = input.value.slice(0, input.getAttribute('maxlength'));
            }
        }
    </script>
@endsection
