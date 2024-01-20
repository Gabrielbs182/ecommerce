@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
@endsection

@section('content')
    <div class="d-flex justify-content-center">
        <div class="card w-50">
            <div class="card">
                <h5 class="card-header">Recuperação de senha.</h5>
                <div class="card-body">

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <p class="card-text">Esqueceu sua senha ? Sem problemas, nos informe o seu email e vamos enviar
                            um link para recupera-la</p>
                        <div class="mt-4">
                            <input type="email" class="form-control" name="email" id="email"
                                placeholder="Digite seu email" required autofocus />
                        </div>



                        <div class="mt-4">
                            <input type="submit" class="btn btn-primary float-right" value="{{ __('Recuperar senha.') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
