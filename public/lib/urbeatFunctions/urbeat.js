function buscarProduto(id) {
    var asset = $('#asset').val();
    $('#loadingSpinner').show();
    $.getJSON('/produto/buscar/' + id, function (produto) {
        var foto = '<img class="w-100 h-100" src="' + asset +
            '/' + produto[0].id + '.png" alt="Image">';
        var foto2 = '<img class="w-100 h-100" src="' + asset +
            '/' + produto[0].id + '_1.png" alt="Image">';
        var foto3 = '<img class="w-100 h-100" src="' + asset +
            '/' + produto[0].id + '_2.png" alt="Image">';
        var foto4 = '<img class="w-100 h-100" src="' + asset +
            '/' + produto[0].id + '_3.png" alt="Image">';
        var foto5 = '<img class="w-100 h-100" src="' + asset +
            '/' + produto[0].id + '_4.png" alt="Image">';
        $('#primeiroCarrosel').empty();
        $('#descricaoProdModal').empty();
        $('#segundoCarrosel').empty();
        $('#terceiroCarrosel').empty();
        $('#quartoCarrosel').empty();
        $('#quintoCarrosel').empty();
        $('#nomeProdModal').empty();
        $('#valorProdModal').empty();
        $('#comprar').empty();
        $('#comprarButton').empty();
        $('#idProduto').empty();
        var descricaoHtml = produto[0].descricao || ''; // Caso a descrição seja nula ou undefined, assume uma string vazia
        descricaoHtml = descricaoHtml.replace(/\n/g, '<br>');
        $('#descricaoProdModal').append(descricaoHtml);
        $('#primeiroCarrosel').append(foto);
        $('#segundoCarrosel').append(foto2);
        $('#terceiroCarrosel').append(foto3);
        $('#quartoCarrosel').append(foto4);
        $('#quintoCarrosel').append(foto5);
        $('#nomeProdModal').append(produto[0].nome);
        var valComDesconto = produto[0].valor - (produto[0].valor * (produto[0].desconto / 100));
        console.log(valComDesconto);
        var val = String(valComDesconto).replace('.', ',');
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
            $('#loadingSpinner').hide();
        });
    });
}

function obterDadosCEP(cep) {
    // Montar a URL com o CEP fornecido
    $('#loadingSpinner').show();
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

            // Aqui você pode fazer o que quiser com os dados, por exemplo, preencher um formulário
        })
        .catch(error => {
            console.error('Erro:', error);
        });
};

