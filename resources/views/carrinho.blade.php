@extends('layouts.app')

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
                        <div class="row">
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Logradouro</label>
                                <input class="form-control" type="text" id="logradouro" name="logradouro" readonly>
                            </div>
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Bairro</label>
                                <input class="form-control" type="text" id="bairro" name="bairro" readonly>
                            </div>
                            <div class="col-md-6 form-group" id="numero-group">
                                <label>Numero</label>
                                <input class="form-control" type="text" id="numero" name="numero" required>
                            </div>
                            <div class="col-md-6 form-group" id="cidade-group">
                                <label>Cidade</label>
                                <input class="form-control" type="text" id="cidade" name="cidade" readonly>
                            </div>
                            <div class="col-md-6 form-group" id="uf-group">
                                <label>Uf</label>
                                <input class="form-control" type="text" id="uf" name="uf" readonly>
                            </div>
                            <div class="col-md-6 form-group" id="cep-group">
                                <label>CEP</label>
                                <input class="form-control" type="text" id="cep" onblur="calcularFrete()"
                                    maxlength="10" oninput="validarNumero(this)">
                            </div>
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
                                    <p>{{ $p->valor * $p->quantidade }}</p>
                                </div>
                                @php $total += ($p->valor*$p->quantidade); @endphp
                            @endforeach
                            <hr class="mt-0">
                            <div class="d-flex justify-content-between mb-3 pt-1">
                                <h6 class="font-weight-medium">Subtotal</h6>
                                <h6 class="font-weight-medium">R$ {{ $total }}</h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-medium">Frete</h6>
                                <h6 class="font-weight-medium">R$ {{ $valor }}</h6>
                            </div>
                        </div>
                        <div class="card-footer border-secondary bg-transparent">
                            <div class="d-flex justify-content-between mt-2">
                                <h5 class="font-weight-bold">Total</h5>
                                <h5 class="font-weight-bold">R$ {{ $total + $valor }}</h5>
                            </div>
                        </div>
                    </div>

                    @guest
                        <div class="row">
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Nome</label>
                                <input class="form-control" type="text" id="nome" name="nome" required>
                            </div>
                            <div class="col-md-6 form-group" id="logradouro-group">
                                <label>Sobrenome</label>
                                <input class="form-control" type="text" id="Sobrenome" name="Sobrenome" required>
                            </div>
                            <div class="col-md-6 form-group" id="numero-group">
                                <label>Cpf</label>
                                <input class="form-control" type="cpf" id="cpf" name="cpf" required>
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
                    @endguest

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
            $('#cep').on('input', function() {
                // Remove todos os caracteres não numéricos
                var cep = $(this).val().replace(/\D/g, '');

                // Aplica a máscara "99999-999"
                if (cep.length >= 5) {
                    cep = cep.substring(0, 5) + '-' + cep.substring(5, 8);
                }

                // Limita o tamanho a 9 caracteres
                cep = cep.substring(0, 9);

                // Atualiza o valor do campo
                $(this).val(cep);
            });
        });

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

        function obterDadosCEP(cep) {
            // Montar a URL com o CEP fornecido
            var url = `https://viacep.com.br/ws/${cep}/json`;

            // Fazer a requisição usando a API fetch
            fetch(url)
                .then(response => {
                    // Verificar se a resposta da requisição está OK (código 200)
                    if (!response.ok) {
                        throw new Error(`Erro na requisição: ${response.status}`);
                    }
                    // Parsear a resposta JSON
                    return response.json();
                })
                .then(data => {
                    // Manipular os dados obtidos
                    console.log(data);
                    document.getElementById("logradouro").value = data.logradouro;
                    document.getElementById("bairro").value = data.bairro;
                    document.getElementById("cidade").value = data.localidade;
                    document.getElementById("uf").value = data.uf;
                    document.getElementById("cep").value = cep;

                    // Aqui você pode fazer o que quiser com os dados, por exemplo, preencher um formulário
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        };

        @if (isset($cep))
            window.onload = obterDadosCEP({{ $cep }});
        @endif

        function calcularFrete() {
            $("#loader-div").show();
            // Obtenha o valor do CEP
            var cepValue = document.getElementById("cep").value;

            obterDadosCEP(cepValue);

            // Verifique se o CEP é válido antes de fazer a requisição
            if (validarCEP(cepValue)) {
                // Faça a requisição para calcular o frete
                $.getJSON('/melhorenvio/calcularFrete/' + cepValue, function(frete) {
                    console.log(frete);

                    for (var transportadora of frete) {
                        if ('error' in transportadora) {
                            console.log("Tipo de envio " + transportadora.name + " Contem um erro:");
                            console.log("Transportadora = " + transportadora.company.name + "Contem um erro:");
                            console.log(transportadora.error);
                        } else {
                            console.log('ID = ' + transportadora.company.id);
                            console.log('Transportadora = ' + transportadora.company.name);
                            console.log('Logo = ' + transportadora.company.picture);
                            console.log('Tipo envio = ' + transportadora.name);
                            console.log('Preço = ' + transportadora.price);
                        }
                        console.log(transportadora);
                        console.log(transportadora.error);

                    }

                    // Verifique se o retorno indica um CEP inválido
                    if (frete && frete.error) {
                        // Exiba a mensagem de erro
                        $("#cep-error-message").text("CEP inválido");

                        // Adicione uma classe para destacar o campo em vermelho
                        $("#cep-group").addClass("has-error");
                    } else {
                        // Se o CEP for válido, limpe a mensagem de erro e remova a classe de estilo
                        $("#cep-error-message").text("");
                        $("#cep-group").removeClass("has-error");
                    }
                });
            } else {
                if (document.getElementById("cep").value != '') {
                    // Exibir mensagem de erro para CEP inválido
                    $("#cep-error-message").text("CEP inválido");

                    // Adicionar classe para destacar o campo em vermelho
                    $("#cep-group").addClass("has-error");
                }
            }
        };

        function validarNumero(input) {
            // Remover caracteres não numéricos
            input.value = input.value.replace(/\D/g, '');

            // Limitar o comprimento conforme o atributo maxlength
            if (input.value.length > parseInt(input.getAttribute('maxlength'))) {
                input.value = input.value.slice(0, input.getAttribute('maxlength'));
            }
        }

        function validarCEP(cep) {
            // Remover caracteres não numéricos
            var cepNumerico = cep.replace(/\D/g, '');

            // Verificar se o CEP tem exatamente 8 dígitos
            if (cepNumerico.length === 8) {
                return true;
            } else {
                return false;
            }
        };
    </script>
@endsection
