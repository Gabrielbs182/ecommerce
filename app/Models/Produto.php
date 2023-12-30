<?php

namespace App\Models;

class Produto extends RModel
{
    protected $table = "produtos";

    protected $fillable = ['nome', 'descricao', 'categoria_id', 'valor','peso','altura','largura','comprimento'];

}
