@extends('layouts.app')

@section('content')
    <script src="{{asset('javascript.js')}}"></script>
    <form action="PayController.php" name="formCard" id="formCard" method="post">
        <div class="col-6 m-auto">
            <input type="text" name="publicKey" id="publicKey">
            <input type="text" name="encriptedCard" id="encriptedCard">
            <input type="text" class="form-control" name="cardNumber" id="cardNumber" maxlength="16"
                placeholder="Número Cartão">
            <input type="text" class="form-control" name="cardHolder" id="cardHolder" placeholder="Nome no Cartão">
            <input type="text" class="form-control" name="cardMonth" id="cardMonth" maxlength="2"
                placeholder="Mês do Cartão">
            <input type="text" class="form-control" name="cardYear" id="cardYear" maxlength="4"
                placeholder="Ano do Cartão">
            <input type="text" class="form-control" name="cardCCV" id="cardCCV" maxlength="4"
                placeholder="Ano do Cartão">
            <input type="Submit" class="btn btn-primary" value="Pagar">
        </div>
    </form>
@endsection
