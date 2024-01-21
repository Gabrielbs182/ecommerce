<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class ClienteService
{

    public function salvarUsuario(Usuario $usuario)
    {
        try {
            //testando se o usuario ja tem cadastro
            $dbUsuario = Usuario::where("email", $usuario->email)->first();
            if ($dbUsuario) {
                return ["status" => 'err', 'message' => 'Email já cadastrado.'];
            }
            //iniciando transação
            DB::beginTransaction();
            $usuario->save();
            //finalizando
            DB::commit();
            event(new Registered($usuario));
            Auth::login($usuario);
            return ["status" => 'ok', 'message' => 'Usuario cadastrado com sucesso, um email de verificação de email foi enviado.'];
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'ClienteService.salvarUsuario', 'message' => $e->getMessage()]);
            DB::rollback();
            return ["status" => 'err', 'message' => 'Não pode cadastrar um usuario'];
        }
    }

    public function ativarUsuario(Usuario $usuario)
    {
        try {
            $usuario->ativo = 1;
            DB::beginTransaction();
            $usuario->save();
            DB::commit();
            event(new Registered($usuario));
            Auth::login($usuario);
            return ["status" => 'ok', 'message' => 'Usuario cadastrado com sucesso, um email de verificação de email foi enviado.'];
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'ClienteService.salvarUsuario', 'message' => $e->getMessage()]);
            DB::rollback();
            return ["status" => 'err', 'message' => 'Não pode cadastrar um usuario'];
        }
    }
}
