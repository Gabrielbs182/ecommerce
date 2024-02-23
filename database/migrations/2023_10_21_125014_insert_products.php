<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Cor;
use App\Models\Tamanho;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class InsertProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Categorias
        $cat = new \App\Models\Categoria(['categoria' => 'Geral']);
        $cat->save();

        // Admin
        $pass = env('ADEMIR');
        $pass = bcrypt($pass);
        $user = new Usuario(['email' => 'contato@urbeatstore.com.br', 'cpf' => '12345678912', 'password' => $pass, 'nome' => 'Administrador Michely', 'isAdmin' => 1, 'ativo' => 1, 'telefone' => '27996658538']);
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
