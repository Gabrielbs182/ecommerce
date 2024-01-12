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

        // Produtos
        $prod = new \App\Models\Produto(['nome' => 'Rasteirinha Luna', 'valor' => 85, 'foto' => 'images/produto1.png', 'descricao' => '', 'peso' => 200, 'altura' => 15, 'largura' => 5, 'comprimento' => 10, 'categoria_id' => $cat->id]);
        $prod->save();
        $prod = new \App\Models\Produto(['nome' => 'Papete Tifany', 'valor' => 210, 'foto' => 'images/produto2.png', 'descricao' => '', 'peso' => 250, 'altura' => 12, 'largura' => 3, 'comprimento' => 8, 'categoria_id' => $cat->id]);
        $prod->save();
        $prod = new \App\Models\Produto(['nome' => 'Flat Carol', 'valor' => 185, 'foto' => 'images/produto3.png', 'descricao' => '', 'peso' => 240, 'altura' => 11, 'largura' => 6, 'comprimento' => 7, 'categoria_id' => $cat->id]);
        $prod->save();
        $prod = new \App\Models\Produto(['nome' => "Nike Dunk Ben Jerry's", 'valor' => 185.5, 'foto' => 'images/produto4.png', 'descricao' => '', 'peso' => 300, 'altura' => 16, 'largura' => 5, 'comprimento' => 11, 'categoria_id' => $cat->id]);
        $prod->save();
        $prod = new \App\Models\Produto(['nome' => 'Nike Dunk Metallic Gold', 'valor' => 187.85, 'foto' => 'images/produto5.png', 'descricao' => '', 'peso' => 400, 'altura' => 34, 'largura' => 7, 'comprimento' => 12, 'categoria_id' => $cat->id]);
        $prod->save();
        $prod = new \App\Models\Produto(['nome' => 'Nike Air Force Branco', 'valor' => 165, 'foto' => 'images/produto6.png', 'descricao' => '', 'peso' => 340, 'altura' => 16, 'largura' => 9, 'comprimento' => 12, 'categoria_id' => $cat->id]);
        $prod->save();



        // Estoque
        $produtos = Produto::all();

        foreach ($produtos as $key => $p) {
            for ($i = 29; $i <= 39; $i++) {
                $estoque = new Estoque([
                    'quantidade' => 0,
                    'numero' => $i,
                    'produto_id' => $p->id
                ]);
                $estoque->save();
            }
        }

        // Admin
        $pass = env('ADEMIR');
        $pass = bcrypt($pass);
        $user = new Usuario(['email' => 'urbeat_adm@adm.com', 'cpf' => '12345678912', 'password' => $pass, 'nome' => 'Administrador Administrador', 'isAdmin' => 1, 'ativo' => 1, 'telefone' => '27999999999']);
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
