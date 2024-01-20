@extends('layouts.app')

@section('scriptjs')
    <script src="{{ secure_asset('js/jquery.js') }}"></script>
@endsection

@section('content')
    <div class="d-flex justify-content-center">
        <div class="card w-50">
            <div class="card">
                <h5 class="card-header">Recuperação de senha.</h5>
                <div class="card-body text-center">

                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email Address -->
                        <div>
                            <input type="email" class="form-control" id="readonly" name="readonly" value="{{ $email }}" readonly>
                            <input type="hidden" id="email" name="email" value="{{ $email }}">
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Digite sua senha" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirme sua senha" required>
                        </div>

                        <button type="submit" class="btn btn-primary float-right mt-4">
                            {{ __('Recuperar senha.') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
