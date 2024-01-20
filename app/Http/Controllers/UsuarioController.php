<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $request->session()->flash("ok", "Senha resetada com sucesso.");

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
        
            ? 
            
            redirect()->route('logar')->with('status', __($status))
            : redirect()->route('logar')
            ->withErrors(['email' => __($status)]);
    }

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
                if (Auth::user()->ativo == 1 && Auth::user()->email_verified_at != null) {
                    return redirect()->route("home");
                } else if (Auth::user()->email_verified_at == null) {
                    $request->session()->flash("err", "Seu email ainda não foi verificado, para acessar todas as funções verifique o seu email.");
                    return redirect()->route("home");
                } else {
                    session()->forget('cart');
                    Auth::logout();
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
