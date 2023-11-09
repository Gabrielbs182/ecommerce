<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Endereco;
use App\Models\Estado;
use App\Services\ClienteService;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    //

    public function cadastrar(Request $request)
    {
        $cidades = Cidade::all();
        $estados = Estado::all();
        $data = [];
        $data["cidades"] = $cidades;
        $data["estados"] = $estados;
        return view("cadastrar", $data);
    }

    public function cadastrarCliente(Request $request)
    {

        //Pegar todos os valores do formulario
        $values = $request->all();
        $usuario = new Usuario($values);
        $usuario->email = $request->input('email');
        $senha = $request->input("password");
        $usuario->password = HASH::make($senha);
        $usuario->isAdmin = 0;

        $endereco = new Endereco($values);
        $endereco->logradouro = $request->input("endereco", "");
        $endereco->logradouro = $request->input("cidade_id", "");


        $clienteService = new ClienteService();
        $result = $clienteService->salvarUsuario($usuario, $endereco);

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
