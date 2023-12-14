<form action="{{ route('getToken2') }}" method="post">
    @csrf
    <input type="text" name="grant_type" value="authorization_code">
    <input type="text" name="client_id" value="3946">
    <input type="text" name="client_secret" value="REMOVIDO_MELHORENVIO_CLIENT_SECRET">
    <input type="text" name="redirect_uri" value="https://urbeatstore.com.br/melhorenvio/getAuthorization">
    <input type="hidden" name="code" value="{{ env('AUTHORIZATION_CODE') }}">
    <input type="submit">
</form>
