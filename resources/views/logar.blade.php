@extends('layouts.app')

@section('scriptjs')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection


@section('content')
    <div class="container">
        <div class="row login">
            <div class="col-4">
            </div>
            <div class="col-4 justify-content-center text-center">
                <h2 class="mb-3">Logar</h2>
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
                </form>
            </div>
            <div class="col-4">
            </div>
        </div>
    </div>
@endsection
