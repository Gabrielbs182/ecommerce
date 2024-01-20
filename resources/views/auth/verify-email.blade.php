@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
@endsection

@section('content')
    <div class="d-flex justify-content-center">
        <div class="card w-50">
            <div class="card">
                <h5 class="card-header">Verificação de email pendente.</h5>
                <div class="card-body">
                    <p class="card-text">Olá enquanto você não verificar o seu email, não é possivel acessar essa função no sistema, favor
                        verificar o email.</p>
                    <div>
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                {{ __('Reenviar verificação de email.') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
