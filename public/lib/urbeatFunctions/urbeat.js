function buscarProduto(id) {
    var asset = $('#asset').val();
    $.getJSON('/produto/' + id, function (produto) {
        console.log(produto)
        var foto = '<img class="w-100 h-100" src="' + asset +
            '/' + produto[0].id + '.png" alt="Image">';
        var foto2 = '<img class="w-100 h-100" src="' + asset +
            '/' + produto[0].id + '_2.png" alt="Image">';
        $('#primeiroCarrosel').empty();
        $('#segundoCarrosel').empty();
        $('#nomeProdModal').empty();
        $('#valorProdModal').empty();
        $('#comprar').empty();
        $('#comprarButton').empty();
        $('#idProduto').empty();
        $('#idProduto').val(produto[0].id);
        $('#primeiroCarrosel').append(foto);
        $('#segundoCarrosel').append(foto2);
        $('#nomeProdModal').append(produto[0].nome);
        var val = produto[0].valor.replace('.', ',');
        var valor = 'R$ ' + val;
        $('#valorProdModal').append(valor);
        $('#exampleModalCenter').modal('show');
        $.getJSON('/estoque/' + id, function (estoque) {
            var testeTamanho = true;
            estoque.forEach(function (item) {
                if (item.quantidade > 0) {
                    if (testeTamanho) {
                        testeTamanho = false;
                        var comprar =
                            '<div class="form-row">' +
                            '<div class="form-group col-md-6">' +
                            '<label for="tamanhoModal">Tamanho</label>' +
                            '<select id="tamanhoModal" class="form-control" disabled onchange="buscarEstoque()" required>' +
                            '<option selected></option>' +
                            '</select>' +
                            '</div>' +
                            '<div class="form-group col-md-6" id="inputZipDiv">' +
                            '<label for="inputZip">Quantidade</label>' +
                            '<input type="number" class="form-control" id="inputZip" required>' +
                            '</div>' +
                            '</div>' +
                            '</form>' +
                            '<div id="comprarButton">' +
                            '<button type="button" class="btn btn-primary"' +
                            'onclick="adicionarCarrinho(' + produto[0].id +
                            ')">Comprar</button>' +
                            '</div>';
                        $('#comprar').append(comprar);
                    }
                    var tamanho = '<option value="' + item.numero + '">' + item.numero +
                        '</option>';
                    $('#tamanhoModal').append(tamanho);
                    $("#tamanhoModal").prop("disabled", false);
                }
            });
            if (testeTamanho) {
                var aviso = '<h1 class="font-weight-light text-center">Produto Esgotado</h1>';
                $('#comprar').append(aviso);
            }
        });
    });
}

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

            // Aqui você pode fazer o que quiser com os dados, por exemplo, preencher um formulário
        })
        .catch(error => {
            console.error('Erro:', error);
        });
};

function calcularFrete() {
    $("#loader-div").show();
    // Obtenha o valor do CEP
    var cepValue = document.getElementById("cep").value;

    obterDadosCEP(cepValue);

    // Verifique se o CEP é válido antes de fazer a requisição
    if (validarCEP(cepValue)) {
        console.log('Cep é valido');
        // Faça a requisição para calcular o frete
        $.getJSON('/melhorenvio/calcularFrete/' + cepValue, function (frete) {
            console.log('iniciou calculo do');

            document.getElementById("envios").innerHTML = '';

            linhas =
                '<legend class="col-form-label col-sm-2 pt-0">A domicílio</legend>' +
                '<div class="col-sm-10">';

            for (var transportadora of frete) {
                if ('error' in transportadora) {
                    console.log("Tipo de envio " + transportadora.name + " Contem um erro:");
                    console.log("Transportadora = " + transportadora.company.name + "Contem um erro:");
                    console.log(transportadora.error);
                } else {
                    linha = montarLinhaEnvio(transportadora);
                    linhas += linha;
                }
            }

            linhas +=
                '</div>' +
                '<legend class="col-form-label col-sm-2 pt-0">Nossa loja</legend>' +
                '<div class="col-sm-10">' +
                '<div class="form-check">' +
                '<input class="form-check-input" type="radio" name="frete"' +
                'id="gratis" value="' + 0 + '" checked>' +
                '<label class="form-check-label" for="gratis">' +
                'Rua santa catarina, SN, Carapicuiba, Guarapari, Es, 29160-988 - Grátis' +
                '</label>' +
                '</div>' +
                '</div>';

            console.log(linhas);
            document.getElementById("envios").innerHTML = linhas;
        });
    } else {
        // Exibir mensagem de erro para CEP inválido
        $("#cep-error-message").text("CEP inválido");
        console.log('Cep é invalido');
        // Adicionar classe para destacar o campo em vermelho
        $("#cep-group").addClass("has-error");
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

function montarLinhaEnvio(transportadora) {

    linha =
        '<div class="form-check">' +
        '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
        transportadora.id + '/' + transportadora.name + '/' + transportadora.price + '" >' +
        '<label class="form-check-label" for="' + transportadora.id + '">' +
        transportadora.company.name + ' - ' + transportadora.name + ' R$ ' + transportadora.price +
        '</label>' +
        '</div>';
    return linha;
}

function buscarEstoque() {
    var numero = $('#tamanhoModal').val()
    var idProduto = $('#idProduto').val()
    $.getJSON('/estoqueNumero/' + idProduto + '/' + numero, function (estoque) {
        $('#estoque').val(estoque[0].quantidade);

    });
};

function removerProdutoCarrinho(id) {
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'POST',
        url: '/' + id + '/excluirCarrinho',
        context: this,
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function () {
            console.log('item excluido')
            $.getJSON('/buscarCarrinho', function (data) {
                var total = 0;
                var indice = 0;
                $('#modalCarrinhoLista').empty();
                if (data.length === 0) {
                    carregarCarrinho();
                    $('#modalCarrinho').modal('hide');
                } else {
                    carregarCarrinho();
                }
            });
        },
        error: function (error) {
            console.log(error);
        }
    });

}

