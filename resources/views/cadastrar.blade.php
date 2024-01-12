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
    <div class="col-12">
        <div class="col-12 text-center">
            <h2>Cadastrar</h2>
        </div>
        <div class="row">
            <div class="col-2">

            </div>
            <div class="col-8">
                <form action="{{ route('cadastrar_cliente') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                Nome: <input type="text" name="nome" class="form-control" minlength="3"
                                    maxlength="40">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                Sobrenome: <input type="text" name="sobrenome" class="form-control" minlength="5"
                                    maxlength="40">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                Email: <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                Cpf: <input type="cpf" id="CPF" name="cpf" class="form-control">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                DDD: <input type="number" name="ddd" class="form-control" minlength="2"
                                    maxlength="2">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                Telefone: <input type="number" name="telefone" class="form-control" minlength="9"
                                    maxlength="9">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                Senha: <input type="password" name="password" class="form-control" minlength="6"
                                    maxlength="12">
                            </div>
                        </div>
                        {{-- <div class="col-8">
                            <div class="form-group">
                                Endereço: <input type="text" name="endereco" class="form-control" maxlength="80">
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                Numero: <input type="text" name="numero" class="form-control" id="numeroCasa"
                                    maxlength="8">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                Complemento: <input type="text" name="complemento" class="form-control" maxlength="40">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                UF:
                                <select class="form-control" aria-label="Default select example" id="estado">
                                    <option selected></option>
                                    @foreach ($estados as $e)
                                        <option value="{{ $e->id }}" name="estado">{{ $e->sigla }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                Cidade:
                                <select class="form-control" id="listaCidade" aria-label="Default select example" name="cidade_id" disabled>
                                    <option selected>Cidade</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                CEP: <input type="text" id="cep" name="cep" class="form-control">
                            </div>
                        </div> --}}
                        <input type="submit" value="cadastrar"
                            class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">
                </form>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
@endsection
