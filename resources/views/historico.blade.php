@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
    <script>
        // $.ajaxSetup({
        //     headers: {
        //         'X-CSRF-TOKEN': "{{ csrf_token() }}"
        //     }
        // });

        // $(function() {
        //     $(".infocompra").on('click', function(){
        //         //ao clicar no link com class .infocompras esta funcção sera executada
        //         let id = $(this).attr("data-value")
        //         $.post('{{ route('compra_detalhes') }}', { idpedido: id }, (result) => {
        //             //Função de callback -- retorno do ajax
        //             $("#conteudopedido").html(result)
        //         })
        //     })
        // })
    </script>
@endsection

@section('content')
    <h3>Historico</h3>

    <table class="table">
        <thead>
            <tr>
                <th>Data da compra</th>
                <th>Situação</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if (isset($lista))
                @foreach ($lista as $p)
                    <tr>
                        <td>{{ $p->datapedido }}</td>
                        <td>{{ $p->status }}</td>
                        <td>
                            <a type="button" href="" data-value="{{ $p->id }}" class="btn btn-primary infocompra"
                                data-bs-toggle="modal" data-bs-target="#modalcompra">
                                <i class="fas fa-shopping-basket"></i>
                            </a>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="modal fade" id="modalcompra">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalcompraLabel">Detalhes da Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="conteudopedido">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