function carregarCarrinho() {

    $.getJSON('/buscarCarrinho', function (data) {

        // O objeto retornado é um array
        var total = 0;
        var indice = 0;
        $('#modalCarrinhoLista').empty();
        console.log('carregarCarrinho');
        console.log(data);

        // Verificar se data é um objeto
        if (typeof data === 'object' && data !== null) {
            // Iterar sobre as propriedades do objeto
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    var item = data[key];

                    var asset = $('#asset').val();
                    total += item.quantidade * item.valor;
                    var itemCarrinho =
                        '<tr>' +
                        '<td class="w-20 align-middle text-center">' +
                        '<img src="' + asset +
                        '/' + item.id +
                        '.png")" class="img-fluid img-thumbnail" width="100" height="100" ' +
                        'alt="Produto" >' + item.nome + '/' + item.numero + ' x ' + item.quantidade +
                        '</td>' +
                        '<td class="align-middle text-center"> R$' + item.valor + '</td>' +
                        '<td class="align-middle text-center"> R$' + (item.quantidade * item.valor) +
                        '</td>' +
                        '<td class="align-middle text-center">' +
                        '<a href="#" class="btn btn-danger btn-sm" onclick="removerProdutoCarrinho(' +
                        key + ')">' +
                        '<i class="fa fa-times"></i>' +
                        '</a>' +
                        '</td>' +
                        '</tr>';
                    $('#modalCarrinhoLista').append(itemCarrinho);
                    indice++
                }
            }

            $('#totalCarrinho').empty();
            $('#totalCarrinho').append(total);
        } else {
            console.error('O JSON retornado não é um array:', data);
        }
        $('#totalCarrinho').empty();
        $('#totalCarrinho').append(total);
        document.getElementById("envios").innerHTML = '';

        linha =
            '</div>' +
            '<legend class="col-form-label col-sm-2 pt-0">Nossa loja</legend>' +
            '<div class="col-sm-10">' +
            '<div class="form-check">' +
            '<input class="form-check-input" type="radio" name="frete"' +
            'id="gratis" value="' + 0 + '" checked>' +
            '<label class="form-check-label" for="gratis">' +
            'Rua santa catarina, SN, Carapicuiba, Guarapari, Es, 29160-988 - Grátis' +
            '</label>' +
            '</div>' +
            '</div>';

        document.getElementById("envios").innerHTML = linha;
    });
    $.get('/atualizar-quantidade-carrinho', function (data) {
        // Atualize a quantidade na página
        if (data && data.quantidadeCarrinho !== undefined) {
            $('#quantidade-carrinho').text(data.quantidadeCarrinho);
        }
    });
};

function abrirCarrinho() {
    $.getJSON('/buscarCarrinho', function (data) {
        var total = 0;
        var indice = 0;
        $('#modalCarrinhoLista').empty();
        if (data.length === 0) {
            $('#modalCarrinhoVazio').modal('show');
        } else {
            carregarCarrinho();
            $('#modalCarrinho').modal('show');
        }
    });
};

function buscarEstoqueCarrinho(id, callback) {
    var numero = $('#tamanhoModal').val();
    console.log(numero);
    console.log(id);
    $.getJSON('/buscarItemCarrinho/' + id + '/' + numero, function (data) {
        console.log(data);
        callback(data); // Chama o callback com os dados recebidos
    });
}

function adicionarCarrinho(id) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var estoque = $('#estoque').val();
    var numero = $('#tamanhoModal').val();

    quantidade = parseInt($('#inputZip').val());

    // Use uma função de callback para obter os dados assíncronos
    buscarEstoqueCarrinho(id, function ($item) {
        console.log($item);
        if ($item == 1) {
            console.log('Item não existe no carrinho');

        } else {
            console.log('Item existe no carrinho');
            console.log(quantidade);
            quantidade += parseInt($item.quantidade);
            console.log(quantidade);
        }
        // Restante do seu código...
        console.log(quantidade);
        console.log(estoque);
        if (parseInt(quantidade) > parseInt(estoque)) {
            console.log('Não tem estoque');
            $('#inputZip').val('');
            var alerta =
                '<div class="alert alert-danger alert-dismissible fade show text-center fixed-top" role="alert">' +
                'Estoque insuficiente, tente uma quantidade menor.' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';
            $('#modalAlert').append(alerta);
        } else {
            console.log('Tem estoque');

            $.getJSON('/produto/' + id, function (produto) {
                var quantidade = $('#inputZip').val()
                var token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'POST',
                    url: '/carrinho/adicionar/' + id + '/' + quantidade + '/' + numero,
                    context: this,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function () {
                        $.get('/atualizar-quantidade-carrinho', function (data) {
                            // Atualize a quantidade na página
                            if (data && data.quantidadeCarrinho !== undefined) {
                                $('#quantidade-carrinho').text(data
                                    .quantidadeCarrinho);
                            }
                        });
                        $('#exampleModalCenter').modal('hide');

                        carregarCarrinho();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
        }
    });
}