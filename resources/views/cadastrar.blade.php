@extends('layouts.app')

@section('scriptjs')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $('#CPF').on('input', function() {
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

        $(document).ready(function() {
            $('#numeroCasa').on('input', function() {
                // Remove todos os caracteres não numéricos e limita o tamanho a 6 caracteres
                var numeroCasa = $(this).val().replace(/\D/g, '').substring(0, 6);

                // Atualiza o valor do campo
                $(this).val(numeroCasa);
            });
        });
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

        function buscarCidades() {
            idUf = $("#estado").val()
            $.getJSON('/cidades/' + idUf, function(cidades) {

                if (Array.isArray(cidades)) {
                    cidades.forEach(function(cidade) {
                        // Aqui você pode acessar os campos do objeto 'cidade' e imprimir
                        var linha = montarLinhaCidade(cidade);
                        $('#listaCidade').append(linha)
                        var meuSelect = document.getElementById("listaCidade");
                        meuSelect.disabled = false;
                        // Adicione mais campos conforme necessário
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#estado').on('change', function(e) {
                buscarCidades();
            });
        });

        function montarLinhaCidade(cidade) {
            var linha =
                "<option value='" + cidade.id + "' name='cidade'>" + cidade.nome + "</option>";
            return linha;
        };
    </script>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border shadow-lg p-3 mb-5 bg-white rounded mt-5">
                    <div class="card-body">
                        <h2 class="mt-2 mb-4">Cadastrar<b> conta</b></h2>
                        <form action="{{ route('cadastrar_cliente') }}" method="POST">
                            @csrf
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Email</label>
                                    <input type="email" name="email" class="form-control" id="inputEmail4"
                                        placeholder="exemplo@exemplo.com">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">Senha</label>
                                    <input type="password" class="form-control" id="inputPassword4" name="password">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="nome">Nome</label>
                                    <input type="text" name="nome" class="form-control" id="nome" minlength="5"
                                        maxlength="10" placeholder="até 10 digitos, sem espaçamento">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="sobrenome">Sobrenome</label>
                                    <input type="text" class="form-control" id="sobrenome" name="sobrenome"
                                        minlength="5" maxlength="10" placeholder="até 10 digitos, sem espaçamento">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-2">
                                    <label for="ddd">DDD</label>
                                    <input type="number" name="ddd" class="form-control" id="ddd"
                                        placeholder="Somente números">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="telefone">Telefone</label>
                                    <input type="number" class="form-control" id="telefone" name="telefone"
                                        placeholder="Somente números">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="cpf">Cpf</label>
                                    <input type="text" class="form-control" id="CPF" name="cpf"
                                        placeholder="11 digitos">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck" required>
                                    <label class="form-check-label" for="gridCheck">
                                        Concordo com todos os termos legais.
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
