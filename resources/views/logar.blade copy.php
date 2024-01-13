@extends('layouts.app')

@section('scriptjs')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <script src="{{ asset('vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('vendor/countdowntime/countdowntime.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
@endsection
@section('head')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
@endsection


@section('content')
    <div class="container">
        <div class="row login">
            <div class="col-12 justify-content-center text-center">
                <div class="container-login100">
                    <div class="wrap-login100">
                        <form class="login100-form validate-form">
                            <span class="login100-form-title p-b-26">
                                Welcome
                            </span>
                            <span class="login100-form-title p-b-48">
                                <i class="zmdi zmdi-font"></i>
                            </span>

                            <div class="wrap-input100 validate-input" data-validate="Valid email is: a@b.c">
                                <input class="input100" type="text" name="email">
                                <span class="focus-input100" data-placeholder="Email"></span>
                            </div>

                            <div class="wrap-input100 validate-input" data-validate="Enter password">
                                <span class="btn-show-pass">
                                    <i class="zmdi zmdi-eye"></i>
                                </span>
                                <input class="input100" type="password" name="pass">
                                <span class="focus-input100" data-placeholder="Password"></span>
                            </div>

                            <div class="container-login100-form-btn">
                                <div class="wrap-login100-form-btn">
                                    <div class="login100-form-bgbtn"></div>
                                    <button class="login100-form-btn">
                                        Login
                                    </button>
                                </div>
                            </div>

                            <div class="text-center p-t-115">
                                <span class="txt1">
                                    Não tem uma conta ?
                                </span>

                                <a class="txt2" href="#">
                                    Registre-se
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- <h2 class="mb-3">Logar</h2>
                <form action="{{ route('logar') }}" method="post">
                    @csrf
                    <div class="form-group text-center">
                        E-mail:
                        <input type="email" name="email" class="form-control" id="email">
                    </div>

                    <div class="form-group">
                        Senha:
                        <input type="password" name="password" class="form-control">
                    </div>

                    <input type="submit" value="logar" class="btn btn-lg btn-primary">
                </form> --}}
            </div>
        </div>
    </div>
@endsection
