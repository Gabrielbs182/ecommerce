<?php

namespace App\Models;

class Estoque extends RModel
{
    protected $table = "estoque";
    protected $fillable = ['quantidade','cor', 'numero', 'produto_id'];
}