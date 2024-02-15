@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
@endsection

@section('content')
    <input type="hidden" value="{{ asset('images') }}" id="asset">

    <div class="container">
        <div class="table-wrapper table-responsive">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <h2>Políticas<b> Urbeat Store</b></h2>
                    </div>
                </div>
            </div>
            <div>
                <h1 class="mb-2">POLÍTICA DE TROCA — URBEAT STORE</h1>

                <p><b>
                        Chegou a sua mercadoria e você deseja solicitar a troca ou até mesmo nos devolver? Solicitamos que
                        leia
                        com bastante atenção os itens abaixo, que irão auxiliar nesse processo.</b>
                </p>
                <ul>
                    <li>
                        <p>1) Para realizar uma troca ou devolução, você precisa fazer a solicitação em até 7 (sete) dias
                            corridos após o recebimento do produto. Após o prazo de 7 dias não haverá troca ou devolução.
                        </p>
                    </li>
                    <li>
                        <p>2) A troca ou devolução só será bem sucedida se o produto estiver intacto, como enviado. Sem
                            avarias, marcas de uso e odores. E claro, não tem troca ou devolução sem a etiqueta fixada nas
                            peças e a caixa original do produto. Caso a peça retornar e não cumprir esses requisitos, a
                            mesma não será aceita e a peça será devolvida ao cliente!</p>
                    </li>
                    <li>
                        <p>3) A troca ou devolução deverá ser solicitada através do e-mail: <a
                                href="mailto:contato@urbeatstore.com.br">contato@urbeatstore.com.br</a> ou entrando em
                            contato com a nossa equipe de atendimento
                            no whatsapp através deste link <a href="https://w.app/X6vkaJ">Urbeat Whatsapp</a> e somente por
                            estes 2 meios de comunicação, informando o número do pedido e motivo da troca ou devolução. O
                            prazo para resposta é de
                            até 48 horas úteis.</p>
                    </li>
                    <li>
                        <p>4) Se constatado algum defeito ou engano no pedido entregue, a solicitação de troca deve ser
                            feita também no prazo de 7 dias corridos após o recebimento. Para agilizar o processo nos envie
                            foto e explicação do ocorrido.</p>
                    </li>
                    <li>
                        <p>5) Não realizamos trocas de produtos de promoção ou da categoria Outlet.</p>
                    </li>
                    <li>
                        <p>6) Não realizamos trocas de peças brancas.</p>
                    </li>
                    <li>
                        <p>7) Nos casos de troca, o primeiro frete é por conta da loja. Em caso de uma segunda troca, a
                            responsabilidade do frete é inteiramente por conta do cliente (frete de ida e volta). Para
                            devoluções, o custo de frete é totalmente por conta da loja.</p>
                    </li>
                    <li>
                        <p>8) Nos casos de devolução, o valor será estornado conforme o meio de pagamento escolhido pelo
                            cliente no ato da compra.</p>
                    </li>
                </ul>
                </p>
            </div>
        </div>
    </div>
@endsection
