<?php

namespace App\Models;

class Estoque extends RModel
{
    protected $table = "estoque";
    protected $fillable = ['quantidade', 'numero', 'produto_id'];
}