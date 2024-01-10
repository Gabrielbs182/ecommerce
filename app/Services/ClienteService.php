<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            return ["status" => 'ok', 'message' => 'Usuario cadastrado com sucesso'];
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
            return ["status" => 'ok', 'message' => 'Usuario cadastrado com sucesso'];
        } catch (\Exception $e) {
            Log::error("ERRO", ['file' => 'ClienteService.salvarUsuario', 'message' => $e->getMessage()]);
            DB::rollback();
            return ["status" => 'err', 'message' => 'Não pode cadastrar um usuario'];
        }
    }
}
