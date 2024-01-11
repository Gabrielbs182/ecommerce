<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Services\ClienteService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    //

    public function cadastrar(Request $request)
    {
        $data = [];
        return view("cadastrar", $data);
    }

    public function cadastrarCliente(Request $request)
    {

        //Pegar todos os valores do formulario
        $usuarioGenerico = Usuario::where('cpf', $request->input('cpf'))->where('email',  $request->input('email'))->where('ativo', 0)->first();
        if ($usuarioGenerico) {
            $senha = $request->input("password");
            $usuarioGenerico->password = HASH::make($senha);

            $clienteService = new ClienteService();
            $result = $clienteService->ativarUsuario($usuarioGenerico);

            $message = $result["message"];
            $status = $result["status"];

            $request->session()->flash($status, $message);

            if ($status == 'err') {
                return redirect()->route('cadastrar');
            } else {
                Auth::login($usuarioGenerico);

                $carrinho = session('cart', []);
                if (count($carrinho) > 0) {
                    if (Auth::check()) {
                        return redirect()->route('ver_carrinho');
                    }
                } else {
                    return redirect()->route('home');
                }
            }
        } else {
            $values = $request->all();
            $usuario = new Usuario($values);
            $usuario->email = $request->input('email');
            $senha = $request->input("password");
            $nome = $request->input("nome") . " " . $request->input("sobrenome");
            $usuario->password = HASH::make($senha);
            $usuario->nome = $nome;
            $usuario->isAdmin = 0;
            $usuario->ativo = 1;

            // $endereco = new Endereco($values);
            // $endereco->logradouro = $request->input("endereco", "");
            // $endereco->logradouro = $request->input("cidade_id", "");


            $clienteService = new ClienteService();
            $result = $clienteService->salvarUsuario($usuario);

            $message = $result["message"];
            $status = $result["status"];

            $request->session()->flash($status, $message);

            if ($status == 'err') {
                return redirect()->route('cadastrar');
            } else {
                Auth::login($usuario);

                $carrinho = session('cart', []);
                if (count($carrinho) > 0) {
                    if (Auth::check()) {
                        return redirect()->route('ver_carrinho');
                    }
                } else {
                    return redirect()->route('home');
                }
            }
        }
    }
}