function calcularFrete() {
    // Obtenha o valor do CEP
    var cepValue = document.getElementById("cep").value;
    var frete299 = document.getElementById("frete299").value;
    var freteFixo = document.getElementById("freteFixo").value;
    var motoboy = document.getElementById("motoboy").value;
    var motoboyVix = document.getElementById("motoboyVix").value;
    var motoboySerra = document.getElementById("motoboySerra").value;
    var motoboyCariacica = document.getElementById("motoboyCariacica").value;
    var motoboyVV = document.getElementById("motoboyVV").value;

    var teste = obterDadosCEP(cepValue);

    console.log(teste);

    // Verifique se o CEP é válido antes de fazer a requisição
    if (validarCEP(cepValue)) {
        $('#loadingSpinner').show();
        console.log('Cep é valido');
        // Faça a requisição para calcular o frete
        $.getJSON('/melhorenvio/calcularFrete/' + cepValue, function (frete) {

            document.getElementById("envios").innerHTML = '';

            var paragrafo = document.getElementById("totalCarrinho").innerText;
            var total = parseFloat(paragrafo.replace(',', '.'));

            console.log(total);

            linhas =
                '<div class="row w-100 mt-3 ml-1">' +
                '<div class="col-sm-3">' +
                '<legend class="col-form-label pt-0">A domicílio:</legend>' +
                '</div>' +
                '<div class="col-sm-9">';

            cepInteiro = parseInt(cepValue);

            if (cepInteiro >= 10000000 && cepInteiro <= 39999999) {
                if (motoboy == 2) {
                    if (cepInteiro >= 29140001 && cepInteiro <= 29159999 && motoboyCariacica > 0) {
                        linha = montarLinhaEnvioMotoboy('motoboyCariacica', motoboyCariacica);
                        console.log(motoboyCariacica);
                        linhas += linha;
                    } else if (cepInteiro >= 29160001 && cepInteiro <= 29184999 && motoboySerra > 0) {
                        console.log("Serra.");
                        linha = montarLinhaEnvioMotoboy('motoboySerra', motoboySerra);
                        linhas += linha;
                    } else if (cepInteiro >= 29000001 && cepInteiro <= 29099999 && motoboyVix > 0) {
                        console.log("Vitória.");
                        linha = montarLinhaEnvioMotoboy('motoboyVix', motoboyVix);
                        linhas += linha;
                    } else if (cepInteiro >= 29100001 && cepInteiro <= 29129999 && motoboyVV > 0) {
                        console.log("Vila velha.");
                        linha = montarLinhaEnvioMotoboy('motoboyVV', motoboyVV);
                        linhas += linha;
                    }
                }
            }

            var check = 0;

            for (var transportadora of frete) {
                if ('error' in transportadora) {
                    console.log(transportadora.error);
                } else {
                    if (total >= 299 && frete299 == 2 && cepInteiro >= 01000000 && cepInteiro <= 39999999) {
                        if (check == 0 && !(cepInteiro >= 01000000 && cepInteiro <= 39999999)) {
                            linha = montarLinhaEnvio(transportadora, 2);
                            linhas += linha;
                            check = 1;
                        } else {
                            linha = montarLinhaEnvio(transportadora, 3);
                            linhas += linha;
                        }
                    } else if (freteFixo == 2 && cepInteiro >= 01000000 && cepInteiro <= 39999999) {
                        if (check == 0 && !(cepInteiro >= 01000000 && cepInteiro <= 39999999)) {
                            linha = montarLinhaEnvio(transportadora, 4);
                            linhas += linha;
                            check = 1;
                        } else {
                            linha = montarLinhaEnvio(transportadora, 5);
                            linhas += linha;
                        }
                    } else {
                        if (check == 0 && !(cepInteiro >= 01000000 && cepInteiro <= 39999999)) {
                            linha = montarLinhaEnvio(transportadora, 0);
                            linhas += linha;
                            check = 1;
                        } else {
                            linha = montarLinhaEnvio(transportadora, 1);
                            linhas += linha;
                        }
                    }
                }
            }

            linhas +=
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="row w-100">' +
                '<div class="col-sm-12 mt-3">' +
                '<button type="submit" class="btn btn-primary float-right">Finalizar Compra</button>' +
                '</form>' +
                '</div>' +
                '</div>'
                ;

            document.getElementById("envios").innerHTML = linhas;
            $('#loadingSpinner').hide();
        });
    } else {
        // Exibir mensagem de erro para CEP inválido
        $("#cep-error-message").text("CEP inválido");
        console.log('Cep é invalido');
        // Adicionar classe para destacar o campo em vermelho
        $("#cep-group").addClass("has-error");
        $('#loadingBackground').hide(); // Esconde o fundo escurecido
        $('#loadingSpinner').hide();
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

function montarLinhaEnvioMotoboy(motoboy, valor) {


    linha =
        '<div class="form-check">' +
        '<input class="form-check-input" type="radio" name="frete"' + 'id="' + motoboy + '1" value="' +
        motoboy + '/Motoboy/' + valor + '" checked>' +
        '<label class="form-check-label" for="' + motoboy + '1">Motoboy - R$ ' + valor +
        ' - Prazo 1 dia.' +
        '</label>' +
        '</div>';

    return linha;
}

function montarLinhaEnvio(transportadora, check) {

    //check = 0 - Frete normal checkado
    //check = 1 - Frete normal
    //check = 2 - Frete Gratis checkado
    //check = 3 - Frete Gratis 
    //check = 4 - Frete Fixo checkado 
    //check = 5 - Frete Fixo  

    var valorFormatado = (transportadora.price - transportadora.discount).toFixed(2).replace('.', ',');

    if (check == 0) {
        linha =
            '<div class="form-check">' +
            '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
            transportadora.id + '/' + transportadora.name + '/' + (transportadora.price - transportadora.discount).toFixed(2) + '" checked>' +
            '<label class="form-check-label" for="' + transportadora.id + '">' +
            transportadora.company.name + ' - ' + transportadora.name + ' R$ ' + valorFormatado +
            ' - Prazo ' + transportadora.delivery_time + ' dias.' +
            '</label>' +
            '</div>';
    } else if (check == 3) {
        linha =
            '<div class="form-check">' +
            '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
            transportadora.id + '/' + transportadora.name + '/' + (0).toFixed(2) + '">' +
            '<label class="form-check-label" for="' + transportadora.id + '">' +
            transportadora.company.name + ' - ' + transportadora.name + ' Grátis - Prazo ' + transportadora.delivery_time + ' dias.' +
            '</label>' +
            '</div>';
    } else if (check == 2) {
        linha =
            '<div class="form-check">' +
            '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
            transportadora.id + '/' + transportadora.name + '/' + (0).toFixed(2) + '" checked>' +
            '<label class="form-check-label" for="' + transportadora.id + '">' +
            transportadora.company.name + ' - ' + transportadora.name + ' Grátis - Prazo ' + transportadora.delivery_time + ' dias.' +
            '</label>' +
            '</div>';
    } else if (check == 1) {
        linha =
            '<div class="form-check">' +
            '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
            transportadora.id + '/' + transportadora.name + '/' + (transportadora.price - transportadora.discount).toFixed(2) + '">' +
            '<label class="form-check-label" for="' + transportadora.id + '">' +
            transportadora.company.name + ' - ' + transportadora.name + ' R$ ' + valorFormatado +
            ' - Prazo ' + transportadora.delivery_time + ' dias.' +
            '</label>' +
            '</div>';
    } else if (check == 4) {
        linha =
            '<div class="form-check">' +
            '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
            transportadora.id + '/' + transportadora.name + '/' + (14.99).toFixed(2) + '"checked>' +
            '<label class="form-check-label" for="' + transportadora.id + '">' +
            transportadora.company.name + ' - ' + transportadora.name + ' - R$  14,99 (Frete Fixo) - Prazo ' + transportadora.delivery_time + ' dias.' +
            '</label>' +
            '</div>';

    } else if (check == 5) {
        linha =
            '<div class="form-check">' +
            '<input class="form-check-input" type="radio" name="frete"' + 'id="' + transportadora.id + '" value="' +
            transportadora.id + '/' + transportadora.name + '/' + (14.99).toFixed(2) + '">' +
            '<label class="form-check-label" for="' + transportadora.id + '">' +
            transportadora.company.name + ' - ' + transportadora.name + ' - R$  14,99 (Frete Fixo) - Prazo ' + transportadora.delivery_time + ' dias.' +
            '</label>' +
            '</div>';
    }

    return linha;
}

$(document).ready(function () {
    $('#cupomTexto').on('input', function () {
        this.value = this.value.toUpperCase();
    });
});

function buscarCupom() {
    codigo = $("#cupomTexto").val();
    // $('#loadingSpinner').show();
    // var numero = $('#tamanhoModal').val()
    // var idProduto = $('#idProduto').val()
    $.getJSON('/buscarCupom/' + codigo, function (cupom) {
        // $('#estoque').val(estoque[0].quantidade);
        // $('#loadingSpinner').hide();
        if (!cupom) {
            var mensagem = '<p class="text-danger">Cupom inválido !</p>';
            $('#mensagem').empty();
            $('#mensagem').append(mensagem);
        } else {
            if (cupom[0].ativo == 1) {
                if (cupom[0].tipo == 0) {
                    // Se o frete é grátis
                    $('#cupom').val(cupom[0].id);
                    var gratis = "Grátis"
                    $('#valorFreteTexto').text(gratis);
                    var subtotal = $("#subtotal").text();
                    $('#total').text(subtotal);
                    var mensagem = '<p class="text-success">Cupom aplicado !</p>';
                    $('#mensagem').empty();
                    $('#mensagem').append(mensagem);
                    $('#cupomButton').prop('disabled', true);
                } else {

                    $('#cupom').val(cupom[0].id);

                    // Pegar o texto do elemento e remover o símbolo de R$ e espaços
                    var textoSubtotal = $('#subtotal').text().replace('R$ ', '').replace(',', '.');

                    // Pegar o texto do elemento e remover o símbolo de R$ e espaços
                    var textoFrete = $('#valorFreteTexto').text().replace('R$ ', '').replace(',', '.').trim();

                    // Verificar se o frete é "Grátis"
                    var freteGratis = textoFrete == 'Grátis';

                    // Converter para float
                    var subtotal = parseFloat(textoSubtotal);
                    var vlrFrete = freteGratis ? 0 : parseFloat(textoFrete);

                    // Calcular o novo valor (85 - 10%)
                    var novoValor = subtotal - (subtotal * (cupom[0].desconto / 100));
                    var novoTotal = freteGratis ? novoValor : novoValor + vlrFrete;

                    // Formatar para o padrão brasileiro
                    var formatoBrasileiro = {
                        style: 'currency',
                        currency: 'BRL'
                    };

                    var novoValorFormatado = novoValor.toLocaleString('pt-BR', formatoBrasileiro);
                    var novoTotalFormatado = novoTotal.toLocaleString('pt-BR', formatoBrasileiro);

                    // Atualizar o conteúdo do elemento com o novo valor
                    $('#subtotal').text(novoValorFormatado);
                    $('#total').text(novoTotalFormatado);
                    var mensagem = '<p class="text-success">Cupom aplicado !</p>';
                    $('#mensagem').empty();
                    $('#mensagem').append(mensagem);
                    $('#cupomButton').prop('disabled', true);
                }
            } else {
                var mensagem = '<p class="text-danger">Cupom inválido !</p>';
                $('#mensagem').empty();
                $('#mensagem').append(mensagem);
            }
        }
    });
};

function buscarEstoque() {
    console.log('buscando estoque.');
    $('#loadingSpinner').show();
    var numero = $('#tamanhoModal').val()
    var idProduto = $('#idProduto').val()
    console.log('Produto = ' + idProduto);
    console.log('Numero = ' + numero);
    $.getJSON('/estoqueNumero/' + idProduto + '/' + numero, function (estoque) {
        $('#estoque').val(estoque[0].quantidade);
        console.log('quantidade = ' + estoque[0].quantidade);
        $('#loadingSpinner').hide();
    });
};

function removerProdutoCarrinho(id) {
    $('#loadingSpinner').show();
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'POST',
        url: '/' + id + '/excluirCarrinho',
        context: this,
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function () {
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
                $('#loadingSpinner').hide();
            });
        },
        error: function (error) {
            console.log(error);
            $('#loadingSpinner').hide();
        }
    });

}

