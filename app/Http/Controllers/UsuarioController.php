<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function logar(Request $request)
    {
        $data = [];

        if ($request->isMethod("POST")) {
            //Se entrar neste IF é porque o usuario clicou no bbotão logar.

            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
            //logar
            if (Auth::attempt($credentials)) {
                if (Auth::user()->ativo == 1) {
                    return redirect()->route("home");
                } else {
                    $request->session()->flash("err", "Usuario inativo, favor realizar o cadastro para ativar o usuario.");
                    return redirect()->route("logar");
                }
            } else {
                $request->session()->flash("err", "Usuario / Senha invalido");
                return redirect()->route("logar");
            }
        }


        return view("logar", $data);
    }

    public function sair()
    {
        Auth::logout();
        session()->forget('cart');
        return redirect()->route("home");
    }
}