function carregarCarrinho() {
    $('#loadingSpinner').show();

    $.getJSON('/buscarCarrinho', function (data) {

        // O objeto retornado é um array
        var total = 0;
        var indice = 0;
        $('#modalCarrinhoLista').empty();

        // Verificar se data é um objeto
        if (typeof data === 'object' && data !== null) {
            // Iterar sobre as propriedades do objeto
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    var item = data[key];
                    var asset = $('#asset').val();
                    if (item.desconto) {
                        valor = (item.valor - (item.valor * (item.desconto / 100)));
                    } else {
                        valor = item.valor;
                    }
                    valorUnitario = String(valor).replace('.', ',');
                    valorTotal = String(valor * item.quantidade).replace('.', ',');
                    total += valor * item.quantidade;
                    var itemCarrinho =
                        '<tr>' +
                        '<td class="w-20 align-middle text-center">' +
                        '<img src="' + asset +
                        '/' + item.id +
                        '.png")" class="img-fluid img-thumbnail" width="100" height="100" ' +
                        'alt="Produto" >' + item.nome + '/' + item.numero + ' x ' + item.quantidade +
                        '</td>' +
                        '<td class="align-middle text-center"> R$' + valorUnitario + '</td>' +
                        '<td class="align-middle text-center"> R$' + valorTotal +
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
            $('#loadingSpinner').hide();
        } else {
            console.error('O JSON retornado não é um array:', data);
            $('#loadingSpinner').hide();
        }
        $('#totalCarrinho').empty();
        totalFim = String(total).replace('.', ',');
        $('#totalCarrinho').append(totalFim);
        document.getElementById("envios").innerHTML = '';

        linha =
            '</div>' +
            '</div>' +
            '</div>';

        document.getElementById("envios").innerHTML = linha;
    });
    $.get('/atualizar-quantidade-carrinho', function (data) {
        // Atualize a quantidade na página
        if (data && data.quantidadeCarrinho !== undefined) {
            $('#quantidade-carrinho').text(data.quantidadeCarrinho);
        }
        $('#loadingSpinner').hide();
    });
};

function abrirCarrinho() {
    $('#loadingSpinner').show();
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
        $('#loadingSpinner').hide();
    });
};

function buscarEstoqueCarrinho(id, callback) {
    $('#loadingSpinner').show();
    var numero = $('#tamanhoModal').val();
    $.getJSON('/buscarItemCarrinho/' + id + '/' + numero, function (data) {
        callback(data); // Chama o callback com os dados recebidos
        $('#loadingSpinner').hide();
    });
}

function adicionarCarrinho(id) {
    buscarEstoque();
    $('#loadingSpinner').show();
    var token = $('meta[name="csrf-token"]').attr('content');
    var estoque = $('#estoque').val();
    var numero = $('#tamanhoModal').val();

    quantidade = parseInt($('#inputZip').val());

    // Use uma função de callback para obter os dados assíncronos
    buscarEstoqueCarrinho(id, function ($item) {
        if ($item == 1) {
            console.log('Item não existe no carrinho');

        } else {
            quantidade += parseInt($item.quantidade);
        }
        // Restante do seu código...
        console.log(quantidade);
        console.log(estoque);
        if (parseInt(quantidade) > parseInt(estoque)) {
            $('#inputZip').val('');
            var alerta =
                '<div class="alert alert-danger alert-dismissible fade show text-center fixed-top" role="alert">' +
                'Estoque insuficiente, tente uma quantidade menor.' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';
            $('#modalAlert').append(alerta);
            $('#loadingSpinner').hide();
        } else {

            $.getJSON('/produto/buscar/' + id, function (produto) {
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

                        $('html, body').animate({ scrollTop: 0 }, 'slow');

                        $.getJSON('/produto/buscar/' + id, function (produto) {

                            var conteudoPopover = 'Produto adicionado: ' + produto[0].nome + '<br><a href="#" id="mostrarCarrinho" class="btn btn-sm btn-primary float-right mb-1">Mostrar Carrinho</a>';

                            // Adicionar botão ao popover para abrir o carrinho
                            $('#popover').attr('title', 'Produto adicionado ao carrinho');
                            $('#popover').attr('data-content', conteudoPopover);
                            $('#popover').popover('show');

                            // Adicionar manipulador de evento para o botão dentro do popover
                            $('#popover').on('shown.bs.popover', function () {
                                $('#mostrarCarrinho').click(function () {
                                    abrirCarrinho();
                                });
                            });
                        });

                        carregarCarrinho();

                        $('#loadingSpinner').hide();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
        }
    });
}